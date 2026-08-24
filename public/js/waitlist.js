// waitlist.js

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("waitlistForm");
    const steps = document.querySelectorAll(".form-step");
    const nextBtns = document.querySelectorAll(".next-step");
    const prevBtns = document.querySelectorAll(".prev-step");
    const progressBar = document.getElementById("formProgress");
    const specializationError = document.getElementById("specializationError");
    const loadingOverlay = document.getElementById("loadingOverlay");
    let currentStep = 0;

    // Show step
    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle("active", i === index);
        });
        updateProgress(index);
    }

    // Update progress bar
    function updateProgress(stepIndex) {
        const progress = ((stepIndex + 1) / steps.length) * 100;
        progressBar.style.width = progress + "%";
    }

    // Validate only inputs inside the current step
    function validateCurrentStep(stepIndex) {
        const currentInputs = steps[stepIndex].querySelectorAll("input, textarea");
        let valid = true;

        currentInputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add("is-invalid");
                valid = false;
            } else {
                input.classList.remove("is-invalid");
                input.classList.add("is-valid");
            }
        });

        return valid;
    }

    // Validate specialization (step 2)
    function validateSpecialization() {
        const checkboxes = document.querySelectorAll("input[name='specialization'], #residential, #commercial, #other-specialization");
        let checked = false;

        checkboxes.forEach(cb => {
            if (cb.checked) checked = true;
        });

        if (!checked) {
            specializationError.style.display = "block";
            return false;
        } else {
            specializationError.style.display = "none";
            return true;
        }
    }

    // Next step button
    nextBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            if (!validateCurrentStep(currentStep)) {
                return;
            }
            currentStep++;
            showStep(currentStep);
        });
    });

    // Previous step button
    prevBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            currentStep--;
            showStep(currentStep);
        });
    });

    // Form submission
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        if (!validateCurrentStep(currentStep)) {
            return;
        }

        if (!validateSpecialization()) {
            return;
        }

        // Show loading
        loadingOverlay.style.display = "flex";

        // Fake delay (simulate server call)
        setTimeout(() => {
            loadingOverlay.style.display = "none";

            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById("successModal"));
            successModal.show();

            // Reset form
            form.reset();
            currentStep = 0;
            steps.forEach(s => s.classList.remove("active"));
            showStep(0);

            // Clear validation classes
            form.querySelectorAll("input, textarea").forEach(el => {
                el.classList.remove("is-valid", "is-invalid");
            });
        }, 2000);
    });

    // Initialize first step
    showStep(currentStep);

    // Show "Other specialization" input if checked
    document.getElementById("other-specialization").addEventListener("change", (e) => {
        const container = document.getElementById("otherInputContainer");
        if (e.target.checked) {
            container.style.display = "block";
        } else {
            container.style.display = "none";
        }
    });
});
