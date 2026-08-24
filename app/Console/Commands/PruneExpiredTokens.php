<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Housekeeping for the single-use security tokens introduced during the
 * security audit. None of these are dangerous once expired — validation always
 * checks the expiry — but leaving spent rows around indefinitely is untidy and
 * grows the tables, so a nightly sweep nulls/deletes anything past its window.
 */
class PruneExpiredTokens extends Command
{
    protected $signature = 'tokens:prune-expired';

    protected $description = 'Delete/clear expired password-reset and email-verification tokens across all portals.';

    public function handle(): int
    {
        $now = now();

        // Realtor email-verification tokens (users_tables).
        $users = DB::table('users_tables')
            ->whereNotNull('email_verification_token')
            ->where('email_verification_expires_at', '<', $now)
            ->update([
                'email_verification_token'      => null,
                'email_verification_expires_at' => null,
            ]);

        // Portal password-reset tokens (admin / agent / agency).
        $portalCleared = 0;
        foreach (['admins_tables', 'agents_tables', 'agency_partners'] as $table) {
            $portalCleared += DB::table($table)
                ->whereNotNull('password_reset_token')
                ->where('password_reset_expires_at', '<', $now)
                ->update([
                    'password_reset_token'      => null,
                    'password_reset_expires_at' => null,
                ]);
        }

        // Realtor / API password-reset broker rows (password_reset_tokens has
        // only created_at; honour the configured expiry window).
        $expireMinutes = (int) config('auth.passwords.users.expire', 60);
        $brokerDeleted = DB::table('password_reset_tokens')
            ->where('created_at', '<', $now->copy()->subMinutes($expireMinutes))
            ->delete();

        $this->info("Pruned: {$users} realtor verification, {$portalCleared} portal reset, {$brokerDeleted} broker reset tokens.");

        return self::SUCCESS;
    }
}
