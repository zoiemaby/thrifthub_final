<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Create account — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --thrift-green: #0F5E4D;
      --thrift-green-dark: #0A4538;
      --thrift-green-light: #1A7A66;
      --beige: #F6F2EA;
      --white: #FFFFFF;
      --text-dark: #2C2C2C;
      --text-muted: #6B6B6B;
      --text-light: #9A9A9A;
      --gold: #C9A961;
      --gold-light: #E5D4A8;
      --border: #E8E3D8;
      --error: #D32F2F;
      --success: #2E7D32;
      --shadow-sm: 0 2px 8px rgba(15, 94, 77, 0.08);
      --shadow-md: 0 4px 16px rgba(15, 94, 77, 0.12);
      --shadow-lg: 0 8px 32px rgba(15, 94, 77, 0.16);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
      background: var(--beige);
      color: var(--text-dark);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* Main container */
    .container {
      width: 100%;
      max-width: 600px;
      position: relative;
      z-index: 1;
    }

    /* Register card */
    .register-card {
      background: var(--white);
      border-radius: 24px;
      padding: 50px 45px;
      box-shadow: var(--shadow-lg);
      position: relative;
      max-height: 90vh;
      overflow-y: auto;
    }

    @media (max-width: 640px) {
      .register-card {
        padding: 40px 30px;
      }
    }

    .card-header {
      margin-bottom: 36px;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }

    .logo {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, var(--thrift-green) 0%, var(--thrift-green-light) 100%);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-family: 'Playfair Display', serif;
      font-size: 24px;
      font-weight: 700;
      box-shadow: var(--shadow-md);
    }

    .brand-name {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      font-weight: 700;
      color: var(--thrift-green);
      letter-spacing: -0.5px;
    }

    .card-title {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      font-weight: 700;
      color: var(--thrift-green);
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }

    .card-subtitle {
      font-size: 15px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    @media (max-width: 640px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .form-label .required {
      color: var(--error);
      margin-left: 2px;
    }

    /* User type toggle */
    .user-type-toggle {
      display: flex;
      background: var(--beige);
      border-radius: 12px;
      padding: 4px;
      margin-bottom: 30px;
      gap: 4px;
    }

    .toggle-option {
      flex: 1;
      padding: 12px 20px;
      text-align: center;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-muted);
      border: none;
      background: transparent;
    }

    .toggle-option.active {
      background: var(--white);
      color: var(--thrift-green);
      box-shadow: 0 2px 8px rgba(15, 94, 77, 0.1);
      font-weight: 600;
    }

    .input-wrapper {
      position: relative;
    }

    .form-input {
      width: 100%;
      padding: 14px 16px;
      font-size: 14px;
      color: var(--text-dark);
      background: var(--beige);
      border: 2px solid var(--border);
      border-radius: 12px;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
      box-shadow: 0 0 0 4px rgba(15, 94, 77, 0.1);
    }

    .form-input::placeholder {
      color: var(--text-light);
    }

    .form-input.error {
      border-color: var(--error);
    }

    .password-toggle {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 18px;
      padding: 4px;
      transition: color 0.2s ease;
    }

    .password-toggle:hover {
      color: var(--thrift-green);
    }

    .input-hint {
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 6px;
    }

    .password-strength {
      margin-top: 8px;
      height: 4px;
      background: var(--border);
      border-radius: 2px;
      overflow: hidden;
    }

    .password-strength-bar {
      height: 100%;
      width: 0%;
      transition: all 0.3s ease;
      border-radius: 2px;
    }

    .password-strength-bar.weak {
      width: 33%;
      background: var(--error);
    }

    .password-strength-bar.medium {
      width: 66%;
      background: var(--gold);
    }

    .password-strength-bar.strong {
      width: 100%;
      background: var(--success);
    }

    .checkbox-group {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 25px;
      padding: 14px;
      background: var(--beige);
      border-radius: 12px;
    }

    .checkbox-group input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: var(--thrift-green);
      cursor: pointer;
      margin-top: 2px;
      flex-shrink: 0;
    }

    .checkbox-group label {
      font-size: 13px;
      color: var(--text-dark);
      cursor: pointer;
      line-height: 1.5;
      user-select: none;
    }

    .checkbox-group a {
      color: var(--thrift-green);
      text-decoration: none;
      font-weight: 500;
    }

    .checkbox-group a:hover {
      text-decoration: underline;
    }

    .submit-btn {
      width: 100%;
      padding: 16px;
      font-size: 16px;
      font-weight: 600;
      color: var(--white);
      background: linear-gradient(135deg, var(--thrift-green) 0%, var(--thrift-green-light) 100%);
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-md);
      margin-bottom: 25px;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .login-link {
      text-align: center;
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 20px;
    }

    .login-link a {
      color: var(--thrift-green);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    .social-login {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }

    .social-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--beige);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 18px;
      transition: all 0.3s ease;
      border: 1px solid var(--border);
    }

    .social-icon:hover {
      background: var(--thrift-green);
      color: var(--white);
      border-color: var(--thrift-green);
      transform: translateY(-2px);
    }

    .error-message {
      display: none;
      padding: 12px 16px;
      background: rgba(211, 47, 47, 0.1);
      border: 1px solid var(--error);
      border-radius: 12px;
      color: var(--error);
      font-size: 13px;
      margin-bottom: 20px;
      text-align: center;
    }

    .error-message.show {
      display: block;
    }

    .field-error {
      display: none;
      font-size: 11px;
      color: var(--error);
      margin-top: 6px;
    }

    .field-error.show {
      display: block;
    }

    .submit-btn.loading {
      pointer-events: none;
      opacity: 0.7;
      position: relative;
    }

    .submit-btn.loading::after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      top: 50%;
      left: 50%;
      margin-left: -10px;
      margin-top: -10px;
      border: 2px solid var(--white);
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Custom scrollbar */
    .register-card::-webkit-scrollbar {
      width: 6px;
    }

    .register-card::-webkit-scrollbar-track {
      background: var(--beige);
      border-radius: 3px;
    }

    .register-card::-webkit-scrollbar-thumb {
      background: var(--thrift-green);
      border-radius: 3px;
    }

    .register-card::-webkit-scrollbar-thumb:hover {
      background: var(--thrift-green-dark);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="register-card">
      <div class="card-header">
        <div class="logo-container">
          <div class="logo">TH</div>
          <div class="brand-name">ThriftHub</div>
        </div>
        <h1 class="card-title">Create your account</h1>
        <p class="card-subtitle">Join the ThriftHub community and start your sustainable shopping journey</p>
      </div>

      <div class="error-message" id="errorMessage"></div>

      <!-- User Type Toggle -->
      <div class="user-type-toggle">
        <button type="button" class="toggle-option active" id="buyerToggle" data-type="buyer">
          Sign up as a Buyer
        </button>
        <button type="button" class="toggle-option" id="sellerToggle" data-type="seller">
          Sign up as a Seller
        </button>
      </div>

      <form id="registerForm" method="POST" action="../actions/register_customer_action.php" novalidate>
        <input type="hidden" name="role" id="userRole" value="customer">
        <div class="form-row">
          <div class="form-group">
            <label for="fullname" class="form-label">Full name <span class="required">*</span></label>
            <div class="input-wrapper">
              <input 
                type="text" 
                id="fullname" 
                name="fullname" 
                class="form-input" 
                placeholder="Ama Mensah" 
                required 
                minlength="2"
              />
            </div>
            <div class="field-error" id="fullnameError"></div>
          </div>

          <div class="form-group">
            <label for="username" class="form-label">Username <span class="required">*</span></label>
            <div class="input-wrapper">
              <input 
                type="text" 
                id="username" 
                name="username" 
                class="form-input" 
                placeholder="amensah" 
                required 
                minlength="3"
                pattern="[a-zA-Z0-9_]+"
              />
            </div>
            <div class="input-hint">Only letters, numbers, and underscores</div>
            <div class="field-error" id="usernameError"></div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email" class="form-label">Email address <span class="required">*</span></label>
            <div class="input-wrapper">
              <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input" 
                placeholder="you@example.com" 
                required 
                autocomplete="email"
              />
            </div>
            <div class="input-hint">We'll send a confirmation to this email</div>
            <div class="field-error" id="emailError"></div>
          </div>

          <div class="form-group">
            <label for="phone" class="form-label">Phone number</label>
            <div class="input-wrapper">
              <input 
                type="tel" 
                id="phone" 
                name="phone" 
                class="form-input" 
                placeholder="+233 54 000 0000" 
                pattern="^[0-9()+\- ]{7,}$"
              />
            </div>
            <div class="input-hint">Optional - for order updates</div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password" class="form-label">Password <span class="required">*</span></label>
            <div class="input-wrapper">
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-input" 
                placeholder="••••••••" 
                required 
                minlength="6"
                autocomplete="new-password"
              />
              <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                <span id="toggleIcon">👁️</span>
              </button>
            </div>
            <div class="password-strength">
              <div class="password-strength-bar" id="passwordStrength"></div>
            </div>
            <div class="input-hint">At least 6 characters</div>
            <div class="field-error" id="passwordError"></div>
          </div>

          <div class="form-group">
            <label for="confirmPassword" class="form-label">Confirm password <span class="required">*</span></label>
            <div class="input-wrapper">
              <input 
                type="password" 
                id="confirmPassword" 
                name="confirmPassword" 
                class="form-input" 
                placeholder="••••••••" 
                required 
                minlength="6"
                autocomplete="new-password"
              />
              <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="Toggle password visibility">
                <span id="toggleConfirmIcon">👁️</span>
              </button>
            </div>
            <div class="field-error" id="confirmPasswordError"></div>
          </div>
        </div>

        <div class="checkbox-group">
          <input type="checkbox" id="terms" name="terms" required />
          <label for="terms">
            I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
          </label>
        </div>
        <div class="field-error" id="termsError"></div>

        <button type="submit" class="submit-btn" id="submitBtn">
          Create account
        </button>
      </form>

      <div class="login-link">
        Already have an account? <a href="login.php">Sign in!</a>
      </div>

      <div class="social-login">
        <a href="#" class="social-icon" aria-label="Facebook">f</a>
        <a href="#" class="social-icon" aria-label="Twitter">🐦</a>
        <a href="#" class="social-icon" aria-label="LinkedIn">in</a>
      </div>
    </div>
  </div>

  <script>
    // User type toggle
    const buyerToggle = document.getElementById('buyerToggle');
    const sellerToggle = document.getElementById('sellerToggle');
    const userRoleInput = document.getElementById('userRole');

    buyerToggle.addEventListener('click', () => {
      buyerToggle.classList.add('active');
      sellerToggle.classList.remove('active');
      userRoleInput.value = 'customer';
    });

    sellerToggle.addEventListener('click', () => {
      sellerToggle.classList.add('active');
      buyerToggle.classList.remove('active');
      userRoleInput.value = 'seller';
    });

    // Password toggles
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      toggleIcon.textContent = type === 'password' ? '👁️' : '🙈';
    });

    toggleConfirmPassword.addEventListener('click', () => {
      const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordInput.setAttribute('type', type);
      toggleConfirmIcon.textContent = type === 'password' ? '👁️' : '🙈';
    });

    // Password strength
    const passwordStrength = document.getElementById('passwordStrength');
    
    passwordInput.addEventListener('input', () => {
      const password = passwordInput.value;
      let strength = 0;
      
      if (password.length >= 6) strength++;
      if (password.length >= 8) strength++;
      if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
      if (/\d/.test(password)) strength++;
      if (/[^a-zA-Z0-9]/.test(password)) strength++;
      
      passwordStrength.className = 'password-strength-bar';
      if (strength <= 2) {
        passwordStrength.classList.add('weak');
      } else if (strength <= 3) {
        passwordStrength.classList.add('medium');
      } else {
        passwordStrength.classList.add('strong');
      }
    });

    // Form validation
    const registerForm = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const errorMessage = document.getElementById('errorMessage');

    function showFieldError(fieldId, message) {
      const errorEl = document.getElementById(fieldId + 'Error');
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('show');
        document.getElementById(fieldId).classList.add('error');
      }
    }

    function clearFieldError(fieldId) {
      const errorEl = document.getElementById(fieldId + 'Error');
      if (errorEl) {
        errorEl.classList.remove('show');
        errorEl.textContent = '';
        const field = document.getElementById(fieldId);
        if (field) field.classList.remove('error');
      }
    }

    function validateForm() {
      let isValid = true;
      
      ['fullname', 'username', 'email', 'password', 'confirmPassword', 'terms'].forEach(id => {
        clearFieldError(id);
      });

      const fullname = document.getElementById('fullname').value.trim();
      if (fullname.length < 2) {
        showFieldError('fullname', 'Full name must be at least 2 characters');
        isValid = false;
      }

      const username = document.getElementById('username').value.trim();
      if (username.length < 3) {
        showFieldError('username', 'Username must be at least 3 characters');
        isValid = false;
      } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        showFieldError('username', 'Invalid characters');
        isValid = false;
      }

      const email = document.getElementById('email').value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showFieldError('email', 'Invalid email address');
        isValid = false;
      }

      const password = document.getElementById('password').value;
      if (password.length < 6) {
        showFieldError('password', 'Password must be at least 6 characters');
        isValid = false;
      }

      const confirmPassword = document.getElementById('confirmPassword').value;
      if (password !== confirmPassword) {
        showFieldError('confirmPassword', 'Passwords do not match');
        isValid = false;
      }

      const terms = document.getElementById('terms').checked;
      if (!terms) {
        showFieldError('terms', 'You must agree to the terms');
        isValid = false;
      }

      return isValid;
    }

    ['fullname', 'username', 'email', 'password', 'confirmPassword'].forEach(id => {
      const field = document.getElementById(id);
      if (field) {
        field.addEventListener('blur', () => validateForm());
      }
    });

    confirmPasswordInput.addEventListener('input', () => {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;
      
      if (confirmPassword && password !== confirmPassword) {
        showFieldError('confirmPassword', 'Passwords do not match');
      } else {
        clearFieldError('confirmPassword');
      }
    });

    registerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      errorMessage.classList.remove('show');
      
      if (!validateForm()) {
        errorMessage.textContent = 'Please fix the errors above';
        errorMessage.classList.add('show');
        return;
      }

      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
      submitBtn.textContent = '';

      const formData = new FormData(registerForm);
      
      try {
        const response = await fetch(registerForm.action, {
          method: 'POST',
          body: formData
        });

        const result = await response.json().catch(() => null);
        
        if (response.ok && result && result.success) {
          window.location.href = 'login.php?registered=1';
        } else {
          const msg = (result && result.message) ? result.message : 'An error occurred. Please try again.';
          errorMessage.textContent = msg;
          errorMessage.classList.add('show');
          submitBtn.classList.remove('loading');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Create account';
        }
      } catch (error) {
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.classList.add('show');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create account';
      }
    });

    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
      input.addEventListener('input', () => {
        const fieldId = input.id;
        clearFieldError(fieldId);
        errorMessage.classList.remove('show');
      });
    });
  </script>
</body>
</html>
