# MVC System Architecture Diagram
## ThriftHub E-Commerce Platform

---

## Complete MVC Architecture Diagram (ASCII)

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              THRIFTHUB MVC ARCHITECTURE                                  │
│                         Model-View-Controller Pattern Implementation                     │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                    PRESENTATION LAYER (VIEW)                            │
│                                    User Interface Components                            │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────┐  ┌──────────────────────┐  ┌──────────────────────┐
│   PUBLIC VIEWS        │  │   SELLER VIEWS        │  │   ADMIN VIEWS         │
├──────────────────────┤  ├──────────────────────┤  ├──────────────────────┤
│ index.php            │  │ seller_dashboard.php │  │ admin_dashboard.php  │
│ browse_products.php  │  │ product.php          │  │ pending_seller_      │
│ single_product.php   │  │ category.php         │  │   applications.php   │
│ cart.php             │  │ brand.php            │  └──────────────────────┘
│ checkout.php         │  └──────────────────────┘
│ login.php            │
│ register.php         │
│ about.php            │
│ chat.php             │
│ chat_inbox.php       │
│ view_orders.php      │
│ payment_success.php   │
│ paystack_callback.php│
└──────────┬───────────┘
           │
           │ User Interaction
           │ HTTP Requests (GET/POST)
           │
           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              ACTION LAYER (API Endpoints)                                │
│                         RESTful Action Handlers / Routes                                │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────┐
│  ACTIONS DIRECTORY (actions/*.php)                                                       │
├──────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                           │
│  ┌─────────────────────┐  ┌─────────────────────┐  ┌─────────────────────┐            │
│  │ Product Actions     │  │ Cart Actions        │  │ Order Actions       │            │
│  ├─────────────────────┤  ├─────────────────────┤  ├─────────────────────┤            │
│  │ add_product_action  │  │ add_to_cart_action  │  │ process_checkout_   │            │
│  │ update_product_     │  │ get_cart_action     │  │   action            │            │
│  │   action            │  │ remove_from_cart_   │  │ get_customer_orders_│            │
│  │ delete_product_     │  │   action            │  │   action            │            │
│  │   action            │  │ update_quantity_   │  │ update_order_status │            │
│  │ get_product_action  │  │   action            │  └─────────────────────┘            │
│  │ get_products_action │  │ empty_cart_action   │                                      │
│  │ search_products_    │  └─────────────────────┘  ┌─────────────────────┐            │
│  │   action            │                           │ Payment Actions     │            │
│  │ get_public_product_│  ┌─────────────────────┐  ├─────────────────────┤            │
│  │   action            │  │ User Actions        │  │ paystack_init_      │            │
│  │ browse_products_    │  ├─────────────────────┤  │   transaction       │            │
│  │   action            │  │ login_customer_     │  │ paystack_verify_    │            │
│  │ upload_product_     │  │   action            │  │   payment           │            │
│  │   image_action      │  │ register_customer_   │  └─────────────────────┘            │
│  └─────────────────────┘  │   action            │                                      │
│                            │ logout              │  ┌─────────────────────┐            │
│  ┌─────────────────────┐  └─────────────────────┘  │ Chat Actions        │            │
│  │ Category Actions    │                           ├─────────────────────┤            │
│  ├─────────────────────┤  ┌─────────────────────┐  │ start_conversation  │            │
│  │ add_category_action │  │ Seller Actions      │  │ send_message        │            │
│  │ update_category_    │  ├─────────────────────┤  │ fetch_messages      │            │
│  │   action            │  │ apply_seller_action │  │ fetch_inbox         │            │
│  │ delete_category_    │  │ approve_seller      │  │ start_support_chat  │            │
│  │   action            │  │ reject_seller       │  └─────────────────────┘            │
│  │ fetch_categories_   │  │ update_seller_      │                                      │
│  │   action            │  │   profile_action   │  ┌─────────────────────┐            │
│  │ fetch_category_     │  │ get_seller_products │  │ Recommendation      │            │
│  │   action            │  │ get_seller_orders   │  │   Actions            │            │
│  └─────────────────────┘  │ get_seller_dashboard│  ├─────────────────────┤            │
│                            │   _stats             │  │ get_similar_products│            │
│  ┌─────────────────────┐  └─────────────────────┘  │   _action            │            │
│  │ Brand Actions       │                           │ get_also_bought_    │            │
│  ├─────────────────────┤  ┌─────────────────────┐  │   action            │            │
│  │ add_brand_action    │  │ Admin Actions       │  │ get_trending_       │            │
│  │ update_brand_action │  ├─────────────────────┤  │   products_action    │            │
│  │ delete_brand_action │  │ get_admin_stats     │  │ get_user_           │            │
│  │ fetch_brand_action   │  └─────────────────────┘  │   recommendations_  │            │
│  │ get_brands_for_      │                           │   action            │            │
│  │   product_action     │                           └─────────────────────┘            │
│  │ get_public_brands_   │                                                                 │
│  │   action             │                                                                 │
│  └─────────────────────┘                                                                 │
└──────────────────────────────────────────────────────────────────────────────────────────┘
           │
           │ Calls Controller Methods
           │ Passes Request Data
           │
           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                           APPLICATION LAYER (CONTROLLER)                                  │
│                         Request Processing & Business Logic                              │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────┐
│  CONTROLLERS DIRECTORY (controllers/*.php)                                               │
├──────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ ProductController                                                    │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addProduct($data)                                                  │                │
│  │ + updateProduct($productId, $data)                                   │                │
│  │ + deleteProduct($productId, $data)                                   │                │
│  │ + getAllProducts($filters)                                           │                │
│  │ + searchProducts($searchTerm, $filters)                              │                │
│  │ + getProduct($productId)                                             │                │
│  │ + getProductsOrganized()                                             │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ CartController                                                       │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addToCart($data)                                                   │                │
│  │ + getCart()                                                          │                │
│  │ + updateQuantity($cartId, $quantity)                                │                │
│  │ + removeFromCart($cartId)                                            │                │
│  │ + emptyCart()                                                        │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ OrderController                                                      │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + createOrder($data)                                                 │                │
│  │ + getCustomerOrders($customerId)                                    │                │
│  │ + updateOrderStatus($orderId, $status)                               │                │
│  │ + getSellerOrders($sellerId)                                        │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ CustomerController                                                   │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + register($data)                                                    │                │
│  │ + login($email, $password)                                          │                │
│  │ + logout()                                                           │                │
│  │ + getCustomerProfile($customerId)                                    │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ SellerController                                                     │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + applySeller($data)                                                 │                │
│  │ + updateSellerProfile($sellerId, $data)                              │                │
│  │ + getSellerDashboard($sellerId)                                     │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ CategoryController                                                   │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addCategory($data)                                                 │                │
│  │ + updateCategory($categoryId, $data)                                │                │
│  │ + deleteCategory($categoryId)                                        │                │
│  │ + getAllCategories($userId)                                        │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ BrandController                                                      │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addBrand($data)                                                    │                │
│  │ + updateBrand($brandId, $data)                                      │                │
│  │ + deleteBrand($brandId)                                              │                │
│  │ + getAllBrands($userId)                                              │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ ChatController                                                       │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + createConversation($buyerId, $sellerId, $productId)               │                │
│  │ + sendMessage($conversationId, $senderId, $receiverId, $message)  │                │
│  │ + getMessages($conversationId)                                      │                │
│  │ + getUserConversations($userId)                                     │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ RecommendationController                                             │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + getSimilarProducts($productId, $limit)                           │                │
│  │ + getTrendingProducts($limit, $days)                                │                │
│  │ + getAlsoBought($productId, $limit)                                 │                │
│  │ + getUserRecommendations($customerId, $limit)                      │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ AdminController                                                      │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + getAdminStats()                                                    │                │
│  │ + approveSeller($applicationId)                                     │                │
│  │ + rejectSeller($applicationId)                                      │                │
│  │ + getAllUsers()                                                      │                │
│  │ + getAllProducts()                                                   │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
└──────────────────────────────────────────────────────────────────────────────────────────┘
           │
           │ Instantiates & Calls Model Methods
           │ Validates Input
           │ Processes Business Logic
           │
           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              DATA LAYER (MODEL)                                          │
│                         Business Logic & Data Access                                    │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────┐
│  CLASSES DIRECTORY (classes/*.php)                                                        │
│  All classes extend Database class                                                       │
├──────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Database (settings/db_class.php) - Base Class                       │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + query($sql)                                                       │                │
│  │ + fetchOne($sql)                                                    │                │
│  │ + fetchAll($sql)                                                    │                │
│  │ + escape($data)                                                     │                │
│  │ + insert_id()                                                       │                │
│  │ + getConnection()                                                  │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                              ▲                                                           │
│                              │ Inheritance                                              │
│                              │                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Product extends Database                                             │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addProduct($data)                                                  │                │
│  │ + editProduct($productId, $data, $sellerId)                        │                │
│  │ + deleteProduct($productId, $sellerId)                             │                │
│  │ + getProduct($productId)                                            │                │
│  │ + getAllProducts($sellerId, $categoryId, $brandId, ...)           │                │
│  │ + searchProducts($searchTerm, $filters)                           │                │
│  │ + searchProductsCount($searchTerm, $filters)                      │                │
│  │ + getProductsBySeller($sellerId)                                   │                │
│  │ + getProductsOrganized($sellerId)                                   │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Cart extends Database                                               │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addToCart($productId, $quantity, $customerId, $ipAddress)        │                │
│  │ + getCart($customerId, $ipAddress)                                │                │
│  │ + updateQuantity($cartId, $quantity)                              │                │
│  │ + removeFromCart($cartId)                                          │                │
│  │ + emptyCart($customerId, $ipAddress)                              │                │
│  │ + getCartCount($customerId, $ipAddress)                           │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Order extends Database                                              │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + createOrder($customerId, $totalAmount, $orderStatus)            │                │
│  │ + addOrderDetails($orderId, $productId, $quantity, $price)        │                │
│  │ + recordPayment($amount, $customerId, $orderId, ...)               │                │
│  │ + getPastOrders($customerId, $limit, $offset)                     │                │
│  │ + getOrderById($orderId, $customerId)                              │                │
│  │ + updateOrderStatus($orderId, $status)                            │                │
│  │ + getSellerOrders($sellerId, $limit, $offset)                     │                │
│  │ + generateOrderReference()                                         │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Customer extends Database                                            │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + register($name, $email, $password, $phone)                       │                │
│  │ + login($email, $password)                                          │                │
│  │ + getCustomer($customerId)                                         │                │
│  │ + updateCustomer($customerId, $data)                              │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Seller extends Database                                             │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + applySeller($userId, $data)                                      │                │
│  │ + getSeller($sellerId)                                              │                │
│  │ + updateSellerProfile($sellerId, $data)                            │                │
│  │ + getSellerStats($sellerId)                                        │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Category extends Database                                            │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addCategory($userId, $categoryName)                               │                │
│  │ + updateCategory($categoryId, $categoryName)                      │                │
│  │ + deleteCategory($categoryId)                                      │                │
│  │ + getCategory($categoryId)                                         │                │
│  │ + getAllCategories($userId)                                       │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Brand extends Database                                              │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + addBrand($userId, $brandName)                                    │                │
│  │ + updateBrand($brandId, $brandName)                                │                │
│  │ + deleteBrand($brandId)                                             │                │
│  │ + getBrand($brandId)                                                │                │
│  │ + getAllBrands($userId)                                             │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Chat extends Database                                               │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + create_conversation($buyerId, $sellerId, $productId)             │                │
│  │ + get_conversation($buyerId, $sellerId, $productId)                │                │
│  │ + get_conversation_by_id($conversationId)                          │                │
│  │ + create_message($conversationId, $senderId, $receiverId, $text)  │                │
│  │ + get_messages_by_conversation($conversationId)                   │                │
│  │ + get_user_conversations($userId)                                  │                │
│  │ + mark_messages_as_read($conversationId, $userId)                │                │
│  │ + get_unread_count($userId)                                        │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Recommendation extends Database                                      │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + getSimilarProducts($productId, $limit)                           │                │
│  │ + getTrendingProducts($limit, $days)                                │                │
│  │ + getAlsoBought($productId, $limit)                                 │                │
│  │ + getUserRecommendations($customerId, $limit)                      │                │
│  │ - buildVectorsIfNeeded() [private]                                  │                │
│  │ - cosineSimilarity($v1, $v2) [private]                             │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ SellerApplication extends Database                                   │                │
│  ├─────────────────────────────────────────────────────────────────────┤                │
│  │ + submitApplication($userId, $documentationPath)                   │                │
│  │ + getApplication($applicationId)                                    │                │
│  │ + updateApplicationStatus($applicationId, $status, $reviewedBy)    │                │
│  │ + getPendingApplications()                                          │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
└──────────────────────────────────────────────────────────────────────────────────────────┘
           │
           │ Executes SQL Queries
           │ Performs Data Operations
           │ Returns Results
           │
           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              DATA STORAGE LAYER                                          │
│                         Database & File System                                          │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────┐
│  DATABASE (MariaDB/MySQL)                                                                │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ Tables:                                                              │                │
│  │ - users, roles, customers, sellers                                  │                │
│  │ - products, categories, brands                                      │                │
│  │ - cart, orders, orderdetails, payments                              │                │
│  │ - conversations, messages                                            │                │
│  │ - seller_applications, business_types, sectors                       │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
│                                                                                           │
│  FILE SYSTEM                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐                │
│  │ uploads/                                                             │                │
│  │   ├── u{user_id}/                                                    │                │
│  │   │   ├── docs/ (documentation)                                      │                │
│  │   │   ├── p{product_id}/ (product images)                           │                │
│  │   │   └── store/ (store logos/banners)                               │                │
│  └─────────────────────────────────────────────────────────────────────┘                │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## Request Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              REQUEST FLOW IN MVC PATTERN                                 │
└─────────────────────────────────────────────────────────────────────────────────────────┘

USER ACTION (Browser)
    │
    │ HTTP Request (GET/POST)
    │ URL: /actions/add_to_cart_action.php
    │ Data: {product_id: 5, quantity: 2}
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  VIEW LAYER                                             │
│  - User clicks "Add to Cart" button                    │
│  - JavaScript sends AJAX request                       │
│  - Displays loading state                              │
└─────────────────────────────────────────────────────────┘
    │
    │ POST Request
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  ACTION LAYER                                           │
│  actions/add_to_cart_action.php                         │
│  - Receives POST data                                  │
│  - Validates session                                    │
│  - Extracts parameters                                  │
│  - Instantiates Controller                              │
└─────────────────────────────────────────────────────────┘
    │
    │ $controller->addToCart($data)
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  CONTROLLER LAYER                                       │
│  controllers/cart_controller.php                      │
│  - Validates input data                                 │
│  - Checks user authentication                           │
│  - Processes business rules                             │
│  - Calls Model methods                                  │
└─────────────────────────────────────────────────────────┘
    │
    │ $cart->addToCart($productId, $quantity, ...)
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  MODEL LAYER                                            │
│  classes/cart_class.php                                 │
│  - Validates product exists                            │
│  - Checks stock availability                            │
│  - Builds SQL query                                     │
│  - Executes database operation                          │
└─────────────────────────────────────────────────────────┘
    │
    │ SQL: INSERT INTO cart ...
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  DATABASE LAYER                                         │
│  settings/db_class.php                                  │
│  - Executes SQL query                                   │
│  - Returns result                                       │
└─────────────────────────────────────────────────────────┘
    │
    │ Result: {success: true, cart_id: 123}
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  MODEL LAYER                                            │
│  - Returns result array                                 │
└─────────────────────────────────────────────────────────┘
    │
    │ Return: ['success' => true, 'message' => '...']
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  CONTROLLER LAYER                                       │
│  - Formats response                                     │
│  - Returns JSON                                         │
└─────────────────────────────────────────────────────────┘
    │
    │ JSON Response
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  ACTION LAYER                                           │
│  - Outputs JSON response                                │
│  - Sets HTTP headers                                    │
└─────────────────────────────────────────────────────────┘
    │
    │ HTTP Response (JSON)
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  VIEW LAYER                                             │
│  - JavaScript receives response                        │
│  - Updates UI (cart badge, success message)           │
│  - Refreshes cart display                              │
└─────────────────────────────────────────────────────────┘
    │
    │ User sees updated cart
    │
    ▼
USER SEES RESULT (Browser)
```

---

## MVC Component Interaction

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    MVC COMPONENT INTERACTION PATTERN                         │
└─────────────────────────────────────────────────────────────────────────────┘

                    ┌──────────────┐
                    │     USER     │
                    └──────┬───────┘
                           │
                           │ 1. User Action
                           │
                    ┌──────▼───────┐
                    │    VIEW      │
                    │  (PHP/HTML)  │
                    │              │
                    │ - Renders UI │
                    │ - Handles    │
                    │   Events     │
                    └──────┬───────┘
                           │
                           │ 2. HTTP Request
                           │
                    ┌──────▼──────────────┐
                    │      ACTION         │
                    │  (Endpoint/Router)  │
                    │                     │
                    │ - Receives Request  │
                    │ - Routes to         │
                    │   Controller        │
                    └──────┬──────────────┘
                           │
                           │ 3. Calls Controller
                           │
                    ┌──────▼──────────────┐
                    │    CONTROLLER       │
                    │  (Business Logic)   │
                    │                     │
                    │ - Validates Input   │
                    │ - Processes Rules   │
                    │ - Calls Model       │
                    └──────┬──────────────┘
                           │
                           │ 4. Calls Model
                           │
                    ┌──────▼──────────────┐
                    │      MODEL          │
                    │  (Data Access)      │
                    │                     │
                    │ - Database Queries  │
                    │ - Data Operations  │
                    │ - Returns Data      │
                    └──────┬──────────────┘
                           │
                           │ 5. SQL Query
                           │
                    ┌──────▼──────────────┐
                    │     DATABASE       │
                    │   (MariaDB/MySQL)  │
                    │                     │
                    │ - Stores Data      │
                    │ - Returns Results  │
                    └──────┬──────────────┘
                           │
                           │ 6. Data Result
                           │
                    ┌──────▼──────────────┐
                    │      MODEL          │
                    │ - Formats Data     │
                    └──────┬──────────────┘
                           │
                           │ 7. Returns Data
                           │
                    ┌──────▼──────────────┐
                    │    CONTROLLER       │
                    │ - Formats Response │
                    └──────┬──────────────┘
                           │
                           │ 8. Returns Response
                           │
                    ┌──────▼──────────────┐
                    │      ACTION         │
                    │ - Outputs JSON/HTML │
                    └──────┬──────────────┘
                           │
                           │ 9. HTTP Response
                           │
                    ┌──────▼───────┐
                    │    VIEW      │
                    │ - Updates UI │
                    │ - Shows Result│
                    └──────┬───────┘
                           │
                           │ 10. User Sees Result
                           │
                    ┌──────▼───────┐
                    │     USER     │
                    └─────────────┘
```

---

## Directory Structure in MVC Context

```
ThriftHub/
│
├── VIEW LAYER (Presentation)
│   ├── view/                    # Public-facing views
│   │   ├── index.php
│   │   ├── browse_products.php
│   │   ├── single_product.php
│   │   ├── cart.php
│   │   ├── checkout.php
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── chat.php
│   │   └── ...
│   │
│   ├── seller/                  # Seller views
│   │   ├── seller_dashboard.php
│   │   ├── product.php
│   │   ├── category.php
│   │   └── brand.php
│   │
│   ├── admin/                   # Admin views
│   │   ├── admin_dashboard.php
│   │   └── pending_seller_applications.php
│   │
│   └── assets/                  # Static assets
│       ├── css/
│       ├── js/
│       └── images/
│
├── ACTION LAYER (API Endpoints)
│   └── actions/                 # RESTful endpoints
│       ├── add_product_action.php
│       ├── add_to_cart_action.php
│       ├── login_customer_action.php
│       ├── process_checkout_action.php
│       └── ...
│
├── CONTROLLER LAYER (Business Logic)
│   └── controllers/            # Request handlers
│       ├── product_controller.php
│       ├── cart_controller.php
│       ├── order_controller.php
│       ├── customer_controller.php
│       ├── seller_controller.php
│       ├── category_controller.php
│       ├── brand_controller.php
│       ├── chat_controller.php
│       ├── recommendation_controller.php
│       └── admin_controller.php
│
├── MODEL LAYER (Data Access)
│   └── classes/                 # Business logic classes
│       ├── product_class.php
│       ├── cart_class.php
│       ├── order_class.php
│       ├── customer_class.php
│       ├── seller_class.php
│       ├── category_class.php
│       ├── brand_class.php
│       ├── chat_class.php
│       ├── recommendation_class.php
│       └── sellerApplication_class.php
│
└── DATA LAYER (Storage)
    ├── settings/
    │   ├── db_class.php         # Database connection
    │   ├── db_cred.php          # Database credentials
    │   └── core.php             # Core initialization
    │
    ├── db/
    │   └── thrifthub.sql        # Database schema
    │
    └── uploads/                  # File storage
        └── u{user_id}/
```

---

## MVC Separation of Concerns

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SEPARATION OF CONCERNS                                   │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│  VIEW (Presentation)                                                        │
│  ────────────────────────────────────────────────────────────────────────  │
│  Responsibilities:                                                          │
│  • Display data to user                                                    │
│  • Handle user input                                                        │
│  • Format output                                                            │
│  • UI/UX presentation                                                       │
│                                                                             │
│  Does NOT:                                                                  │
│  • Access database directly                                                 │
│  • Contain business logic                                                  │
│  • Process data                                                             │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│  CONTROLLER (Application Logic)                                             │
│  ────────────────────────────────────────────────────────────────────────  │
│  Responsibilities:                                                          │
│  • Process user requests                                                   │
│  • Validate input data                                                     │
│  • Coordinate between View and Model                                       │
│  • Handle business rules                                                   │
│  • Format responses                                                        │
│                                                                             │
│  Does NOT:                                                                  │
│  • Access database directly                                                │
│  • Display HTML/CSS                                                        │
│  • Store data                                                              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│  MODEL (Data & Business Logic)                                              │
│  ────────────────────────────────────────────────────────────────────────  │
│  Responsibilities:                                                          │
│  • Access database                                                          │
│  • Perform data operations                                                 │
│  • Implement business logic                                                │
│  • Validate data integrity                                                 │
│  • Return data to Controller                                               │
│                                                                             │
│  Does NOT:                                                                  │
│  • Handle HTTP requests                                                    │
│  • Display views                                                           │
│  • Format output for display                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Benefits of MVC Architecture in ThriftHub

1. **Separation of Concerns**
   - Clear boundaries between presentation, logic, and data
   - Easier maintenance and updates

2. **Code Reusability**
   - Models can be used by multiple controllers
   - Controllers can serve multiple views

3. **Testability**
   - Each layer can be tested independently
   - Easier unit testing

4. **Scalability**
   - Easy to add new features
   - Modular structure supports growth

5. **Maintainability**
   - Changes in one layer don't affect others
   - Clear code organization

6. **Team Collaboration**
   - Frontend developers work on Views
   - Backend developers work on Models/Controllers
   - Clear division of work

---

**Document Version:** 1.0  
**Last Updated:** November 2025  
**Architecture Pattern:** Model-View-Controller (MVC)

