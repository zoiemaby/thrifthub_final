<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Sign in — ThriftHub</title>
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
      --shadow-sm: 0 2px 8px rgba(15, 94, 77, 0.08);
      --shadow-md: 0 4px 16px rgba(15, 94, 77, 0.12);
      --shadow-lg: 0 8px 32px rgba(15, 94, 77, 0.16);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
      background: var(--beige);
      color: var(--text-dark);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      position: relative;
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* Main container */
    .container {
      width: 100%;
      max-width: 500px;
      position: relative;
      z-index: 1;
      margin: auto;
    }

    /* Login card */
    .login-card {
      background: var(--white);
      border-radius: 24px;
      padding: 50px 45px;
      box-shadow: var(--shadow-lg);
      position: relative;
    }

    @media (max-width: 640px) {
      .login-card {
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
      font-size: 32px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 10px;
      letter-spacing: -0.5px;
    }

    .card-subtitle {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .form-group {
      margin-bottom: 24px;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      color: var(--text-light);
      font-size: 18px;
      z-index: 1;
    }

    .form-input {
      width: 100%;
      padding: 14px 16px 14px 48px;
      font-size: 15px;
      color: var(--text-dark);
      background: var(--beige);
      border: 1px solid var(--border);
      border-radius: 12px;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(15, 94, 77, 0.1);
    }

    .form-input::placeholder {
      color: var(--text-light);
    }

    .password-toggle {
      position: absolute;
      right: 16px;
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 18px;
      padding: 4px;
      transition: color 0.2s ease;
      z-index: 2;
    }

    .password-toggle:hover {
      color: var(--thrift-green);
    }

    .forgot-link {
      text-align: right;
      margin-bottom: 30px;
    }

    .forgot-link a {
      color: var(--text-muted);
      text-decoration: none;
      font-size: 13px;
      transition: color 0.2s ease;
    }

    .forgot-link a:hover {
      color: var(--thrift-green);
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
      margin-bottom: 30px;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .signup-link {
      text-align: center;
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 30px;
    }

    .signup-link a {
      color: var(--thrift-green);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .signup-link a:hover {
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
      font-size: 14px;
      margin-bottom: 20px;
      text-align: center;
    }

    .error-message.show {
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
      to {
        transform: rotate(360deg);
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="login-card">
      <div class="card-header">
        <div class="logo-container">
          <div class="logo">TH</div>
          <div class="brand-name">ThriftHub</div>
        </div>
        <h1 class="card-title">Welcome</h1>
        <p class="card-subtitle">Log in to your account to continue</p>
      </div>

      <div class="error-message" id="errorMessage"></div>

      <form id="loginForm" method="POST" action="../actions/login_customer_action.php">

        <div class="form-group">
          <div class="input-wrapper">
            <span class="input-icon">👤</span>
            <input
              type="email"
              id="email"
              name="email"
              class="form-input"
              placeholder="awesome@user.com"
              required
              autocomplete="email" />
          </div>
        </div>

        <div class="form-group">
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input
              type="password"
              id="password"
              name="password"
              class="form-input"
              placeholder="••••••••••••"
              required
              autocomplete="current-password" />
            <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
              <span id="toggleIcon">👁️</span>
            </button>
          </div>
        </div>

        <div class="forgot-link">
          <a href="#">Forgot your password!</a>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
          Log In
        </button>
      </form>

      <div class="signup-link">
        Don't have an account? <a href="register.php">Sign up!</a>
      </div>

      <div class="social-login">
        <a href="#" class="social-icon" aria-label="Facebook">f</a>
        <a href="#" class="social-icon" aria-label="Twitter">🐦</a>
        <a href="#" class="social-icon" aria-label="LinkedIn">in</a>
      </div>
    </div>
  </div>

  <script>
    // Password toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      toggleIcon.textContent = type === 'password' ? '👁️' : '🙈';
    });

    // Remove inline login handler to avoid duplicate listeners and
    // let `assets/js/login.js` handle validation and redirects.
  </script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Login Form Validation -->
  <script src="../assets/js/login.js"></script>
</body>

</html>