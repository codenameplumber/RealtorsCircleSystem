<div>
    
<!-- Main -->
  <div class="main-content py-5 mt-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

          <!-- Header -->
          <div class="header-section text-center mb-5">
            <i class="fas fa-users-cog fa-3x text-primary mb-3"></i>
            <h1 class="fw-bold">Join Our Exclusive Waitlist</h1>
            <p class="text-muted">Be among the first to experience the future of property networking.</p>
            <div class="progress-indicator mb-4">
              <div class="progress">
                <div class="progress-bar" id="formProgress"></div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <div class="form-container bg-white p-4 rounded shadow-sm">
            <form id="waitlistForm" wire:submit.prevent="submit" class="needs-validation" novalidate>

              <!-- Step 1 -->
              <div class="form-step active" data-step="1" wire:ignore.self>
                <h4 class="mb-3">Personal Information</h4>
                <div class="row g-3">

                  <div class="col-12">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="fullName" required wire:model="full_name" class="form-control @error('full_name') is-invalid @enderror">
                     @error('full_name')
                       <div class="invalid-feedback ">Please provide your full name.</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" class="form-control" id="phoneNumber" wire:model="phone_number" class="form-control @error('phone_number') is-invalid @enderror" required>
                    @error('phone_number')
                       <div class="invalid-feedback">Please enter your phone number.</div>
                     @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" id="emailAddress" wire:model="email" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                      <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label class="form-label">Home Address *</label>
                    <textarea class="form-control" id="homeAddress" rows="3" required wire:model="home_address" class="form-control @error('home_address') is-invalid @enderror"></textarea>
                    @error('home_address')
                      <div class="invalid-feedback">Please provide your home address.</div>
                    @enderror
                  </div>

                </div>
                
                   <br>
                    <h4 class="mb-3">Professional Experience</h4>
                        <div class="mb-3">
                        <label class="form-label">Years of Experience *</label>
                        <input type="number" class="form-control" wire:model="years_experience"  min="0" max="50" required class="form-control @error('years_experience') is-invalid @enderror">
                        @error('years_experience') 
                            <div class="invalid-feedback">Enter 0-50 years.</div>
                        @enderror
                        </div>

                        <div class="mb-3">
                        <label class="form-label">Areas of Specialization *</label>
                       <select required wire:model="specializations" class="form-select @error('specializations') is-invalid @enderror">
                            <option value="0">Select Specialization</option>
                            <option value="Residential Sales">Residential Sales</option>
                            <option value="Commercial Sales">Commercial Sales</option>
                            <option value="Residential Rentals">Residential Rentals</option>
                            <option value="Commercial Rentals">Commercial Rentals</option>
                            <option value="Land & Plots">Land & Plots</option>
                            <option value="general">General Services</option>
                            <option value="Other">Other</option>
                        </select>
                          @error('specializations') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                 

                <div class="mt-4 d-flex justify-content-between">
                  
                    <button type="submit" wire:loading.target="submit" wire:loading.attr="disabled" class="btn btn-primary w-100">
                        <span wire:loading.remove wire:target="submit">Join Waitlist</span>
                        <span wire:loading wire:target="submit">Processing...</span>
                    </button>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-4 text-center">
        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
        <h3 class="fw-bold">Welcome to Realtors Circle!</h3>
        <p class="text-muted">Thank you for joining our exclusive waitlist.</p>
        <p>You've been successfully added to our launch list. We'll notify you as soon as Realtors Circle goes live!</p>
                    
        <a href="/">
           <button class="btn btn-primary" data-bs-dismiss="modal">Got it!</button>
        </a>
      </div>
    </div>
  </div>



 <script>
    window.addEventListener('waitlist-success', () => {
        var modal = new bootstrap.Modal(document.getElementById('successModal'))
        modal.show();
    })
</script>

</div>
