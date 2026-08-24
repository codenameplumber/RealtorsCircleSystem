<?php

namespace App\Console\Commands;

use App\Models\AgencyPartner;
use App\Models\CommissionSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Rich demo dataset for a "fully functional"-looking system.
 *
 * Idempotent by design: every step tops **up** to a target rather than
 * inserting a fixed batch, so running it twice leaves the same end state
 * instead of doubling rows. Nothing is deleted.
 *
 *   - A pool of "showcase" realtors is brought to >=100 approved properties
 *     (photos AND videos) and >=100 commission rows each.
 *   - Every real agency is brought to a target number of connected realtors
 *     (default 500) using lightweight accounts, so the agency network/earnings
 *     dashboards are populated.
 *
 * Bulk inserts in chunks; safe to run against MySQL.
 */
class SeedRichDemoData extends Command
{
    protected $signature = 'demo:seed-rich
        {--showcase=120 : How many realtors get the full 100 properties + 100 commissions}
        {--properties=100 : Target property count per showcase realtor}
        {--commissions=100 : Target commission count per showcase realtor}
        {--connections=500 : Target connected-realtor count per agency}
        {--light-properties=2 : Properties per lightweight agency realtor}';

    protected $description = 'Seed a rich, idempotent demo dataset (properties, videos, commissions, agency connections).';

    /** Public sample MP4s so seeded listings have a real, playable video src. */
    private array $sampleVideos = [
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
    ];

    private array $types = ['Apartment', 'Duplex', 'Bungalow', 'Detached House', 'Semi-Detached', 'Terraced House', 'Land', 'Commercial', 'Penthouse', 'Studio'];
    private array $listingTypes = ['For Sale', 'For Rent', 'For Sale', 'Short Let', 'For Sale'];
    private array $amenitySets = [
        ['Swimming Pool', 'Generator', 'Security/Guard', 'Parking Space', 'Water Supply'],
        ['Generator', 'CCTV', 'Parking Space', 'Air Conditioning', 'Internet/WiFi'],
        ['Solar Power', 'Generator', 'Security/Guard', 'CCTV', 'Parking Space', 'Water Supply'],
        ['Air Conditioning', 'CCTV', 'Intercom', 'Elevator', 'Generator', 'Parking Space'],
    ];

    private int $photoSeed = 900000;

    public function handle(): int
    {
        $showcaseTarget    = (int) $this->option('showcase');
        $propTarget        = (int) $this->option('properties');
        $commTarget        = (int) $this->option('commissions');
        $connectionTarget  = (int) $this->option('connections');
        $lightProps        = (int) $this->option('light-properties');

        $this->passwordHash = Hash::make('Password@123');

        $settings = CommissionSetting::current();
        $this->settings = $settings;

        $agentIds = DB::table('agents_tables')->where('Status', 'active')->pluck('id')->all();
        if (empty($agentIds)) {
            $this->error('No active agents found — run the base seeders first.');
            return self::FAILURE;
        }

        $this->info('Step 1/3 — growing agencies to ' . $connectionTarget . ' connections each…');
        $this->growAgencyConnections($connectionTarget, $lightProps);

        $this->info('Step 2/3 — selecting ' . $showcaseTarget . ' showcase realtors and topping up properties…');
        $showcase = $this->selectShowcaseRealtors($showcaseTarget);
        $this->topUpProperties($showcase, $propTarget);

        $this->info('Step 3/3 — generating commissions for showcase realtors…');
        $this->topUpCommissions($showcase, $commTarget, $agentIds);

        $this->newLine();
        $this->info('✓ Done.');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total realtors', DB::table('users_tables')->count()],
                ['Total properties', DB::table('properties_tables')->count()],
                ['Total commissions', DB::table('commission_tables')->count()],
                ['Agencies at target', DB::table('agency_partners')
                    ->whereRaw('(select count(*) from users_tables u where u.agency_id = agency_partners.id) >= ?', [$connectionTarget])
                    ->count()],
            ]
        );

        return self::SUCCESS;
    }

    // -- Step 1: agency connections -------------------------------------------

    private function growAgencyConnections(int $target, int $lightProps): void
    {
        // Only the real agencies — skip obvious QA/test rows so we don't
        // manufacture 500 accounts behind a throwaway agency.
        $agencies = AgencyPartner::where('status', 'active')
            ->where('company_name', 'not like', '%TEST%')
            ->get();

        $bar = $this->output->createProgressBar($agencies->count());
        $bar->start();

        foreach ($agencies as $agency) {
            $current = DB::table('users_tables')->where('agency_id', $agency->id)->count();
            $need    = max(0, $target - $current);

            if ($need > 0) {
                $this->createLightweightRealtors($agency, $need, $lightProps);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function createLightweightRealtors(AgencyPartner $agency, int $need, int $lightProps): void
    {
        $now      = now()->toDateTimeString();
        $state    = $agency->state ?: 'Lagos';
        $lga      = $agency->lga ?: 'Ikeja';

        // Deterministic, collision-proof identifiers keyed to this agency so a
        // re-run recognises them and does not create duplicates.
        $existing = DB::table('users_tables')
            ->where('Email', 'like', "demo.conn.a{$agency->id}r%@realtorscircle.test")
            ->pluck('Email')->flip();

        $rows = [];
        $made = 0;
        $r    = 0;

        while ($made < $need) {
            $email = "demo.conn.a{$agency->id}r{$r}@realtorscircle.test";
            $r++;

            if ($existing->has($email)) {
                continue; // already created on a prior run
            }

            $rows[] = [
                'First_name'        => 'Partner',
                'Last_name'         => 'Realtor ' . $r,
                'Email'             => $email,
                'Phone'             => '0709' . str_pad((string) ($agency->id * 100000 + $r), 7, '0', STR_PAD_LEFT),
                'email_verified_at' => $now,
                'password'          => $this->passwordHash,
                'Bio'               => "Agency-network realtor with {$agency->company_name}.",
                'Address'           => "{$lga}, {$state}",
                'Specialization'    => 'Residential',
                'Yrs_experience'    => (string) rand(1, 15),
                '2fa'               => 'disabled',
                'Status'            => 'active',
                'State'             => $state,
                'Local_area'        => $lga,
                'Lga'               => $lga,
                'Country'           => 'Nigeria',
                'steps_signup'      => 'complete',
                'kyc_status'        => 'approved',
                'agency_id'         => $agency->id,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
            $made++;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('users_tables')->insert($chunk);
        }

        // A couple of properties each so the agency network shows listings.
        if ($lightProps > 0) {
            $newIds = DB::table('users_tables')
                ->where('agency_id', $agency->id)
                ->where('Email', 'like', "demo.conn.a{$agency->id}r%@realtorscircle.test")
                ->pluck('id', 'id');

            $props = [];
            foreach ($newIds as $uid) {
                // Skip realtors that already have listings (idempotent re-run).
                for ($p = 0; $p < $lightProps; $p++) {
                    $props[] = $this->makePropertyRow($uid, $state, $lga, $p, withVideo: false);
                }
            }
            // Only insert for realtors with zero properties so far.
            $withProps = DB::table('properties_tables')->whereIn('user_id', array_keys($newIds->all()))
                ->distinct()->pluck('user_id')->flip();
            $props = array_values(array_filter($props, fn ($row) => ! $withProps->has($row['user_id'])));

            foreach (array_chunk($props, 500) as $chunk) {
                DB::table('properties_tables')->insert($chunk);
            }
        }
    }

    // -- Step 2: showcase properties ------------------------------------------

    private function selectShowcaseRealtors(int $count): \Illuminate\Support\Collection
    {
        // Prefer genuine, verified test realtors; spread some across agencies so
        // agency earnings populate. Exclude the demo.conn.* lightweight rows.
        return DB::table('users_tables')
            ->where('Status', 'active')
            ->whereNotNull('email_verified_at')
            ->where('Email', 'like', '%@realtorscircle.test')
            ->where('Email', 'not like', 'demo.conn.%')
            ->orderBy('id')
            ->limit($count)
            ->get(['id', 'State', 'Local_area', 'agency_id']);
    }

    private function topUpProperties(\Illuminate\Support\Collection $realtors, int $target): void
    {
        $bar = $this->output->createProgressBar($realtors->count());
        $bar->start();

        foreach ($realtors as $realtor) {
            $current = DB::table('properties_tables')->where('user_id', $realtor->id)->count();
            $need    = max(0, $target - $current);

            if ($need > 0) {
                $state = $realtor->State ?: 'Lagos';
                $lga   = $realtor->Local_area ?: 'Ikeja';
                $rows  = [];
                for ($p = 0; $p < $need; $p++) {
                    $rows[] = $this->makePropertyRow($realtor->id, $state, $lga, $p, withVideo: true);
                }
                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('properties_tables')->insert($chunk);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function makePropertyRow($userId, string $state, string $lga, int $p, bool $withVideo): array
    {
        $now   = now()->toDateTimeString();
        $type  = $this->types[$p % count($this->types)];
        $lt    = $this->listingTypes[$p % count($this->listingTypes)];
        $beds  = ($p % 5) + 1;
        $price = (string) (($type === 'Land' ? 8_000_000 : 15_000_000) + ($p * 750_000) + rand(0, 5_000_000));

        $s1 = $this->photoSeed++;
        $s2 = $this->photoSeed++;
        $s3 = $this->photoSeed++;

        $videos = $withVideo
            ? [$this->sampleVideos[$p % count($this->sampleVideos)]]
            : [];

        return [
            'user_id'         => $userId,
            'title'           => $type === 'Land' ? "{$beds} Plot(s) of Land in {$lga}" : "{$beds} Bedroom {$type} in {$lga}",
            'type'            => $type,
            'description'     => "A well-appointed {$type} located in {$lga}, {$state}. {$lt}. Fitted with modern amenities and excellent access to major roads, schools and markets.",
            'listing_type'    => $lt,
            'street'          => (($p % 40) + 1) . ' Estate Drive',
            'neigborhood'     => $lga,
            'city'            => $state,
            'state'           => $state,
            'localarea'       => $lga,
            'country'         => 'Nigeria',
            'amenities'       => json_encode($this->amenitySets[$p % count($this->amenitySets)]),
            'price'           => $price,
            'photos'          => json_encode([
                "https://picsum.photos/seed/rc{$s1}/1200/800",
                "https://picsum.photos/seed/rc{$s2}/1200/800",
                "https://picsum.photos/seed/rc{$s3}/1200/800",
            ]),
            'videos'          => json_encode($videos),
            'listing_status'  => 'active',
            'approval_status' => 'approved',
            'views'           => (string) rand(5, 1200),
            'created_at'      => now()->subDays(rand(1, 300))->toDateTimeString(),
            'updated_at'      => $now,
        ];
    }

    // -- Step 3: commissions ---------------------------------------------------

    private function topUpCommissions(\Illuminate\Support\Collection $realtors, int $target, array $agentIds): void
    {
        $s = $this->settings;
        $statusWeights = ['paid', 'paid', 'paid', 'paid', 'paid', 'processing', 'processing', 'pending', 'pending', 'disputed'];
        $peerIds = $realtors->pluck('id')->all();

        $bar = $this->output->createProgressBar($realtors->count());
        $bar->start();

        foreach ($realtors as $realtor) {
            $current = DB::table('commission_tables')
                ->where('listing_realtor_id', $realtor->id)
                ->count();
            $need = max(0, $target - $current);

            if ($need > 0) {
                $propIds = DB::table('properties_tables')->where('user_id', $realtor->id)->pluck('id')->all();
                if (empty($propIds)) {
                    $bar->advance();
                    continue;
                }

                $rows = [];
                for ($i = 0; $i < $need; $i++) {
                    $model  = ($i % 3 === 0) ? 1 : 2; // ~1/3 referral deals
                    $total  = rand(2_000_000, 45_000_000);
                    $status = $statusWeights[$i % count($statusWeights)];

                    $agencyId = $realtor->agency_id;
                    $override = 0.0;
                    $platform = 0.0;

                    if ($model === 1) {
                        $listing   = round($total * $s->m1_listing_pct / 100, 2);
                        $referring = round($total * $s->m1_referring_pct / 100, 2);
                        $agentFee  = round($total * $s->m1_agent_pct / 100, 2);
                        $platform  = round($total * $s->m1_platform_pct / 100, 2);
                        $refId     = $this->pickPeer($peerIds, $realtor->id);
                    } else {
                        $listing   = round($total * $s->m2_listing_pct / 100, 2);
                        $referring = 0.0;
                        $agentFee  = round($total * $s->m2_agent_pct / 100, 2);
                        $platform  = round($total * $s->m2_platform_pct / 100, 2);
                        $refId     = null;
                    }

                    if ($agencyId) {
                        $override = round($platform * ($s->agency_override_pct ?? 0) / 100, 2);
                        $platform = round($platform - $override, 2);
                    }

                    $dealDate = now()->subDays(rand(1, 360));

                    $rows[] = [
                        'property_id'              => $propIds[array_rand($propIds)],
                        'listing_realtor_id'       => $realtor->id,
                        'referring_realtor_id'     => $refId,
                        'agent_id'                 => $agentIds[array_rand($agentIds)],
                        'agency_id'                => $agencyId,
                        'agency_override_amount'   => $override,
                        'total_amount'             => $total,
                        'listing_realtor_amount'   => $listing,
                        'referring_realtor_amount' => $referring,
                        'agent_fee'                => $agentFee,
                        'platform_fee'             => $platform,
                        'status'                   => $status,
                        'deal_reference'           => 'DEMO-' . strtoupper(Str::random(10)),
                        'deal_date'                => $dealDate->toDateString(),
                        'notes'                    => 'Seeded demo deal.',
                        'created_at'               => $dealDate->toDateTimeString(),
                        'updated_at'               => $dealDate->toDateTimeString(),
                    ];
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('commission_tables')->insert($chunk);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function pickPeer(array $peerIds, $selfId)
    {
        if (count($peerIds) < 2) {
            return null;
        }
        do {
            $id = $peerIds[array_rand($peerIds)];
        } while ($id === $selfId);

        return $id;
    }

    private string $passwordHash = '';
    private $settings;
}
