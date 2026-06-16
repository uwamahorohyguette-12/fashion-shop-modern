# KigaliThreads
## E-Commerce Fashion Shop — Project Report

**Student:** Uwamahorohy Guette
**GitHub:** https://github.com/uwamahorohyguette-12/fashion-shop-modern
**Live URL:** https://kigalithread.gt.tc/
**Submission Date:** 2026

---

## 1. Introduction

The rapid growth of internet access and mobile technology across Rwanda has created significant opportunities for digital commerce. Fashion retail, one of the most active sectors in Kigali's economy, has largely remained offline, relying on physical markets and word-of-mouth. KigaliThreads is a web-based e-commerce platform developed to bridge this gap by providing a modern, fully functional online fashion store tailored specifically for the Rwandan market.

The platform was built from the ground up using core web technologies — PHP, MySQL, HTML, CSS, and JavaScript — without relying on heavy frameworks, keeping it lightweight, affordable to host, and easy to maintain. It covers the complete customer journey: discovering products, adding them to a cart, placing an order via MTN Mobile Money simulation, receiving a confirmation, tracking the order status, and downloading a printable receipt.

The project also fulfills all academic requirements including version control via GitHub, live deployment on InfinityFree hosting, and a fully automated CI/CD pipeline using GitHub Actions.

---

## 2. Problem Statement

Small and medium-sized fashion businesses in Rwanda face several challenges when trying to sell online:

- **No affordable platform:** Global solutions like Shopify charge monthly fees that are out of reach for small retailers.
- **No local payment support:** Most platforms do not natively support MTN Mobile Money, the most widely used payment method in Rwanda.
- **No order visibility:** Customers have no way to track their orders after purchase without calling the seller directly.
- **Limited technical capacity:** Business owners often lack the technical knowledge to set up and manage complex e-commerce systems.

There is a clear need for a simple, locally-tailored, affordable, and deployable e-commerce solution that supports the Rwandan payment ecosystem and provides both customers and shop owners with the tools they need.

---

## 3. Objectives

The main objectives of this project were:

1. Design and develop a responsive, mobile-friendly online fashion store.
2. Implement a full shopping flow: browsing → cart → checkout → confirmation → tracking.
3. Build a complete admin dashboard for product, order, and customer management.
4. Support both image URL and local file upload for product images in the admin panel.
5. Allow customers to track their orders using only an Order ID — without requiring a login.
6. Generate printable branded receipts for customers.
7. Deploy the application to a live, publicly accessible hosting environment.
8. Implement a CI/CD pipeline using GitHub Actions for automated testing and deployment on every code push.

---

## 4. System Features

### 4.1 Customer-Facing Features

| Feature | Description |
|---|---|
| Homepage | Hero banner, featured collections, new arrivals, and best sellers sections |
| Product Listing | Grid layout with category filtering and keyword search |
| Product Detail Page | Product images, description, size variants, and Add to Cart button |
| Shopping Cart | Add/remove items, update quantities, running subtotal using browser localStorage |
| Checkout | Customer details form, MTN Mobile Money number input, order summary sidebar |
| Order Confirmation | Thank you page showing Order ID, 4-step status tracker, and receipt link |
| Order Tracking | Public search page — customer enters Order ID to see live status timeline |
| Printable Receipt | Branded receipt with itemized table, totals, delivery address, and print button |
| My Account | Order history with color-coded statuses, Track and Receipt buttons per order |
| User Authentication | Register, login, and logout with PHP session management |

### 4.2 Admin Features

| Feature | Description |
|---|---|
| Dashboard | Overview statistics: total products, orders, customers, and revenue |
| Product Management | Add, edit, and delete products; supports both image URL and file upload |
| Order Management | View all orders, update status (pending → paid → shipped → delivered) |
| Customer Management | View all registered customers and their contact details |
| Collections Management | Manage product categories (Men, Women, Kids, Shoes, Accessories) |
| User Management | Manage admin user accounts |

---

## 5. Technologies Used

| Layer | Technology | Purpose |
|---|---|---|
| Backend Language | PHP 8.1 | Server-side logic, routing, form handling |
| Database | MySQL 8 | Persistent data storage |
| Database Access | PDO with prepared statements | Secure, injection-safe queries |
| Frontend Styling | Tailwind CSS (CDN) | Responsive utility-first CSS framework |
| Typography | Google Fonts (Inter, Playfair Display) | Professional UI typography |
| Frontend Logic | Vanilla JavaScript | Cart management, tab switching, form handling |
| Cart Persistence | Browser localStorage | Stateless cart without server sessions |
| Authentication | PHP Sessions + password_hash / password_verify | Secure user login system |
| Local Development | XAMPP (Apache + MySQL + PHP) | Local development environment |
| Hosting | InfinityFree | Free shared PHP/MySQL hosting |
| Version Control | Git + GitHub | Source code management and collaboration |
| CI/CD | GitHub Actions | Automated testing and deployment pipeline |

---

## 6. System Architecture

### 6.1 Application Architecture

```
┌──────────────────────────────────────────────────────────┐
│                      Browser (Client)                     │
│     HTML5 + Tailwind CSS + Vanilla JS + localStorage      │
└─────────────────────────┬────────────────────────────────┘
                          │  HTTP Requests
┌─────────────────────────▼────────────────────────────────┐
│              Apache Web Server — PHP 8.1                  │
│                                                          │
│   ┌──────────────────┐   ┌──────────────────────────┐    │
│   │   Storefront     │   │      Admin Panel         │    │
│   │  index.php       │   │   admin/index.php        │    │
│   │  products.php    │   │   admin/products.php     │    │
│   │  product.php     │   │   admin/orders.php       │    │
│   │  cart.php        │   │   admin/customers.php    │    │
│   │  checkout.php    │   │   admin/collections.php  │    │
│   │  order-          │   │   admin/users.php        │    │
│   │  tracking.php    │   └──────────────────────────┘    │
│   │  receipt.php     │                                   │
│   │  account.php     │   ┌──────────────────────────┐    │
│   └──────────────────┘   │   Shared Includes        │    │
│                          │   includes/init.php      │    │
│   ┌──────────────────┐   │   includes/functions.php │    │
│   │   config/        │   │   includes/auth.php      │    │
│   │   app.php        │   │   includes/header.php    │    │
│   │   database.php   │   │   includes/footer.php    │    │
│   └──────────────────┘   └──────────────────────────┘    │
└─────────────────────────┬────────────────────────────────┘
                          │  PDO / SQL
┌─────────────────────────▼────────────────────────────────┐
│                    MySQL Database                         │
│                                                          │
│  users          ecom_products      ecom_product_variants  │
│  ecom_collections                  ecom_product_collections│
│  ecom_customers  ecom_orders       ecom_order_items       │
└──────────────────────────────────────────────────────────┘
```

### 6.2 Database Schema

| Table | Columns | Purpose |
|---|---|---|
| `users` | id, email, password_hash, full_name, is_admin | Admin and customer user accounts |
| `ecom_products` | id, name, handle, description, price, product_type, inventory_qty, images, tags, status, has_variants | Full product catalog |
| `ecom_product_variants` | id, product_id, title, option1, sku, price, inventory_qty, position | Size/color variants per product |
| `ecom_collections` | id, title, handle, description, image, is_visible | Product categories |
| `ecom_product_collections` | product_id, collection_id, position | Many-to-many product-category mapping |
| `ecom_customers` | id, email, name, phone | Customer profiles |
| `ecom_orders` | id, customer_id, status, subtotal, tax, shipping, total, shipping_address, payment_ref, notes | Order records |
| `ecom_order_items` | id, order_id, product_id, variant_id, product_name, variant_title, sku, quantity, unit_price, total | Individual items per order |

### 6.3 Order Status Flow

```
Customer Places Order
        │
        ▼
    [pending]  ← Default on checkout
        │
        │  Admin confirms payment
        ▼
     [paid]
        │
        │  Admin ships order
        ▼
   [shipped]
        │
        │  Order delivered
        ▼
  [delivered]
        │
        ▼
[cancelled / refunded]  ← If needed at any stage
```

---

## 7. Screenshots

### Homepage
![Home Page](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/home.png)

### Product Listing Page
![Product Listing](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/product.png)

### Product Details Page
![Product Details](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/product-details.png)

### Shopping Cart
![Shopping Cart](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/shopping-cart.png)

### Checkout Page
![Checkout](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/checkout.png)

### Order Confirmation Page
![Order Confirmation](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/order-confirmation.png)

### Order Tracking Page
![Order Tracking](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/tracking.png)

### Receipt Page
![Receipt](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/receipt.png)

### My Account Page
![My Account](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/MyAccount.png)

### Admin Dashboard
![Admin Dashboard](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/Admindashboard.png)

### Admin Products
![Admin Products](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/Adminproducts.png)

### Admin Orders
![Admin Orders](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/AdminOrder.png)

### GitHub Repository
![GitHub Repository](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/GitHubrepository.png)

### CI/CD Pipeline
![CI/CD Pipeline](https://raw.githubusercontent.com/uwamahorohyguette-12/fashion-shop-modern/main/screenshots/ci-cd.png)

---

## 8. GitHub Repository

**Repository URL:** https://github.com/uwamahorohyguette-12/fashion-shop-modern

The repository contains:
- Full application source code (PHP, JS, CSS)
- Database schema (`database/schema.sql`)
- GitHub Actions workflow (`.github/workflows/ci-cd.yml`)
- Project report (`PROJECT_REPORT.md`)
- `.gitignore` and `.htaccess` configuration files
- Meaningful commit history documenting the development process

---

## 9. Deployment

**Live Application URL:** https://kigalithread.gt.tc/

The application is deployed on **InfinityFree** free shared hosting with the following configuration:

- **Web Server:** Apache with PHP 8.1
- **Database:** MySQL on host `sql310.infinityfree.com`, database `if0_42195796_fashion_shop`
- **Domain:** Custom domain `kigalithread.gt.tc`
- **File Upload:** Product images stored in `public/uploads/products/`
- **Routing:** `.htaccess` configured for security and clean URL routing
- **BASE_URL:** Set to empty string `''` for root-level deployment

---

## 10. CI/CD Pipeline

A full Continuous Integration and Continuous Deployment pipeline is implemented using **GitHub Actions**, defined in `.github/workflows/ci-cd.yml`.

### 10.1 Pipeline Flow

```
Developer pushes code to main branch on GitHub
                      │
                      ▼
        ┌─────────────────────────┐
        │    JOB 1: CI — Lint     │
        │  & Validate             │
        │                         │
        │  1. Checkout code       │
        │  2. Setup PHP 8.1       │
        │  3. php -l on all .php  │
        │     files (syntax check)│
        │  4. Verify schema.sql   │
        │     exists              │
        │  5. Verify all required │
        │     pages exist         │
        └────────────┬────────────┘
                     │ Pass
                     ▼
        ┌─────────────────────────┐
        │    JOB 2: CD — Deploy   │
        │  (only on main branch)  │
        │                         │
        │  1. Checkout code       │
        │  2. FTP upload to       │
        │     InfinityFree        │
        │  3. Exclude .git,       │
        │     .github, node_      │
        │     modules             │
        └─────────────────────────┘
                     │
                     ▼
         Live site updated at
         https://kigalithread.gt.tc/
```

### 10.2 Security

FTP credentials are stored as **encrypted GitHub repository secrets** (`FTP_HOST`, `FTP_USER`, `FTP_PASS`) and are never exposed in the source code or workflow file. They are injected at runtime by GitHub Actions only during deployment.

### 10.3 Trigger Conditions

| Event | CI Runs | CD Runs |
|---|---|---|
| Push to `main` | ✅ Yes | ✅ Yes (if CI passes) |
| Pull Request to `main` | ✅ Yes | ❌ No |
| Push to other branches | ❌ No | ❌ No |

---

## 11. Challenges Encountered

| # | Challenge | Solution Applied |
|---|---|---|
| 1 | Order ID is a one-way `md5` hash — cannot be reversed for lookup | Built `findOrderByDisplayId()` which fetches all orders and matches the hash iteratively |
| 2 | Duplicate product URL handles causing database constraint errors | Added a pre-insert uniqueness loop that auto-appends `-2`, `-3`, etc. to the handle |
| 3 | Image URL and file upload inputs conflicting in the same form | Used a hidden `<input name="images">` populated by JavaScript on form submit, isolating the two input types |
| 4 | `enctype="multipart/form-data"` required for file upload but disrupted URL field | Decoupled the visible URL text input from the submitted field name; hidden field carries the final value |
| 5 | `BASE_URL` path mismatch between local XAMPP and live InfinityFree deployment | Set `BASE_URL` to `''` (empty string) in `config/app.php` for root-level hosting |
| 6 | Shopping cart state lost on page navigation (PHP is stateless) | Implemented cart logic entirely in browser `localStorage` using a custom JavaScript `Cart` class |
| 7 | Orders defaulting to `paid` status immediately on placement | Changed `checkout-process.php` to insert orders with `pending` status; admin confirms payment manually |
| 8 | CI/CD pipeline deploying local database credentials to live server | Ensures production `config/database.php` with live credentials is committed before each push |

---

## 12. Future Work

The following enhancements are planned for future development iterations:

- **Real MTN MoMo API Integration** — Replace the simulated payment with live MTN Mobile Money API for Rwanda, enabling real transactions.
- **Email Notifications** — Automatically send order confirmation and status update emails to customers using PHPMailer or an SMTP service.
- **SMS Notifications** — Send SMS alerts via Africa's Talking API when an order status changes.
- **Advanced Product Filtering** — Add price range sliders, size filters, color filters, and tag-based filtering on the product listing page.
- **Customer Reviews and Ratings** — Allow verified buyers to leave product reviews and star ratings.
- **Discount Codes and Promotions** — Implement a coupon system that applies percentage or fixed discounts at checkout.
- **Multi-Image Product Gallery** — Allow multiple images per product with a swipe-able gallery on the product detail page.
- **Low Stock Alerts** — Notify the admin when product inventory falls below a set threshold.
- **Sales Analytics Dashboard** — Add charts showing daily/monthly revenue, top-selling products, and order trends.
- **Progressive Web App (PWA)** — Add a service worker and manifest to make the storefront installable on mobile devices.
- **Social Login** — Allow customers to sign in with Google or Facebook for faster registration.

---

## 13. Conclusion

KigaliThreads is a fully functional, production-deployed e-commerce platform that successfully meets all the requirements set for this project. The system provides a complete online shopping experience — from product discovery and cart management to checkout, order tracking, and receipt generation — all tailored for the Rwandan fashion market with MTN Mobile Money support.

The admin dashboard gives store managers complete control over their catalog, orders, and customers, while the CI/CD pipeline ensures that every code change is automatically validated and deployed to the live server without manual intervention.

Beyond the technical deliverables, this project demonstrates the practical application of full-stack web development, relational database design, secure authentication, client-server architecture, version control, and modern DevOps practices. It serves as a solid foundation that can be extended into a fully commercial product for Rwandan fashion retailers.

---

*KigaliThreads E-Commerce Platform — Project Report*
*GitHub: https://github.com/uwamahorohyguette-12/fashion-shop-modern*
*Live: https://kigalithread.gt.tc/*
