<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ThriftHub - Ghana's #1 Thrift Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet"/>
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
      --border: #E8E3D8;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--text-dark);
      line-height: 1.6;
    }

    /* Top Green Bar */
    .top-bar {
      background: var(--thrift-green);
      color: var(--white);
      text-align: center;
      padding: 10px 20px;
      font-size: 14px;
      font-weight: 500;
    }

    /* Header */
    .header {
      background: var(--white);
      padding: 16px 40px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .header-content {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      font-weight: 700;
      color: var(--thrift-green);
      text-decoration: none;
      white-space: nowrap;
    }

    .search-bar {
      flex: 1;
      max-width: 500px;
      position: relative;
    }

    .search-bar input {
      width: 100%;
      padding: 12px 16px 12px 45px;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 14px;
      background: var(--beige);
      transition: all 0.3s ease;
    }

    .search-bar input:focus {
      outline: none;
      border-color: var(--thrift-green);
      background: var(--white);
    }

    .search-bar i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
    }

    .header-icons {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .header-icon {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: var(--beige);
      color: var(--text-dark);
      text-decoration: none;
      transition: all 0.3s ease;
      position: relative;
    }

    .header-icon:hover {
      background: var(--thrift-green);
      color: var(--white);
    }

    .header-icon .badge {
      position: absolute;
      top: -4px;
      right: -4px;
      background: var(--gold);
      color: var(--white);
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }

    .language-selector {
      padding: 8px 12px;
      border: 2px solid var(--border);
      border-radius: 8px;
      background: var(--white);
      font-size: 14px;
      cursor: pointer;
    }

    /* Navigation */
    .nav-bar {
      background: var(--white);
      border-top: 1px solid var(--border);
      padding: 12px 40px;
      position: relative;
    }

    .nav-links {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      gap: 32px;
      list-style: none;
    }

    .nav-item {
      position: relative;
    }

    .nav-links a {
      color: var(--text-dark);
      text-decoration: none;
      font-size: 15px;
      font-weight: 500;
      transition: color 0.3s ease;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .nav-links a:hover {
      color: var(--thrift-green);
    }

    .nav-links .chevron {
      font-size: 12px;
      transition: transform 0.3s ease;
    }

    .nav-item:hover .chevron {
      transform: rotate(180deg);
    }

    /* Dropdown Menu */
    .dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      background: var(--white);
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
      min-width: 200px;
      padding: 8px 0;
      margin-top: 8px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .nav-item:hover .dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-item {
      display: block;
      padding: 12px 20px;
      color: var(--text-dark);
      text-decoration: none;
      font-size: 14px;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .dropdown-item:hover {
      background: var(--beige);
      color: var(--thrift-green);
    }

    .dropdown-item i {
      width: 20px;
      text-align: center;
      font-size: 16px;
    }

    /* Hero Section */
    .hero {
      max-width: 1400px;
      margin: 60px auto;
      padding: 0 40px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
    }

    .hero-content h3 {
      display: inline-block;
      background: rgba(15, 94, 77, 0.1);
      color: var(--thrift-green);
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .hero-content h1 {
      font-family: 'Playfair Display', serif;
      font-size: 56px;
      font-weight: 700;
      color: var(--text-dark);
      line-height: 1.2;
      margin-bottom: 20px;
      letter-spacing: -1px;
    }

    .hero-content p {
      font-size: 18px;
      color: var(--text-muted);
      margin-bottom: 32px;
      line-height: 1.7;
    }

    .hero-buttons {
      display: flex;
      gap: 16px;
      margin-bottom: 40px;
    }

    .btn-primary {
      padding: 16px 32px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .btn-primary:hover {
      background: var(--thrift-green-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(15, 94, 77, 0.3);
    }

    .btn-secondary {
      padding: 16px 32px;
      background: var(--white);
      color: var(--thrift-green);
      border: 2px solid var(--thrift-green);
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .btn-secondary:hover {
      background: var(--beige);
    }

    .hero-stats {
      display: flex;
      gap: 40px;
    }

    .stat-item {
      display: flex;
      flex-direction: column;
    }

    .stat-number {
      font-size: 28px;
      font-weight: 700;
      color: var(--thrift-green);
    }

    .stat-label {
      font-size: 14px;
      color: var(--text-muted);
    }

    .hero-image {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .hero-image img {
      width: 100%;
      height: 500px;
      object-fit: cover;
    }

    .waste-overlay {
      position: absolute;
      bottom: 20px;
      left: 20px;
      background: var(--thrift-green);
      color: var(--white);
      padding: 16px 24px;
      border-radius: 12px;
      font-weight: 600;
    }

    /* Shop by Category */
    .category-section {
      max-width: 1400px;
      margin: 80px auto;
      padding: 0 40px;
    }

    .section-title {
      text-align: center;
      font-size: 36px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 40px;
    }

    .category-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 24px;
      max-width: 1000px;
      margin: 0 auto;
    }

    .category-card {
      background: var(--white);
      border: 2px solid var(--border);
      border-radius: 16px;
      padding: 32px 24px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .category-card:hover {
      border-color: var(--thrift-green);
      transform: translateY(-4px);
      box-shadow: 0 4px 16px rgba(15, 94, 77, 0.15);
    }

    .category-icon {
      font-size: 48px;
      margin-bottom: 16px;
    }

    .category-name {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
    }

    /* Featured Items */
    .featured-section {
      max-width: 1400px;
      margin: 80px auto;
      padding: 0 40px;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
    }

    .section-header h2 {
      font-size: 36px;
      font-weight: 700;
      color: var(--text-dark);
    }

    .view-all {
      color: var(--thrift-green);
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
    }

    .view-all:hover {
      text-decoration: underline;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 24px;
    }

    .product-card {
      background: var(--white);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .product-image {
      position: relative;
      width: 100%;
      height: 280px;
      background: var(--beige);
      overflow: hidden;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      background: var(--thrift-green);
      color: var(--white);
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .product-info {
      padding: 20px;
    }

    .product-name {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .product-price {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
    }

    .current-price {
      font-size: 20px;
      font-weight: 700;
      color: var(--thrift-green);
    }

    .original-price {
      font-size: 16px;
      color: var(--text-muted);
      text-decoration: line-through;
    }

    .product-rating {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 14px;
      color: var(--text-muted);
    }

    .stars {
      color: var(--gold);
    }

    /* Eco Impact Section */
    .eco-impact {
      background: var(--thrift-green);
      color: var(--white);
      margin: 80px 0 0;
      padding: 80px 40px;
    }

    .eco-content {
      max-width: 1400px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
    }

    .eco-text h2 {
      font-family: 'Playfair Display', serif;
      font-size: 42px;
      font-weight: 700;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .eco-text p {
      font-size: 18px;
      margin-bottom: 32px;
      opacity: 0.95;
      line-height: 1.7;
    }

    .eco-list {
      list-style: none;
      margin-bottom: 32px;
    }

    .eco-list li {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
      font-size: 16px;
    }

    .eco-list li::before {
      content: '✓';
      background: var(--white);
      color: var(--thrift-green);
      width: 24px;
      height: 24px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 14px;
    }

    .eco-image {
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .eco-image img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }

    /* Why Choose Section */
    .why-choose {
      max-width: 1400px;
      margin: 80px auto;
      padding: 0 40px;
    }

    .why-choose h2 {
      text-align: center;
      font-size: 36px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 60px;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 32px;
    }

    .feature-card {
      text-align: center;
      padding: 32px;
    }

    .feature-icon {
      width: 80px;
      height: 80px;
      background: var(--beige);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 36px;
      color: var(--thrift-green);
    }

    .feature-card h3 {
      font-size: 20px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .feature-card p {
      font-size: 15px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    /* Ready to Start Section */
    .ready-section {
      background: var(--beige);
      padding: 80px 40px;
      text-align: center;
    }

    .ready-content {
      max-width: 800px;
      margin: 0 auto;
    }

    .ready-section h2 {
      font-family: 'Playfair Display', serif;
      font-size: 42px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 16px;
    }

    .ready-section p {
      font-size: 18px;
      color: var(--text-muted);
      margin-bottom: 32px;
    }

    .ready-buttons {
      display: flex;
      gap: 16px;
      justify-content: center;
    }

    /* Footer */
    .footer {
      background: #1a1a1a;
      color: var(--white);
    }

    .footer-newsletter {
      background: #2a2a2a;
      padding: 40px;
    }

    .newsletter-content {
      max-width: 1400px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: center;
    }

    .newsletter-text h3 {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .newsletter-text p {
      color: #cccccc;
      font-size: 14px;
    }

    .newsletter-form {
      display: flex;
      gap: 12px;
    }

    .newsletter-form input {
      flex: 1;
      padding: 14px 16px;
      border: none;
      border-radius: 8px;
      background: #3a3a3a;
      color: var(--white);
      font-size: 14px;
    }

    .newsletter-form input::placeholder {
      color: #999;
    }

    .newsletter-form button {
      padding: 14px 32px;
      background: var(--thrift-green);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .newsletter-form button:hover {
      background: var(--thrift-green-light);
    }

    .footer-main {
      padding: 60px 40px 40px;
    }

    .footer-content {
      max-width: 1400px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
      gap: 40px;
    }

    .footer-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .footer-logo {
      width: 48px;
      height: 48px;
      background: var(--thrift-green);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: 20px;
    }

    .footer-brand-name {
      font-family: 'Playfair Display', serif;
      font-size: 24px;
      font-weight: 700;
      color: var(--white);
    }

    .footer-description {
      color: #cccccc;
      font-size: 14px;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .social-links {
      display: flex;
      gap: 12px;
    }

    .social-link {
      width: 40px;
      height: 40px;
      background: #2a2a2a;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .social-link:hover {
      background: var(--thrift-green);
    }

    .footer-column h4 {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 16px;
    }

    .footer-links {
      list-style: none;
    }

    .footer-links li {
      margin-bottom: 12px;
    }

    .footer-links a {
      color: #cccccc;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s ease;
    }

    .footer-links a:hover {
      color: var(--white);
    }

    .footer-bottom {
      border-top: 1px solid #2a2a2a;
      padding: 24px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .footer-bottom-content {
      max-width: 1400px;
      margin: 0 auto;
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .copyright {
      color: #999;
      font-size: 14px;
    }

    .footer-legal {
      display: flex;
      gap: 24px;
    }

    .footer-legal a {
      color: #999;
      text-decoration: none;
      font-size: 12px;
    }

    .footer-legal a:hover {
      color: var(--white);
    }

    .payment-methods {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 16px;
    }

    .payment-method {
      padding: 8px 12px;
      background: #2a2a2a;
      border-radius: 6px;
      font-size: 12px;
      color: #cccccc;
    }

    /* Responsive */
    @media (max-width: 968px) {
      .hero {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .hero-stats {
        justify-content: center;
      }

      .eco-content {
        grid-template-columns: 1fr;
      }

      .footer-content {
        grid-template-columns: 1fr 1fr;
      }

      .newsletter-content {
        grid-template-columns: 1fr;
      }

      .header-content {
        flex-wrap: wrap;
      }

      .search-bar {
        order: 3;
        width: 100%;
        max-width: 100%;
      }
    }

    @media (max-width: 640px) {
      .hero-content h1 {
        font-size: 36px;
      }

      .hero-buttons {
        flex-direction: column;
      }

      .nav-links {
        flex-wrap: wrap;
        gap: 16px;
      }

      .footer-content {
        grid-template-columns: 1fr;
      }

      .footer-bottom-content {
        flex-direction: column;
        gap: 16px;
        text-align: center;
      }
    }
  </style>
  </head>
<body>
  <!-- Top Green Bar -->
  <div class="top-bar">
    Join our sustainability mission! Every purchase saves 2kg of textile waste.
  </div>

  <!-- Header -->
  <header class="header">
    <div class="header-content">
      <a href="index.php" class="logo">ThriftHub</a>
      
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search for thrifted treasures...">
      </div>

      <div class="header-icons">
        <a href="#" class="header-icon">
          <i class="fas fa-shopping-cart"></i>
          <span class="badge">3</span>
        </a>
        <a href="#" class="header-icon">
          <i class="fas fa-bell"></i>
          <span class="badge">2</span>
        </a>
        <a href="view/login.php" class="header-icon">
          <i class="fas fa-user"></i>
        </a>
        <select class="language-selector">
          <option>EN</option>
          <option>FR</option>
        </select>
      </div>
    </div>
  </header>

  <!-- Navigation -->
  <nav class="nav-bar">
    <ul class="nav-links">
      <li class="nav-item">
        <a href="index.php">
          <i class="fas fa-home"></i>
          <span>Home</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#">
          <span>Shop</span>
          <i class="fas fa-chevron-down chevron"></i>
        </a>
        <div class="dropdown">
          <a href="#" class="dropdown-item">All Items</a>
          <a href="#" class="dropdown-item">Clothing</a>
          <a href="#" class="dropdown-item">Gadgets</a>
          <a href="#" class="dropdown-item">Accessories</a>
          <a href="#" class="dropdown-item">Home</a>
          <a href="#" class="dropdown-item">Kids</a>
        </div>
      </li>
      <li class="nav-item">
        <a href="view/about.php">
          <span>About Us</span>
          <i class="fas fa-chevron-down chevron"></i>
        </a>
        <div class="dropdown">
          <a href="view/about.php#mission" class="dropdown-item">Mission & Vision</a>
          <a href="view/about.php#values" class="dropdown-item">Our Values</a>
          <a href="view/about.php#impact" class="dropdown-item">Impact</a>
          <a href="view/about.php#press" class="dropdown-item">Press</a>
        </div>
      </li>
      <li class="nav-item">
        <a href="#">
          <span>More</span>
          <i class="fas fa-chevron-down chevron"></i>
        </a>
        <div class="dropdown">
          <a href="#" class="dropdown-item">
            <i class="fas fa-leaf"></i>
            <span>Sustainability</span>
          </a>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users"></i>
            <span>Community & Blog</span>
          </a>
          <a href="#" class="dropdown-item">
            <i class="fas fa-question-circle"></i>
            <span>Help Center</span>
          </a>
          <a href="#" class="dropdown-item">
            <i class="fas fa-briefcase"></i>
            <span>Careers</span>
          </a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h3>Shop Sustainability, Save More.</h3>
      <h1>Ghana's #1 Thrift Marketplace</h1>
      <p>Discover quality pre-loved items at unbeatable prices. Support local sellers, reduce waste, and find unique treasures.</p>
      
      <div class="hero-buttons">
        <a href="view/login.php" class="btn-primary">Start Shopping</a>
        <a href="view/login.php" class="btn-secondary">Start Selling</a>
      </div>

      <div class="hero-stats">
        <div class="stat-item">
          <span class="stat-number">50K+</span>
          <span class="stat-label">Items Listed</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">12K+</span>
          <span class="stat-label">Happy Buyers</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">2.5K</span>
          <span class="stat-label">Sellers</span>
        </div>
      </div>
    </div>

    <div class="hero-image">
      <img src="assets/images/landback.jpg" alt="ThriftHub Hero">
      <div class="waste-overlay">
        Waste Saved: 125 Tonnes
      </div>
    </div>
  </section>

  <!-- Shop by Category -->
  <section class="category-section">
    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
      <div class="category-card">
        <div class="category-icon">👕</div>
        <div class="category-name">Clothing</div>
      </div>
      <div class="category-card">
        <div class="category-icon">📱</div>
        <div class="category-name">Gadgets</div>
      </div>
      <div class="category-card">
        <div class="category-icon">💍</div>
        <div class="category-name">Accessories</div>
      </div>
      <div class="category-card">
        <div class="category-icon">🏠</div>
        <div class="category-name">Home</div>
      </div>
      <div class="category-card">
        <div class="category-icon">👶</div>
        <div class="category-name">Kids</div>
      </div>
    </div>
  </section>

  <!-- Featured Thrift Items -->
  <section class="featured-section">
    <div class="section-header">
      <h2>Featured Thrift Items</h2>
      <a href="#" class="view-all">View All</a>
    </div>
    <div class="products-grid">
      <div class="product-card">
        <div class="product-image">
          <div class="product-badge">10% OFF</div>
          <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
        </div>
        <div class="product-info">
          <div class="product-name">Vintage Levi's Denim Jacket</div>
          <div class="product-price">
            <span class="current-price">GHS 150</span>
            <span class="original-price">GHS 200</span>
          </div>
          <div class="product-rating">
            <span class="stars">★★★★★</span>
            <span>(24 reviews)</span>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-image">
          <div class="product-badge">FREE SHIPPING</div>
          <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
        </div>
        <div class="product-info">
          <div class="product-name">iPhone 12 Pro</div>
          <div class="product-price">
            <span class="current-price">GHS 2,800</span>
            <span class="original-price">GHS 3,000</span>
          </div>
          <div class="product-rating">
            <span class="stars">★★★★☆</span>
            <span>(18 reviews)</span>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-image">
          <div class="product-badge">NEW</div>
          <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);"></div>
        </div>
        <div class="product-info">
          <div class="product-name">Handmade Kente Bag</div>
          <div class="product-price">
            <span class="current-price">GHS 85</span>
          </div>
          <div class="product-rating">
            <span class="stars">★★★★★</span>
            <span>(32 reviews)</span>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-image">
          <div class="product-badge">10% OFF</div>
          <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"></div>
        </div>
        <div class="product-info">
          <div class="product-name">Nike Air Max Sneakers</div>
          <div class="product-price">
            <span class="current-price">GHS 180</span>
            <span class="original-price">GHS 200</span>
          </div>
          <div class="product-rating">
            <span class="stars">★★★★☆</span>
            <span>(15 reviews)</span>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-image">
          <div class="product-badge">FREE SHIPPING</div>
          <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);"></div>
        </div>
        <div class="product-info">
          <div class="product-name">HP Pavilion Laptop</div>
          <div class="product-price">
            <span class="current-price">GHS 1,800</span>
            <span class="original-price">GHS 2,000</span>
          </div>
          <div class="product-rating">
            <span class="stars">★★★★★</span>
            <span>(28 reviews)</span>
          </div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-image">
          <div class="product-badge">NEW</div>
          <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);"></div>
        </div>
        <div class="product-info">
          <div class="product-name">Ariana Print Maxi Dress</div>
          <div class="product-price">
            <span class="current-price">GHS 95</span>
          </div>
          <div class="product-rating">
            <span class="stars">★★★★☆</span>
            <span>(21 reviews)</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Eco-Impact -->
  <section class="eco-impact">
    <div class="eco-content">
      <div class="eco-text">
        <h2>🌿 Our Eco-Impact</h2>
        <p>Every purchase on ThriftHub contributes to a circular economy and reduces textile waste in Ghana.</p>
        <ul class="eco-list">
          <li>125 Tonnes of Textile Waste Diverted</li>
          <li>2,000+ Jobs Created</li>
          <li>Supporting local entrepreneurs</li>
          <li>40,000+ Items Given Second Life</li>
          <li>Quality products at affordable prices</li>
        </ul>
        <a href="#" class="btn-secondary" style="background: var(--white); color: var(--thrift-green); border: 2px solid var(--white);">Learn More About Our Mission</a>
      </div>
      <div class="eco-image">
        <img src="assets/images/landback.jpg" alt="Eco Impact">
      </div>
    </div>
  </section>

  <!-- Why Choose ThriftHub -->
  <section class="why-choose">
    <h2>Why Choose ThriftHub?</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🛡️</div>
        <h3>Secure Payments</h3>
        <p>Payment holds funds until delivery confirmed.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🌱</div>
        <h3>Eco-Friendly</h3>
        <p>Buy/sell pre-loved items, reduce waste.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🚚</div>
        <h3>Fast Delivery</h3>
        <p>1-3 days within Accra, 3-7 nationwide.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">👥</div>
        <h3>Trusted Sellers</h3>
        <p>Verified sellers with ratings & reviews.</p>
      </div>
    </div>
  </section>

  <!-- Ready to Start -->
  <section class="ready-section">
    <div class="ready-content">
      <h2>Ready to Start Your Thrift Journey?</h2>
      <p>Join thousands of Ghanaians buying and selling quality pre-loved items.</p>
      <div class="ready-buttons">
        <a href="view/login.php" class="btn-primary">Browse Items</a>
        <a href="view/login.php" class="btn-secondary">Become a Seller</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-newsletter">
      <div class="newsletter-content">
        <div class="newsletter-text">
          <h3>Stay Updated</h3>
          <p>Get the latest deals and sustainability tips.</p>
        </div>
        <form class="newsletter-form">
          <input type="email" placeholder="Enter your email">
          <button type="submit">Subscribe</button>
        </form>
      </div>
    </div>

    <div class="footer-main">
      <div class="footer-content">
        <div>
          <div class="footer-brand">
            <div class="footer-logo">TH</div>
            <div class="footer-brand-name">ThriftHub</div>
          </div>
          <p class="footer-description">Ghana's trusted marketplace for sustainable, affordable thrift shopping. Every purchase supports local sellers and reduces textile waste.</p>
          <div class="social-links">
            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
          </div>
        </div>

        <div class="footer-column">
          <h4>About</h4>
          <ul class="footer-links">
            <li><a href="#">About Us</a></li>
            <li><a href="#">Our Impact</a></li>
            <li><a href="#">Press</a></li>
            <li><a href="#">Careers</a></li>
          </ul>
        </div>

        <div class="footer-column">
          <h4>Sell</h4>
          <ul class="footer-links">
            <li><a href="#">How It Works</a></li>
            <li><a href="#">Fees</a></li>
            <li><a href="#">Start Selling</a></li>
            <li><a href="#">Seller Dashboard</a></li>
          </ul>
        </div>

        <div class="footer-column">
          <h4>Help</h4>
          <ul class="footer-links">
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Returns & Refunds</a></li>
            <li><a href="#">Shipping Info</a></li>
          </ul>
        </div>

        <div class="footer-column">
          <h4>Policies</h4>
          <ul class="footer-links">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Seller Policy</a></li>
            <li><a href="#">Accessibility</a></li>
          </ul>
          <div class="payment-methods">
            <div class="payment-method">MTN Mobile</div>
            <div class="payment-method">Vodafone Cash</div>
            <div class="payment-method">AirtelTigo Money</div>
            <div class="payment-method">Paystack</div>
            <div class="payment-method">Visa/Mastercard</div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="footer-bottom-content">
        <div class="copyright">© 2024 ThriftHub Ghana. All rights reserved.</div>
        <div class="footer-legal">
          <a href="#">Privacy</a>
          <a href="#">Terms</a>
          <a href="#">Accessibility</a>
        </div>
      </div>
    </div>
  </footer>
  </body>
</html>
