// Countdown Timer
function initCountdown() {
  // Set launch date (30 days from now)
  const launchDate = new Date()
  launchDate.setDate(launchDate.getDate() + 30)

  function updateCountdown() {
    const now = new Date().getTime()
    const distance = launchDate.getTime() - now

    const days = Math.floor(distance / (1000 * 60 * 60 * 24))
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
    const seconds = Math.floor((distance % (1000 * 60)) / 1000)

    document.getElementById("days").textContent = days.toString().padStart(2, "0")
    document.getElementById("hours").textContent = hours.toString().padStart(2, "0")
    document.getElementById("minutes").textContent = minutes.toString().padStart(2, "0")
    document.getElementById("seconds").textContent = seconds.toString().padStart(2, "0")

    if (distance < 0) {
      document.querySelector(".countdown-timer").innerHTML = '<h3 class="text-primary">We\'re Live!</h3>'
    }
  }

  updateCountdown()
  setInterval(updateCountdown, 1000)
}

// Animated Counter for Stats
function animateCounters() {
  const counters = document.querySelectorAll(".stat-number")

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const counter = entry.target
        const target = Number.parseInt(counter.getAttribute("data-target"))
        const increment = target / 100
        let current = 0

        const updateCounter = () => {
          if (current < target) {
            current += increment
            counter.textContent = Math.floor(current).toLocaleString()
            requestAnimationFrame(updateCounter)
          } else {
            counter.textContent = target.toLocaleString()
          }
        }

        updateCounter()
        observer.unobserve(counter)
      }
    })
  })

  counters.forEach((counter) => observer.observe(counter))
}

// Email Signup Form
function initSignupForm() {
  const form = document.getElementById("signupForm")
  const modal = document.getElementById("successModal") // Declare the modal element

  form.addEventListener("submit", (e) => {
    e.preventDefault()

    const email = form.querySelector('input[type="email"]').value

    // Simulate API call
    setTimeout(() => {
      // Store email (in real app, send to backend)
      console.log("Email registered:", email)

      // Show success modal
      new bootstrap.Modal(modal).show() // Use bootstrap.Modal constructor

      // Reset form
      form.reset()
    }, 1000)
  })
}

// Smooth Scrolling for Anchor Links
function initSmoothScrolling() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })
      }
    })
  })
}

// Navbar Scroll Effect
function initNavbarScroll() {
  const navbar = document.querySelector(".navbar")

  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      navbar.style.background = "rgba(255, 255, 255, 0.98)"
      navbar.style.boxShadow = "0 2px 20px rgba(0, 0, 0, 0.1)"
    } else {
      navbar.style.background = "rgba(255, 255, 255, 0.95)"
      navbar.style.boxShadow = "none"
    }
  })
}

// Fade In Animation on Scroll
function initScrollAnimations() {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in")
        }
      })
    },
    {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    },
  )

  // Observe elements that should animate
  document.querySelectorAll(".feature-card, .floating-card, .countdown-timer, .signup-form").forEach((el) => {
    observer.observe(el)
  })
}

// Particle Background Effect
function initParticleBackground() {
  const canvas = document.createElement("canvas")
  const ctx = canvas.getContext("2d")

  canvas.style.position = "fixed"
  canvas.style.top = "0"
  canvas.style.left = "0"
  canvas.style.width = "100%"
  canvas.style.height = "100%"
  canvas.style.pointerEvents = "none"
  canvas.style.zIndex = "1"
  canvas.style.opacity = "0.1"

  document.querySelector(".hero-section").appendChild(canvas)

  function resizeCanvas() {
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight
  }

  const particles = []
  const particleCount = 50

  class Particle {
    constructor() {
      this.x = Math.random() * canvas.width
      this.y = Math.random() * canvas.height
      this.vx = (Math.random() - 0.5) * 0.5
      this.vy = (Math.random() - 0.5) * 0.5
      this.radius = Math.random() * 2 + 1
    }

    update() {
      this.x += this.vx
      this.y += this.vy

      if (this.x < 0 || this.x > canvas.width) this.vx *= -1
      if (this.y < 0 || this.y > canvas.height) this.vy *= -1
    }

    draw() {
      ctx.beginPath()
      ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2)
      ctx.fillStyle = "#10b981"
      ctx.fill()
    }
  }

  function initParticles() {
    for (let i = 0; i < particleCount; i++) {
      particles.push(new Particle())
    }
  }

  function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height)

    particles.forEach((particle) => {
      particle.update()
      particle.draw()
    })

    requestAnimationFrame(animate)
  }

  resizeCanvas()
  initParticles()
  animate()

  window.addEventListener("resize", resizeCanvas)
}

// Initialize all functions when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  initCountdown()
  animateCounters()
  initSignupForm()
  initSmoothScrolling()
  initNavbarScroll()
  initScrollAnimations()
  initParticleBackground()

  // Add fade-in class to initial elements
  setTimeout(() => {
    document.querySelectorAll(".hero-content > *").forEach((el, index) => {
      el.classList.add("fade-in")
      if (index > 0) el.classList.add(`delay-${Math.min(index, 3)}`)
    })
  }, 500)
})

// Handle form validation
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

// Add loading state to signup button
function setButtonLoading(button, loading) {
  if (loading) {
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subscribing...'
    button.disabled = true
  } else {
    button.innerHTML = '<i class="fas fa-bell me-2"></i>Notify Me'
    button.disabled = false
  }
}

// Enhanced form submission with validation
document.getElementById("signupForm").addEventListener("submit", function (e) {
  e.preventDefault()

  const emailInput = this.querySelector('input[type="email"]')
  const submitButton = this.querySelector('button[type="submit"]')
  const email = emailInput.value.trim()

  // Validate email
  if (!validateEmail(email)) {
    emailInput.classList.add("is-invalid")
    return
  }

  emailInput.classList.remove("is-invalid")
  emailInput.classList.add("is-valid")

  // Set loading state
  setButtonLoading(submitButton, true)

  // Simulate API call
  setTimeout(() => {
    setButtonLoading(submitButton, false)

    // Show success modal
    const modal = document.getElementById("successModal") // Declare the modal element
    new bootstrap.Modal(modal).show() // Use bootstrap.Modal constructor

    // Reset form
    this.reset()
    emailInput.classList.remove("is-valid")
  }, 2000)
})
