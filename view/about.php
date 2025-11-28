<?php
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - ThriftHub</title>
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

    .nav-links a:hover,
    .nav-links a.active {
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
    .about-hero {
      max-width: 1400px;
      margin: 60px auto;
      padding: 0 40px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
    }

    .about-hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: 48px;
      font-weight: 700;
      color: var(--text-dark);
      line-height: 1.2;
      margin-bottom: 24px;
      letter-spacing: -1px;
    }

    .about-hero p {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.8;
      margin-bottom: 16px;
    }

    .about-hero-image {
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .about-hero-image img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }

    /* Mission & Vision */
    .mission-vision {
      max-width: 1400px;
      margin: 80px auto;
      padding: 0 40px;
    }

    .mission-vision-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
    }

    .mission-card,
    .vision-card {
      background: var(--white);
      border: 2px solid var(--border);
      border-radius: 16px;
      padding: 40px;
      position: relative;
    }

    .mission-card .icon,
    .vision-card .icon {
      width: 60px;
      height: 60px;
      background: var(--thrift-green);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-size: 24px;
      margin-bottom: 24px;
    }

    .mission-card h2,
    .vision-card h2 {
      font-size: 28px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 16px;
    }

    .mission-card p,
    .vision-card p {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.8;
    }

    /* Our Values */
    .values-section {
      max-width: 1400px;
      margin: 80px auto;
      padding: 0 40px;
      text-align: center;
    }

    .values-section h2 {
      font-size: 36px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 60px;
    }

    .values-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;
    }

    .value-card {
      padding: 32px;
    }

    .value-icon {
      width: 80px;
      height: 80px;
      background: var(--beige);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 36px;
      color: var(--thrift-green);
    }

    .value-card h3 {
      font-size: 22px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .value-card p {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.7;
    }

    /* Our Impact */
    .impact-section {
      background: var(--thrift-green);
      color: var(--white);
      padding: 80px 40px;
      margin: 80px 0;
    }

    .impact-content {
      max-width: 1400px;
      margin: 0 auto;
    }

    .impact-section h2 {
      font-size: 36px;
      font-weight: 700;
      text-align: center;
      margin-bottom: 16px;
    }

    .impact-section .subtitle {
      text-align: center;
      font-size: 18px;
      opacity: 0.95;
      margin-bottom: 60px;
    }

    .impact-stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
      margin-bottom: 60px;
    }

    .impact-stat {
      background: var(--white);
      color: var(--text-dark);
      padding: 32px;
      border-radius: 16px;
      text-align: center;
    }

    .impact-stat-number {
      font-size: 36px;
      font-weight: 700;
      color: var(--thrift-green);
      margin-bottom: 8px;
    }

    .impact-stat-label {
      font-size: 16px;
      color: var(--text-muted);
    }

    .impact-initiatives {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 32px;
      margin-top: 60px;
    }

    .initiative-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
    }

    .initiative-icon {
      width: 40px;
      height: 40px;
      background: var(--white);
      color: var(--thrift-green);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }

    .initiative-content h3 {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .initiative-content p {
      font-size: 16px;
      opacity: 0.95;
      line-height: 1.6;
    }

    /* In The News */
    .news-section {
      max-width: 1400px;
      margin: 80px auto;
      padding: 0 40px;
      text-align: center;
    }

    .news-section h2 {
      font-size: 36px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 60px;
    }

    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 32px;
    }

    .news-card {
      background: var(--white);
      border: 2px solid var(--border);
      border-radius: 16px;
      padding: 32px;
      text-align: left;
    }

    .news-card h3 {
      font-size: 20px;
      font-weight: 600;
      color: var(--thrift-green);
      margin-bottom: 12px;
    }

    .news-card p {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.7;
    }

    /* Responsive */
    @media (max-width: 968px) {
      .about-hero {
        grid-template-columns: 1fr;
      }

      .mission-vision-grid {
        grid-template-columns: 1fr;
      }

      .values-grid {
        grid-template-columns: 1fr;
      }

      .impact-stats {
        grid-template-columns: repeat(2, 1fr);
      }

      .impact-initiatives {
        grid-template-columns: 1fr;
      }

      .news-grid {
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
      .impact-stats {
        grid-template-columns: 1fr;
      }

      .about-hero h1 {
        font-size: 36px;
      }
    }

    /* Smooth scroll */
    html {
      scroll-behavior: smooth;
    }

    section {
      scroll-margin-top: 100px;
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
      <a href="../index.php" class="logo">ThriftHub</a>
      
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
        <?php 
        $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
        ?>
        <a href="<?php echo $isLoggedIn ? '#' : 'login.php'; ?>" class="header-icon">
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
        <a href="../index.php">
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
        <a href="about.php" class="active">
          <span>About Us</span>
          <i class="fas fa-chevron-down chevron"></i>
        </a>
        <div class="dropdown">
          <a href="about.php#mission" class="dropdown-item">Mission & Vision</a>
          <a href="about.php#values" class="dropdown-item">Our Values</a>
          <a href="about.php#impact" class="dropdown-item">Impact</a>
          <a href="about.php#press" class="dropdown-item">Press</a>
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
  <section class="about-hero">
    <div>
      <h1>Making Thrift Shopping Easy & Sustainable</h1>
      <p>ThriftHub is Ghana's leading digital marketplace for buying and selling quality pre-loved items. We're on a mission to make sustainable shopping accessible, affordable, and trusted.</p>
      <p>Founded in 2021, we've grown to serve over 12,000 buyers and 2,500 sellers across Ghana, creating economic opportunities while reducing environmental impact.</p>
    </div>
    <div class="about-hero-image">
      <img src="../assets/images/landback.jpg" alt="ThriftHub Community">
    </div>
  </section>

  <!-- Mission & Vision -->
  <section id="mission" class="mission-vision">
    <div class="mission-vision-grid">
      <div class="mission-card">
        <div class="icon">
          <i class="fas fa-shopping-bag"></i>
        </div>
        <h2>Our Mission</h2>
        <p>To create Ghana's most trusted and sustainable marketplace where everyone can buy and sell quality pre-loved items, reducing waste while creating economic opportunities for thousands of Ghanaians.</p>
      </div>
      <div class="vision-card">
        <div class="icon">
          <i class="fas fa-shopping-bag"></i>
        </div>
        <h2>Our Vision</h2>
        <p>To be West Africa's leading circular economy platform, where thrift shopping is the first choice for conscious consumers, and where many Ghanaians can earn income from items they no longer need.</p>
      </div>
    </div>
  </section>

  <!-- Our Values -->
  <section id="values" class="values-section">
    <h2>Our Values</h2>
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon">🌱</div>
        <h3>Sustainability First</h3>
        <p>Every transaction reduces waste and extends product lifecycles.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">👥</div>
        <h3>Community Focused</h3>
        <p>Supporting local sellers and building trust in our marketplace.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">🛡️</div>
        <h3>Quality & Trust</h3>
        <p>Verified sellers, secure payments, and buyer protection.</p>
      </div>
    </div>
  </section>

  <!-- Our Impact -->
  <section id="impact" class="impact-section">
    <div class="impact-content">
      <h2>Our Impact</h2>
      <p class="subtitle">Every transaction on ThriftHub contributes to a more sustainable Ghana. Here's the impact we've made together.</p>
      
      <div class="impact-stats">
        <div class="impact-stat">
          <div class="impact-stat-number">125 Tonnes</div>
          <div class="impact-stat-label">Textile Waste Diverted</div>
        </div>
        <div class="impact-stat">
          <div class="impact-stat-number">2,500+</div>
          <div class="impact-stat-label">Jobs Created</div>
        </div>
        <div class="impact-stat">
          <div class="impact-stat-number">50,000+</div>
          <div class="impact-stat-label">Items Re-sold</div>
        </div>
        <div class="impact-stat">
          <div class="impact-stat-number">GHS 5M+</div>
          <div class="impact-stat-label">Economic Value</div>
        </div>
      </div>

      <div class="impact-initiatives">
        <div class="initiative-item">
          <div class="initiative-icon">⭐</div>
          <div class="initiative-content">
            <h3>Star Seller Program</h3>
            <p>300+ verified star sellers</p>
          </div>
        </div>
        <div class="initiative-item">
          <div class="initiative-icon">🎓</div>
          <div class="initiative-content">
            <h3>Campus Initiative</h3>
            <p>Monthly thrift sales at Universities</p>
          </div>
        </div>
        <div class="initiative-item">
          <div class="initiative-icon">👩</div>
          <div class="initiative-content">
            <h3>Women Empowerment</h3>
            <p>40% of sellers are women entrepreneurs</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- In The News -->
  <section id="press" class="news-section">
    <h2>In The News</h2>
    <div class="news-grid">
      <div class="news-card">
        <h3>Business Insider Africa</h3>
        <p>Praised for our impact on sustainable commerce in West Africa.</p>
      </div>
      <div class="news-card">
        <h3>Ghana Tech Summit</h3>
        <p>Praised for our impact on sustainable commerce in West Africa.</p>
      </div>
      <div class="news-card">
        <h3>Sustainability Magazine</h3>
        <p>Praised for our impact on sustainable commerce in West Africa.</p>
      </div>
    </div>
  </section>
</body>
</html>

