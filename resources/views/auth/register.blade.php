@extends('layouts.app')

@section('content')
<style>
  /* Modern Color Palette */
  :root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --accent: #06b6d4;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --dark: #1f2937;
    --light: #f8fafc;
    --gray: #6b7280;
    --gray-light: #e5e7eb;
    --white: #ffffff;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
  }

  /* Global Styles */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.6;
    color: var(--dark);
    background: var(--light);
  }

  /* Modern Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInLeft {
    from {
      opacity: 0;
      transform: translateX(-30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes fadeInRight {
    from {
      opacity: 0;
      transform: translateX(30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes float {
    0%, 100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  @keyframes pulse {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }

  @keyframes gradient {
    0% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
    100% {
      background-position: 0% 50%;
    }
  }

  @keyframes shimmer {
    0% {
      background-position: -200% 0;
    }
    100% {
      background-position: 200% 0;
    }
  }

  .animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
  }

  .animate-fade-in-left {
    animation: fadeInLeft 0.8s ease-out;
  }

  .animate-fade-in-right {
    animation: fadeInRight 0.8s ease-out;
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-pulse {
    animation: pulse 2s ease-in-out infinite;
  }

  .animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
  }

  /* Auth Container */
  .auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gradient-primary);
    position: relative;
    overflow: hidden;
    padding: 2rem 0;
  }

  .auth-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
      radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
      radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
  }

  /* Animated Particles */
  .auth-particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
  }

  .particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
  }

  .particle:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
  .particle:nth-child(2) { top: 60%; left: 80%; animation-delay: 2s; }
  .particle:nth-child(3) { top: 80%; left: 20%; animation-delay: 4s; }
  .particle:nth-child(4) { top: 30%; left: 70%; animation-delay: 1s; }
  .particle:nth-child(5) { top: 70%; left: 50%; animation-delay: 3s; }

  /* Auth Card */
  .auth-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 3rem;
    box-shadow: var(--shadow-xl);
    width: 100%;
    max-width: 500px;
    position: relative;
    z-index: 10;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .auth-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-success);
    border-radius: 24px 24px 0 0;
  }

  /* Auth Header */
  .auth-header {
    text-align: center;
    margin-bottom: 2rem;
  }

  .auth-logo {
    width: 80px;
    height: 80px;
    background: var(--gradient-success);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    font-weight: 800;
    font-size: 2rem;
    animation: float 6s ease-in-out infinite;
  }

  .auth-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.5rem;
  }

  .auth-subtitle {
    color: var(--gray);
    font-size: 1rem;
  }

  /* Form Styles */
  .auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .form-group {
    position: relative;
  }

  .form-label {
    display: block;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
  }

  .form-input {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid var(--gray-light);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    position: relative;
  }

  .form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    transform: translateY(-2px);
  }

  .form-input::placeholder {
    color: var(--gray);
  }

  /* Input Icons */
  .input-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 1.25rem;
  }

  .form-group.has-icon .form-input {
    padding-right: 3rem;
  }

  /* Password Strength Indicator */
  .password-strength {
    margin-top: 0.5rem;
    display: flex;
    gap: 0.25rem;
  }

  .strength-bar {
    height: 4px;
    border-radius: 2px;
    flex: 1;
    background: var(--gray-light);
    transition: all 0.3s ease;
  }

  .strength-bar.active {
    background: var(--success);
  }

  .strength-bar.medium {
    background: var(--warning);
  }

  .strength-bar.weak {
    background: var(--danger);
  }

  .strength-text {
    font-size: 0.75rem;
    margin-top: 0.25rem;
    color: var(--gray);
  }

  /* Terms and Conditions */
  .terms-group {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin: 1rem 0;
  }

  .checkbox-input {
    width: 18px;
    height: 18px;
    border: 2px solid var(--gray-light);
    border-radius: 4px;
    position: relative;
    cursor: pointer;
    margin-top: 0.125rem;
    flex-shrink: 0;
  }

  .checkbox-input:checked {
    background: var(--primary);
    border-color: var(--primary);
  }

  .checkbox-input:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
  }

  .terms-text {
    font-size: 0.875rem;
    color: var(--dark);
    line-height: 1.5;
  }

  .terms-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
  }

  .terms-link:hover {
    text-decoration: underline;
  }

  /* Submit Button */
  .submit-btn {
    width: 100%;
    padding: 1rem 2rem;
    background: var(--gradient-success);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
  }

  .submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
  }

  .submit-btn:active {
    transform: translateY(0);
  }

  .submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
  }

  .submit-btn:hover::before {
    left: 100%;
  }

  /* Auth Footer */
  .auth-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-light);
  }

  .auth-footer-text {
    color: var(--gray);
    font-size: 0.875rem;
  }

  .auth-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .auth-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
  }

  /* Error Messages */
  .error-message {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: var(--danger);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    margin-bottom: 1rem;
  }

  .form-error {
    color: var(--danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .form-error::before {
    content: '⚠';
    font-size: 0.875rem;
  }

  /* Success Messages */
  .success-message {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: var(--success);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    margin-bottom: 1rem;
  }

  /* Loading State */
  .submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
  }

  .submit-btn.loading {
    position: relative;
  }

  .submit-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  @keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .auth-container {
      padding: 1rem;
    }

    .auth-card {
      padding: 2rem;
      margin: 1rem;
    }

    .auth-title {
      font-size: 1.75rem;
    }

    .auth-logo {
      width: 60px;
      height: 60px;
      font-size: 1.5rem;
    }

    .form-row {
      grid-template-columns: 1fr;
    }

    .terms-group {
      align-items: flex-start;
    }
  }
</style>

<div class="auth-container">
  <div class="auth-bg"></div>
  
  <!-- Animated Particles -->
  <div class="auth-particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <div class="auth-card animate-fade-in-up">
    <!-- Auth Header -->
    <div class="auth-header">
      <div class="auth-logo">H</div>
      <h1 class="auth-title">Create Account</h1>
      <p class="auth-subtitle">Join Hollyn hosting and start building your website</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
      <div class="error-message">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    @if(session('status'))
      <div class="success-message">
        {{ session('status') }}
      </div>
    @endif

    <!-- Register Form -->
    <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
      @csrf
      
      <!-- Name Field -->
      <div class="form-group has-icon">
        <label class="form-label">Full Name</label>
        <input 
          type="text" 
          name="name" 
          value="{{ old('name') }}"
          class="form-input" 
          placeholder="Enter your full name"
          required 
          autocomplete="name"
        >
        <div class="input-icon">👤</div>
        @error('name')
          <div class="form-error">{{ $message }}</div>
        @enderror
      </div>

      <!-- Email Field -->
      <div class="form-group has-icon">
        <label class="form-label">Email Address</label>
        <input 
          type="email" 
          name="email" 
          value="{{ old('email') }}"
          class="form-input" 
          placeholder="Enter your email"
          required 
          autocomplete="email"
        >
        <div class="input-icon">📧</div>
        @error('email')
          <div class="form-error">{{ $message }}</div>
        @enderror
      </div>

      <!-- Phone Field -->
      <div class="form-group has-icon">
        <label class="form-label">Phone Number (Optional)</label>
        <input 
          type="tel" 
          name="phone" 
          value="{{ old('phone') }}"
          class="form-input" 
          placeholder="Enter your phone number"
          autocomplete="tel"
        >
        <div class="input-icon">📱</div>
        @error('phone')
          <div class="form-error">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password Fields Row -->
      <div class="form-row">
        <!-- Password Field -->
        <div class="form-group has-icon">
          <label class="form-label">Password</label>
          <input 
            type="password" 
            name="password" 
            class="form-input" 
            placeholder="Create password"
            required 
            autocomplete="new-password"
            id="passwordInput"
          >
          <div class="input-icon" id="passwordToggle" style="cursor: pointer;">👁️</div>
          @error('password')
            <div class="form-error">{{ $message }}</div>
          @enderror
        </div>

        <!-- Confirm Password Field -->
        <div class="form-group has-icon">
          <label class="form-label">Confirm Password</label>
          <input 
            type="password" 
            name="password_confirmation" 
            class="form-input" 
            placeholder="Confirm password"
            required 
            autocomplete="new-password"
            id="confirmPasswordInput"
          >
          <div class="input-icon" id="confirmPasswordToggle" style="cursor: pointer;">👁️</div>
        </div>
      </div>

      <!-- Password Strength Indicator -->
      <div id="passwordStrength" style="display: none;">
        <div class="password-strength">
          <div class="strength-bar"></div>
          <div class="strength-bar"></div>
          <div class="strength-bar"></div>
          <div class="strength-bar"></div>
        </div>
        <div class="strength-text" id="strengthText"></div>
      </div>

      <!-- Terms and Conditions -->
      <div class="terms-group">
        <input type="checkbox" name="terms" class="checkbox-input" id="terms" required>
        <label for="terms" class="terms-text">
          I agree to the <a href="#" class="terms-link">Terms of Service</a> and 
          <a href="#" class="terms-link">Privacy Policy</a>
        </label>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="submit-btn" id="submitBtn">
        <span id="btnText">Create Account</span>
      </button>
    </form>

    <!-- Auth Footer -->
    <div class="auth-footer">
      <p class="auth-footer-text">
        Already have an account? 
        <a href="{{ route('login') }}" class="auth-link">Sign in here</a>
      </p>
    </div>
  </div>
</div>

<script>
// Password Toggle Functionality
function setupPasswordToggle(inputId, toggleId) {
  document.getElementById(toggleId)?.addEventListener('click', function() {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = this;
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleIcon.textContent = '🙈';
    } else {
      passwordInput.type = 'password';
      toggleIcon.textContent = '👁️';
    }
  });
}

setupPasswordToggle('passwordInput', 'passwordToggle');
setupPasswordToggle('confirmPasswordInput', 'confirmPasswordToggle');

// Password Strength Indicator
document.getElementById('passwordInput')?.addEventListener('input', function() {
  const password = this.value;
  const strengthContainer = document.getElementById('passwordStrength');
  const strengthBars = document.querySelectorAll('.strength-bar');
  const strengthText = document.getElementById('strengthText');
  
  if (password.length === 0) {
    strengthContainer.style.display = 'none';
    return;
  }
  
  strengthContainer.style.display = 'block';
  
  let strength = 0;
  let strengthLabel = '';
  
  // Length check
  if (password.length >= 8) strength++;
  
  // Character variety checks
  if (/[a-z]/.test(password)) strength++;
  if (/[A-Z]/.test(password)) strength++;
  if (/[0-9]/.test(password)) strength++;
  if (/[^A-Za-z0-9]/.test(password)) strength++;
  
  // Update strength bars
  strengthBars.forEach((bar, index) => {
    bar.classList.remove('active', 'medium', 'weak');
    if (index < strength) {
      if (strength <= 2) {
        bar.classList.add('weak');
        strengthLabel = 'Weak';
      } else if (strength <= 3) {
        bar.classList.add('medium');
        strengthLabel = 'Medium';
      } else {
        bar.classList.add('active');
        strengthLabel = 'Strong';
      }
    }
  });
  
  strengthText.textContent = `Password strength: ${strengthLabel}`;
});

// Form Submission with Loading State
document.getElementById('registerForm')?.addEventListener('submit', function() {
  const submitBtn = document.getElementById('submitBtn');
  const btnText = document.getElementById('btnText');
  
  submitBtn.classList.add('loading');
  submitBtn.disabled = true;
  btnText.style.opacity = '0';
});

// Input Focus Effects
document.querySelectorAll('.form-input').forEach(input => {
  input.addEventListener('focus', function() {
    this.parentElement.classList.add('focused');
  });
  
  input.addEventListener('blur', function() {
    this.parentElement.classList.remove('focused');
  });
});

// Smooth scroll reveal animation
window.addEventListener('load', function() {
  document.querySelector('.auth-card').style.opacity = '1';
  document.querySelector('.auth-card').style.transform = 'translateY(0)';
});
</script>

@endsection