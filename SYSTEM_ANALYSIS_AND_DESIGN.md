# System Analysis and Design Document
## ThriftHub E-Commerce Platform

**Version:** 1.0  
**Date:** November 2025  
**Project:** ThriftHub - Ghana's #1 Thrift Marketplace

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [System Requirements](#system-requirements)
3. [Tools and Technologies](#tools-and-technologies)
4. [System Architecture](#system-architecture)
5. [Database Design](#database-design)
6. [Functional Modules](#functional-modules)
7. [Design Patterns and Techniques](#design-patterns-and-techniques)
8. [Security Design](#security-design)
9. [Performance Optimization](#performance-optimization)
10. [System Deployment](#system-deployment)

---

## Executive Summary

ThriftHub is a comprehensive e-commerce platform designed for the Ghanaian market, focusing on sustainable thrift shopping. The system enables multiple user roles (Admin, Customer/Buyer, Seller) to interact in a marketplace environment where sellers can list pre-loved items and buyers can purchase them. The platform includes advanced features such as product recommendations, real-time messaging, secure payment processing, and comprehensive order management.

---

## System Requirements

### 1. User Authentication and Authorization System

**Requirement:** The system must support multi-role user authentication with secure session management and role-based access control.

**Details:**
- **User Roles:** Admin (Role ID: 1), Customer/Buyer (Role ID: 2), Seller (Role ID: 3)
- **Authentication:** Email-based login with password hashing using bcrypt
- **Session Management:** PHP session-based authentication with secure session handling
- **Authorization:** Role-based access control (RBAC) implemented at controller and view levels
- **Password Security:** Bcrypt hashing algorithm with cost factor 10
- **User Registration:** Separate registration flows for customers and sellers
- **Seller Verification:** Admin approval workflow for seller applications

**Tools & Techniques:**
- PHP Session Management (`session_start()`)
- Bcrypt Password Hashing (`password_hash()`, `password_verify()`)
- Role-based Access Control (RBAC) pattern
- Database-driven role management (`roles` table)

**Implementation Files:**
- `classes/customer_class.php` - Customer authentication logic
- `classes/seller_class.php` - Seller authentication logic
- `actions/login_customer_action.php` - Login processing
- `actions/register_customer_action.php` - Registration processing
- `settings/core.php` - Role constants and session initialization

---

### 2. Product Management System

**Requirement:** The system must provide comprehensive product management capabilities for sellers, including CRUD operations, image uploads, categorization, and inventory tracking.

**Details:**
- **Product Creation:** Sellers can create products with title, description, price, category, brand, condition, keywords, and images
- **Product Categories:** Hierarchical category system with user-specific categories
- **Product Brands:** Brand management per seller
- **Product Conditions:** Enum-based condition system (new, like-new, good, fair)
- **Product Status:** Status management (active, inactive, sold, hidden)
- **Image Management:** Multi-image upload system with organized file structure (`uploads/u{user_id}/p{product_id}/`)
- **Product Search:** Advanced search with keyword matching, category filtering, brand filtering, price range, and condition filtering
- **Product Organization:** Products organized by category and brand for easy navigation

**Tools & Techniques:**
- Object-Oriented Programming (OOP) - Product class extends Database
- SQL Query Optimization - Indexed columns for performance
- File Upload Handling - PHP `$_FILES` superglobal
- Full-Text Search - LIKE queries with optimized filter order
- Data Validation - Input sanitization and type checking

**Implementation Files:**
- `classes/product_class.php` - Product business logic (574 lines)
- `controllers/product_controller.php` - Product request handling
- `actions/add_product_action.php` - Product creation endpoint
- `actions/update_product_action.php` - Product update endpoint
- `actions/upload_product_image_action.php` - Image upload handler
- `actions/search_products_action.php` - Search functionality

**Database Tables:**
- `products` - Main product table
- `categories` - Product categories
- `brands` - Product brands
- Foreign key relationships with proper indexing

---

### 3. Shopping Cart and Checkout System

**Requirement:** The system must provide a persistent shopping cart that works for both logged-in users and guest users, with seamless checkout and order processing.

**Details:**
- **Cart Persistence:** Cart items stored in database with support for both logged-in users (by user_id) and guests (by IP address)
- **Cart Operations:** Add to cart, remove from cart, update quantity, empty cart
- **Cart Validation:** Product availability checking, price validation
- **Checkout Process:** Multi-step checkout with order creation, payment processing, and order confirmation
- **Order Management:** Order creation with order details, status tracking, and order history

**Tools & Techniques:**
- Session-based cart management
- Database-driven cart persistence
- Transaction management for order creation
- Foreign key constraints for data integrity

**Implementation Files:**
- `classes/cart_class.php` - Cart business logic
- `classes/order_class.php` - Order management (277 lines)
- `controllers/cart_controller.php` - Cart request handling
- `actions/add_to_cart_action.php` - Add item to cart
- `actions/get_cart_action.php` - Retrieve cart items
- `actions/process_checkout_action.php` - Checkout processing

**Database Tables:**
- `cart` - Shopping cart items
- `orders` - Order headers
- `orderdetails` - Order line items

---

### 4. Payment Processing System

**Requirement:** The system must integrate with Paystack payment gateway to process secure online payments in Ghanaian Cedis (GHS) with support for multiple payment methods.

**Details:**
- **Payment Gateway:** Paystack integration for mobile money (MTN, Vodafone, AirtelTigo) and card payments
- **Payment Methods:** Mobile Money (MoMo), Card payments, Bank transfers
- **Currency:** Ghanaian Cedis (GHS)
- **Transaction Tracking:** Unique transaction references, payment status tracking
- **Payment Verification:** Webhook/callback verification for payment confirmation
- **Payment Records:** Complete payment history stored in database

**Tools & Techniques:**
- RESTful API Integration - Paystack API
- cURL for HTTP requests
- Transaction reference generation
- Payment status enumeration (pending, successful, failed)
- Callback URL handling

**Implementation Files:**
- `settings/paystack_config.php` - Paystack configuration and helper functions
- `actions/paystack_init_transaction.php` - Initialize payment
- `actions/paystack_verify_payment.php` - Verify payment status
- `view/paystack_callback.php` - Payment callback handler
- `view/payment_success.php` - Payment success page

**Database Tables:**
- `payments` - Payment records with transaction references

**API Integration:**
- Paystack Transaction Initialize API
- Paystack Transaction Verify API
- Secure key management (secret and public keys)

---

### 5. Seller Management and Application System

**Requirement:** The system must provide a comprehensive seller onboarding process with application submission, admin review, and seller profile management.

**Details:**
- **Seller Application:** Sellers submit applications with business documentation (CSV format)
- **Application Status:** Three-state workflow (pending, approved, rejected)
- **Seller Profile:** Shop name, business type, sector, store logo, store banner, description
- **Seller Verification:** Admin-controlled verification badge system
- **Business Types:** Individual, Small Business, Organizational
- **Sectors:** Clothing, Electronics, Accessories, Home & Decor, Books
- **Seller Dashboard:** Comprehensive dashboard with product management, order management, and statistics

**Tools & Techniques:**
- File Upload System - Documentation upload
- Workflow Management - Application status tracking
- Admin Review System - Approval/rejection workflow
- Profile Management - Seller profile CRUD operations

**Implementation Files:**
- `classes/seller_class.php` - Seller business logic
- `classes/sellerApplication_class.php` - Application management
- `controllers/seller_controller.php` - Seller request handling
- `actions/apply_seller_action.php` - Submit application
- `actions/approve_seller.php` - Admin approval
- `actions/reject_seller.php` - Admin rejection
- `seller/seller_dashboard.php` - Seller dashboard interface

**Database Tables:**
- `sellers` - Seller profiles
- `seller_applications` - Application records
- `business_types` - Business type lookup
- `sectors` - Sector lookup

---

### 6. Real-Time Messaging and Communication System

**Requirement:** The system must provide a messaging system that enables buyers and sellers to communicate about products, negotiate prices, and discuss order details.

**Details:**
- **Conversation Management:** Create and manage conversations between buyers and sellers
- **Message Threading:** Messages organized by conversation with chronological ordering
- **Read/Unread Status:** Message read status tracking
- **Product Context:** Conversations can be linked to specific products
- **Inbox Management:** User inbox showing all conversations with last message preview
- **Unread Count:** Real-time unread message count for notifications
- **Message History:** Complete message history per conversation

**Tools & Techniques:**
- Relational Database Design - Conversations and messages tables
- Timestamp Management - Created_at and updated_at tracking
- SQL JOIN Operations - Efficient data retrieval
- Real-time Updates - AJAX-based message fetching

**Implementation Files:**
- `classes/chat_class.php` - Messaging business logic (264 lines)
- `controllers/chat_controller.php` - Chat request handling
- `actions/start_conversation.php` - Create conversation
- `actions/send_message.php` - Send message
- `actions/fetch_messages.php` - Retrieve messages
- `actions/fetch_inbox.php` - Get user inbox
- `view/chat.php` - Chat interface
- `view/chat_inbox.php` - Inbox interface

**Database Tables:**
- `conversations` - Conversation headers
- `messages` - Individual messages
- Foreign keys linking to users and products

---

### 7. Product Recommendation Engine

**Requirement:** The system must provide intelligent product recommendations using multiple algorithms to enhance user experience and increase sales.

**Details:**
- **Content-Based Recommendations:** TF-IDF (Term Frequency-Inverse Document Frequency) algorithm for similarity matching
- **Collaborative Filtering:** "Also Bought" recommendations based on purchase patterns
- **Trending Products:** Popularity-based recommendations using sales volume
- **User-Personalized Recommendations:** Category affinity-based recommendations from purchase history
- **Vector Similarity:** Cosine similarity calculation for content-based matching
- **Caching:** In-memory caching of TF-IDF vectors for performance (5-minute TTL)

**Tools & Techniques:**
- TF-IDF Algorithm - Text analysis and vectorization
- Cosine Similarity - Vector similarity calculation
- SQL Aggregation - Sales volume calculations
- Caching Strategy - In-memory vector caching
- Tokenization - Text preprocessing

**Implementation Files:**
- `classes/recommendation_class.php` - Recommendation algorithms (237 lines)
- `controllers/recommendation_controller.php` - Recommendation request handling
- `actions/get_similar_products_action.php` - Similar products
- `actions/get_also_bought_action.php` - Also bought recommendations
- `actions/get_trending_products_action.php` - Trending products
- `actions/get_user_recommendations_action.php` - Personalized recommendations

**Algorithms:**
1. **TF-IDF Vectorization:** Converts product text (title, keywords, description) into numerical vectors
2. **Cosine Similarity:** Calculates similarity between product vectors
3. **Purchase Pattern Analysis:** Identifies frequently co-purchased items
4. **Category Affinity:** Analyzes user purchase history to recommend similar categories

---

### 8. Admin Dashboard and Management System

**Requirement:** The system must provide a comprehensive admin dashboard for managing users, sellers, products, orders, and system-wide statistics.

**Details:**
- **Seller Application Review:** Approve or reject seller applications
- **System Statistics:** Dashboard with key metrics (users, products, orders, revenue)
- **User Management:** View and manage all users
- **Product Oversight:** View all products across all sellers
- **Order Management:** View and manage all orders
- **Category Management:** System-wide category management
- **Brand Management:** System-wide brand oversight

**Tools & Techniques:**
- Dashboard Analytics - SQL aggregation queries
- Admin Interface - Role-based UI rendering
- Statistics Calculation - Real-time metric computation

**Implementation Files:**
- `admin/admin_dashboard.php` - Main admin dashboard
- `admin/pending_seller_applications.php` - Application review interface
- `actions/get_admin_stats.php` - Statistics endpoint
- `actions/approve_seller.php` - Approval action
- `actions/reject_seller.php` - Rejection action
- `controllers/admin_controller.php` - Admin request handling

**Database Queries:**
- Aggregation queries for statistics
- JOIN operations for comprehensive data retrieval
- Status-based filtering

---

### 9. Advanced Search and Filtering System

**Requirement:** The system must provide an advanced search system with multiple filtering options, optimized for performance and user experience.

**Details:**
- **Keyword Search:** Full-text search across product titles, keywords, descriptions, brands, and categories
- **Multi-Filter Support:** Category, brand, price range, condition, and status filters
- **Search Optimization:** Indexed column filtering applied before text search for performance
- **Relevance Ranking:** Results ranked by relevance (exact matches first, then partial matches)
- **Pagination:** Limit and offset support for large result sets
- **Search Count:** Total count calculation for pagination

**Tools & Techniques:**
- SQL Query Optimization - Filter order optimization
- Index Utilization - Database index usage
- LIKE Queries - Pattern matching
- Word Tokenization - Multi-word search support
- Relevance Scoring - CASE statements for ranking

**Implementation Files:**
- `classes/product_class.php` - Search methods (searchProducts, searchProductsCount)
- `actions/search_products_action.php` - Search endpoint
- `actions/browse_products_action.php` - Browse with filters

**Performance Optimizations:**
1. **Indexed Filters First:** Apply indexed column filters (status, category, brand, price) before text search
2. **Word Splitting:** Split search terms into words for better matching
3. **Relevance Ordering:** Order results by match quality
4. **Efficient Counting:** Separate count query for pagination

---

### 10. Responsive User Interface and User Experience

**Requirement:** The system must provide a modern, responsive user interface that works seamlessly across desktop, tablet, and mobile devices with excellent user experience.

**Details:**
- **Responsive Design:** CSS Grid and Flexbox for responsive layouts
- **Modern UI/UX:** Clean, modern design with custom color scheme (Thrift Green, Beige, Gold)
- **Interactive Elements:** Hover effects, transitions, and animations
- **Font Integration:** Google Fonts (Inter, Playfair Display)
- **Icon System:** Font Awesome 6.6.0 for icons
- **Alert System:** SweetAlert2 for user notifications
- **Loading States:** Loading indicators for async operations
- **Error Handling:** User-friendly error messages
- **Accessibility:** Semantic HTML and ARIA considerations

**Tools & Techniques:**
- CSS3 - Modern styling with variables, gradients, shadows
- JavaScript (ES6+) - Modern JavaScript for interactivity
- AJAX - Asynchronous data fetching
- Responsive Design - Mobile-first approach
- CSS Variables - Theme management
- Font Awesome - Icon library
- SweetAlert2 - Alert library

**Implementation Files:**
- `view/*.php` - All view files with embedded CSS
- `assets/js/*.js` - JavaScript modules
- `index.php` - Homepage with comprehensive styling

**Design System:**
- **Color Palette:**
  - Primary: Thrift Green (#0F5E4D)
  - Secondary: Beige (#F6F2EA)
  - Accent: Gold (#C9A961)
  - Text: Dark (#2C2C2C), Muted (#6B6B6B)
- **Typography:** Inter (body), Playfair Display (headings)
- **Spacing:** Consistent padding and margins
- **Shadows:** Multiple shadow levels for depth
- **Border Radius:** Rounded corners (8px-20px)

---

## Tools and Technologies

### Backend Technologies

1. **PHP 8.0.30**
   - Server-side scripting language
   - Object-oriented programming support
   - Session management
   - File handling

2. **MySQLi Extension**
   - Database connectivity
   - Prepared statements support
   - Transaction support
   - Error handling

3. **MariaDB 10.4.32 / MySQL**
   - Relational database management system
   - InnoDB storage engine
   - Foreign key constraints
   - Index optimization

### Frontend Technologies

1. **HTML5**
   - Semantic markup
   - Form validation
   - Accessibility features

2. **CSS3**
   - CSS Variables for theming
   - Flexbox and Grid layouts
   - Animations and transitions
   - Responsive media queries

3. **JavaScript (ES6+)**
   - Modern JavaScript features
   - Async/await for asynchronous operations
   - Fetch API for AJAX requests
   - DOM manipulation

4. **External Libraries:**
   - **Font Awesome 6.6.0** - Icon library
   - **SweetAlert2** - Alert/notification system
   - **Google Fonts** - Typography (Inter, Playfair Display)

### Payment Integration

1. **Paystack API**
   - Payment gateway integration
   - Mobile money support
   - Card payment processing
   - Webhook handling

### Development Tools

1. **XAMPP**
   - Local development environment
   - Apache web server
   - MySQL/MariaDB database
   - PHP runtime

2. **phpMyAdmin 5.2.1**
   - Database management interface
   - SQL query execution
   - Database structure management

### Architecture Patterns

1. **MVC (Model-View-Controller)**
   - Separation of concerns
   - Maintainable code structure
   - Reusable components

2. **Object-Oriented Programming (OOP)**
   - Class-based architecture
   - Inheritance (classes extend Database)
   - Encapsulation
   - Code reusability

3. **RESTful API Design**
   - Action-based endpoints
   - JSON response format
   - HTTP method usage

---

## System Architecture

### Architectural Pattern: MVC (Model-View-Controller)

```
┌─────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │   Views      │  │   Actions   │  │  JavaScript   │       │
│  │  (PHP/HTML)  │  │  (Endpoints) │  │   (Frontend)  │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                       APPLICATION LAYER                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │ Controllers  │  │   Classes    │  │   Business    │       │
│  │              │  │   (Models)   │  │    Logic      │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                        DATA LAYER                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │  Database    │  │   Database   │  │   File        │       │
│  │   Class      │  │  (MariaDB)   │  │   Storage    │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
└─────────────────────────────────────────────────────────────┘
```

### Directory Structure

```
ThriftHub/
├── actions/              # API endpoints (RESTful actions)
├── admin/               # Admin interface pages
├── assets/              # Static assets
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── images/         # Image assets
├── classes/             # Business logic classes (Models)
├── controllers/         # Request handlers (Controllers)
├── db/                  # Database files
│   └── thrifthub.sql   # Database schema
├── functions/           # Utility functions
├── seller/              # Seller interface pages
├── settings/            # Configuration files
│   ├── core.php        # Core initialization
│   ├── db_class.php    # Database class
│   ├── db_cred.php     # Database credentials
│   └── paystack_config.php # Payment config
├── uploads/             # User-uploaded files
│   └── u{user_id}/     # User-specific uploads
│       ├── docs/       # Documentation
│       ├── p{product_id}/ # Product images
│       └── store/      # Store assets
└── view/                # Public-facing pages (Views)
```

### Data Flow Architecture

```
User Request
    │
    ▼
View (PHP/HTML)
    │
    ▼
Action Endpoint (actions/*.php)
    │
    ▼
Controller (controllers/*.php)
    │
    ▼
Class/Model (classes/*.php)
    │
    ▼
Database Class (settings/db_class.php)
    │
    ▼
MariaDB Database
    │
    ▼
Response (JSON/HTML)
    │
    ▼
User Interface
```

---

## Database Design

### Entity-Relationship Diagram (Conceptual)

```
┌──────────┐         ┌──────────┐         ┌──────────┐
│  Users   │────────▶│  Roles   │         │ Customers│
└──────────┘         └──────────┘         └──────────┘
     │                     │                     │
     │                     │                     │
     │                     ▼                     │
     │              ┌──────────┐                │
     │              │  Sellers  │                │
     │              └──────────┘                │
     │                     │                       │
     │                     │                       │
     ▼                     ▼                       ▼
┌──────────┐         ┌──────────┐         ┌──────────┐
│ Products │────────▶│Categories │         │   Cart   │
└──────────┘         └──────────┘         └──────────┘
     │                     │                       │
     │                     │                       │
     │                     ▼                       │
     │              ┌──────────┐                  │
     │              │  Brands  │                  │
     │              └──────────┘                  │
     │                                             │
     ▼                                             ▼
┌──────────┐                                 ┌──────────┐
│  Orders  │─────────────────────────────────▶│OrderDetails│
└──────────┘                                 └──────────┘
     │
     ▼
┌──────────┐
│ Payments │
└──────────┘

┌──────────┐         ┌──────────┐
│Conversations│─────▶│ Messages │
└──────────┘         └──────────┘
```

### Database Tables

#### Core Tables

1. **users** (Primary Key: user_id)
   - Stores user account information
   - Links to roles table
   - Email uniqueness constraint

2. **roles** (Primary Key: role_no)
   - Role definitions (admin, customer, seller)

3. **customers** (Foreign Key: user_id → users)
   - Customer-specific data
   - Links users to customer role

4. **sellers** (Foreign Key: user_id → users)
   - Seller profiles
   - Shop information
   - Verification status

5. **products** (Primary Key: product_id)
   - Product catalog
   - Links to categories, brands, sellers
   - Product status management

6. **categories** (Primary Key: cat_id)
   - Product categorization
   - User-specific categories

7. **brands** (Primary Key: brand_id)
   - Product brands
   - User-specific brands

#### Transaction Tables

8. **cart** (Primary Key: cart_id)
   - Shopping cart items
   - Supports logged-in and guest users

9. **orders** (Primary Key: order_id)
   - Order headers
   - Order status tracking

10. **orderdetails** (Primary Key: orderdetail_id)
    - Order line items
    - Product quantities and prices

11. **payments** (Primary Key: pay_id)
    - Payment records
    - Transaction references
    - Payment status

#### Communication Tables

12. **conversations** (Primary Key: conversation_id)
    - Buyer-seller conversations
    - Product context

13. **messages** (Primary Key: message_id)
    - Individual messages
    - Read/unread status

#### Supporting Tables

14. **seller_applications** (Primary Key: application_id)
    - Seller application records
    - Application status workflow

15. **business_types** (Primary Key: type_id)
    - Business type lookup

16. **sectors** (Primary Key: sector_id)
    - Business sector lookup

### Database Relationships

- **One-to-Many:**
  - User → Products (seller)
  - User → Orders (customer)
  - Order → OrderDetails
  - Conversation → Messages

- **Many-to-Many:**
  - Products ↔ Categories (via product_cat)
  - Products ↔ Brands (via product_brand)

- **Foreign Key Constraints:**
  - CASCADE on DELETE for dependent records
  - SET NULL for optional relationships
  - UPDATE CASCADE for referential integrity

### Indexing Strategy

**Primary Indexes:**
- All primary keys automatically indexed

**Foreign Key Indexes:**
- `products.product_cat` → `categories.cat_id`
- `products.product_brand` → `brands.brand_id`
- `products.seller_id` → `sellers.user_id`
- `orders.customer_id` → `users.user_id`
- `orderdetails.order_id` → `orders.order_id`
- `orderdetails.product_id` → `products.product_id`

**Performance Indexes:**
- `products.product_status` - For filtering active products
- `messages.created_at` - For chronological ordering
- `orders.order_date` - For date-based queries
- `cart.c_id` - For user cart retrieval

---

## Functional Modules

### Module 1: User Management Module

**Components:**
- User Registration
- User Authentication
- Session Management
- Role Management
- Password Management

**Key Features:**
- Email-based registration
- Secure password hashing
- Session-based authentication
- Role-based access control

### Module 2: Product Management Module

**Components:**
- Product CRUD Operations
- Category Management
- Brand Management
- Image Upload System
- Product Search

**Key Features:**
- Multi-image product uploads
- Category and brand organization
- Advanced search with filters
- Product status management

### Module 3: Shopping Cart Module

**Components:**
- Add to Cart
- Update Cart
- Remove from Cart
- Cart Persistence
- Cart Validation

**Key Features:**
- Guest and logged-in user support
- Quantity management
- Real-time cart updates
- Cart persistence across sessions

### Module 4: Order Management Module

**Components:**
- Order Creation
- Order Processing
- Order Status Tracking
- Order History
- Order Details

**Key Features:**
- Multi-item orders
- Order status workflow
- Order history for customers
- Seller order management

### Module 5: Payment Processing Module

**Components:**
- Payment Initialization
- Payment Verification
- Payment Status Tracking
- Transaction Management

**Key Features:**
- Paystack integration
- Multiple payment methods
- Transaction reference generation
- Payment callback handling

### Module 6: Seller Management Module

**Components:**
- Seller Application
- Application Review
- Seller Profile Management
- Seller Dashboard
- Seller Verification

**Key Features:**
- Application workflow
- Admin approval system
- Seller profile customization
- Seller statistics

### Module 7: Messaging Module

**Components:**
- Conversation Management
- Message Sending
- Message Retrieval
- Inbox Management
- Read Status Tracking

**Key Features:**
- Real-time messaging
- Product-context conversations
- Unread message tracking
- Message history

### Module 8: Recommendation Module

**Components:**
- Content-Based Recommendations
- Collaborative Filtering
- Trending Products
- User Personalization

**Key Features:**
- TF-IDF algorithm
- Cosine similarity
- Purchase pattern analysis
- Category affinity

### Module 9: Admin Module

**Components:**
- Admin Dashboard
- Seller Application Review
- System Statistics
- User Management
- Product Oversight

**Key Features:**
- Comprehensive statistics
- Application approval workflow
- System-wide management
- Analytics dashboard

### Module 10: Search and Discovery Module

**Components:**
- Keyword Search
- Advanced Filtering
- Search Optimization
- Relevance Ranking

**Key Features:**
- Multi-field search
- Filter combination
- Performance optimization
- Relevance scoring

---

## Design Patterns and Techniques

### 1. Model-View-Controller (MVC) Pattern

**Implementation:**
- **Models:** Classes in `classes/` directory (Product, Order, Cart, etc.)
- **Views:** PHP files in `view/` directory
- **Controllers:** Classes in `controllers/` directory

**Benefits:**
- Separation of concerns
- Maintainability
- Testability
- Code reusability

### 2. Repository Pattern

**Implementation:**
- Database class acts as repository
- Classes extend Database for data access
- Centralized database operations

### 3. Singleton Pattern (Implicit)

**Implementation:**
- Database connection instantiated once in `core.php`
- Global `$db` object

### 4. Factory Pattern (Implicit)

**Implementation:**
- Controller instantiation in action files
- Class instantiation based on request

### 5. Strategy Pattern

**Implementation:**
- Multiple recommendation algorithms
- Different search strategies
- Payment method selection

### 6. Observer Pattern (Implicit)

**Implementation:**
- Session-based state management
- Event-driven updates

### 7. Template Method Pattern

**Implementation:**
- Consistent CRUD operations across classes
- Standardized error handling

### 8. Data Access Object (DAO) Pattern

**Implementation:**
- Database class abstracts data access
- Classes provide data access methods

---

## Security Design

### 1. Authentication Security

- **Password Hashing:** Bcrypt with cost factor 10
- **Session Security:** PHP session management
- **Session Regeneration:** Session ID regeneration on login
- **Session Timeout:** PHP session timeout configuration

### 2. Authorization Security

- **Role-Based Access Control:** Three-tier role system
- **Permission Checking:** Role verification at controller level
- **Resource Ownership:** Seller ownership verification for products

### 3. Input Validation and Sanitization

- **SQL Injection Prevention:** Prepared statements and escaping
- **XSS Prevention:** HTML escaping in output
- **Input Type Validation:** Type checking and validation
- **File Upload Security:** File type validation, size limits

### 4. Data Protection

- **SQL Injection Prevention:**
  - `mysqli_real_escape_string()` for escaping
  - Type casting for numeric inputs
  - Parameterized queries (prepared statements ready)

- **XSS Prevention:**
  - HTML entity encoding
  - `escapeHtml()` function in JavaScript
  - Output sanitization

### 5. Payment Security

- **Secure API Keys:** Paystack keys stored in configuration
- **Transaction Verification:** Payment verification before order completion
- **Transaction References:** Unique reference generation

### 6. File Upload Security

- **File Type Validation:** Image type checking
- **File Size Limits:** Upload size restrictions
- **Directory Structure:** Organized, user-specific directories
- **File Naming:** Unique file naming to prevent conflicts

### 7. Session Security

- **Session Configuration:** Secure session settings
- **Session Regeneration:** ID regeneration on privilege changes
- **Session Validation:** Session validation on each request

---

## Performance Optimization

### 1. Database Optimization

- **Indexing:** Strategic index placement on frequently queried columns
- **Query Optimization:** Filter order optimization (indexed columns first)
- **Connection Pooling:** Single database connection instance
- **Query Caching:** Recommendation vector caching

### 2. Application Optimization

- **Code Organization:** Modular, reusable code
- **Lazy Loading:** Recommendation vectors built on demand
- **Caching:** In-memory caching for TF-IDF vectors (5-minute TTL)
- **Efficient Algorithms:** Optimized search and recommendation algorithms

### 3. Frontend Optimization

- **Minification:** CSS and JavaScript minification (production)
- **Image Optimization:** Image compression and proper sizing
- **Lazy Loading:** Deferred image loading
- **CDN Usage:** External libraries from CDN

### 4. Search Optimization

- **Indexed Filters First:** Apply indexed column filters before text search
- **Relevance Ranking:** Efficient CASE-based ranking
- **Pagination:** Limit and offset for large result sets
- **Count Optimization:** Separate count queries

### 5. Recommendation Optimization

- **Vector Caching:** TF-IDF vectors cached in memory
- **TTL Management:** 5-minute cache expiration
- **Efficient Similarity:** Optimized cosine similarity calculation
- **Lazy Building:** Vectors built only when needed

---

## System Deployment

### Development Environment

- **Server:** XAMPP (Apache + MariaDB + PHP)
- **PHP Version:** 8.0.30
- **Database:** MariaDB 10.4.32
- **Web Server:** Apache (via XAMPP)

### Production Deployment Considerations

1. **Server Requirements:**
   - PHP 8.0+ with MySQLi extension
   - MariaDB/MySQL 10.4+
   - Apache/Nginx web server
   - SSL certificate for HTTPS

2. **Configuration:**
   - Update `db_cred.php` with production credentials
   - Update `paystack_config.php` with production keys
   - Configure `APP_BASE_URL` for production domain
   - Set proper file permissions for `uploads/` directory

3. **Security Hardening:**
   - Disable error display in production
   - Enable error logging
   - Configure secure session settings
   - Implement HTTPS
   - Regular security updates

4. **Performance:**
   - Enable PHP OPcache
   - Configure database connection pooling
   - Implement CDN for static assets
   - Enable gzip compression

5. **Backup Strategy:**
   - Regular database backups
   - File upload backups
   - Configuration file backups

---

## Conclusion

ThriftHub is a comprehensive, well-architected e-commerce platform that successfully implements modern web development practices, secure payment processing, intelligent recommendations, and a user-friendly interface. The system's modular design, use of established patterns, and focus on performance and security make it a robust solution for the Ghanaian thrift marketplace.

The platform successfully addresses all 10 core requirements through careful system design, appropriate technology selection, and implementation of best practices in web development, database design, and security.

---

**Document Version:** 1.0  
**Last Updated:** November 2025  
**Author:** System Analysis Team  
**Status:** Complete

