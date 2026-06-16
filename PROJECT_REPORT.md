# KigaliThreads — E-Commerce Fashion Shop
## Project Report

---

## 1. Introduction

KigaliThreads is a full-featured e-commerce web application built for the Rwandan fashion market. The platform allows customers to browse clothing collections, add items to a shopping cart, place orders via MTN Mobile Money simulation, and track the status of their deliveries in real time. An admin dashboard gives store managers full control over products, orders, and customers.

The project was developed using PHP, MySQL, HTML, CSS (Tailwind CSS), and JavaScript — without any heavy framework — making it lightweight, fast, and easy to deploy on shared hosting.

---

## 2. Problem Statement

Many local Rwandan fashion businesses lack an affordable, professional online presence that supports the full buying journey — from browsing to payment to delivery tracking. Existing global platforms (Shopify, WooCommerce) are either expensive or require technical expertise beyond the reach of small local retailers. There is a need for a simple, locally-tailored, and deployable e-commerce solution that works well on Rwandan internet conditions and supports MTN Mobile Money — the dominant payment method in Rwanda.

---

## 3. Objectives

- Build a responsive, mobile-friendly online fashion store tailored for the Rwandan market.
- Implement a complete shopping flow: product browsing → cart → checkout → order confirmation → tracking.
- Provide a full admin dashboard for managing products, orders, and customers.
- Support both image URL and file upload for product images.
- Enable customers to track orders by Order ID without requiring a login.
- Generate printable customer receipts.
- Deploy the application to a live hosting environment (InfinityFree).
- Implement a CI/CD pipeline using GitHub Actions for automated testing and deployment.

---

## 4. System Features

### Customer-Facing Features
| Feature | Description |
|---|---|
| Homepage | Hero banner, featured collections, new arrivals, best sellers |
| Product Listing | Filterable by category, with search support |
| Product Detail Page | Images, description, size variants, add to cart |
| Shopping Cart | Add/remove items, update quantities, running total (localStorage) |
| Checkout | Customer details form, MTN MoMo payment simulation, order summary |
| Order Confirmation | Thank you page with order ID, status tracker, receipt link |
| Order Tracking | Public search by Order ID — no login required, live status timeline |
| Printable Receipt | Full branded receipt with items, totals, and delivery address |
| My Account | Order history with color-coded status, Track and Receipt buttons |
| User Authentication | Register, login, logout with session management |

### Admin Features
| Feature | Description |
|---|---|
| Dashboard | Stats: products, orders, customers, revenue |
| Product Management | Add/edit/delete products with image upload or URL |
| Order Management | View orders, update status (pending → paid → shipped → delivered) |
| Customer Management | View all customers and their contact details |
| Collections Management | Manage product categories/collections |
| User Management | Admin user accounts |

---

## 5. Technologies Used

| Layer | Technology |
|---|---|
| Backend | PHP 8.1 (procedural + PDO) |
| Database | MySQL 8 (via PDO with prepared statements) |
| Frontend | HTML5, Tailwind CSS (CDN), Vanilla JavaScript |
| Fonts | Google Fonts — Inter, Playfair Display |
| Cart State | Browser localStorage (JSON) |
| Authentication | PHP Sessions with password_hash / password_verify |
| Hosting | InfinityFree (shared PHP/MySQL hosting) |
| Version Control | Git + GitHub |
| CI/CD | GitHub Actions |
| Local Development | XAMPP (Apache + MySQL + PHP) |

---

## 6. System Architecture

```
┌─────────────────────────────────────────────────────┐
│                     Browser (Client)                 │
│  HTML + Tailwind CSS + Vanilla JS (Cart/localStorage)│
└───────────────────────┬─────────────────────────────┘
                        │ HTTP Requests
┌───────────────────────▼─────────────────────────────┐
│               Apache Web Server (PHP 8.1)            │
│                                                      │
│  ┌─────────────┐  ┌──────────────┐  ┌─────────────┐ │
│  │  Storefront │  │    Admin     │  │   Includes  │ │
│  │  Pages      │  │   Panel      │  │  (shared)   │ │
│  │  index.php  │  │  /admin/     │  │  init.php   │ │
│  │  products   │  │  products    │  │  functions  │ │
│  │  cart       │  │  orders      │  │  auth       │ │
│  │  checkout   │  │  customers   │  │  header     │ │
│  │  tracking   │  │  users       │  │  footer     │ │
│  └─────────────┘  └──────────────┘  └─────────────┘ │
│                                                      │
│  ┌──────────────────────────────────────────────┐    │
│  │               config/                        │    │
│  │   app.php (BASE_URL, helpers)                │    │
│  │   database.php (PDO connection)              │    │
│  └──────────────────────────────────────────────┘    │
└───────────────────────┬─────────────────────────────┘
                        │ PDO / SQL
┌───────────────────────▼─────────────────────────────┐
│                MySQL Database                        │
│                                                      │
│  users │ ecom_products │ ecom_product_variants       │
│  ecom_collections │ ecom_product_collections         │
│  ecom_customers │ ecom_orders │ ecom_order_items      │
└─────────────────────────────────────────────────────┘
```

### Database Schema Summary

| Table | Purpose |
|---|---|
| `users` | Admin and registered user accounts |
| `ecom_products` | Product catalog with images, price, stock |
| `ecom_product_variants` | Size/color variants per product |
| `ecom_collections` | Product categories (Men, Women, Kids, etc.) |
| `ecom_product_collections` | Many-to-many: products ↔ collections |
| `ecom_customers` | Customer profiles (email, name, phone) |
| `ecom_orders` | Orders with status, totals, shipping address |
| `ecom_order_items` | Line items per order |

---

## 7. Screenshots

> Screenshots of the following pages should be included in the final submitted report:
> 1. Homepage
> 2. Product listing page
> 3. Product detail page
> 4. Shopping cart
> 5. Checkout page
> 6. Order confirmation page
> 7. Order tracking page
> 8. Printable receipt
> 9. My Account — order history
> 10. Admin dashboard
> 11. Admin — product management (add/edit with upload & URL tabs)
> 12. Admin — order management (status update)
> 13. GitHub repository
> 14. GitHub Actions CI/CD pipeline run

---

## 8. GitHub Repository Link

> **https://github.com/uwamahorohyguette/fashion-shop-modern**

---

## 9. Deployment Link

**Live Application:** https://kigalithread.gt.tc/

The application is deployed on InfinityFree free shared hosting with:
- PHP 8.1 support
- MySQL database: `if0_42195796_fashion_shop` on host `sql310.infinityfree.com`
- All static assets served from the same host
- `.htaccess` configured for clean routing and security

---

## 10. CI/CD Description

A CI/CD pipeline is implemented using **GitHub Actions** (`.github/workflows/ci-cd.yml`).

### Pipeline Stages

```
Push to main branch
       │
       ▼
┌─────────────────────┐
│   CI: Build & Lint  │
│  • PHP syntax check │
│  • Schema file check│
│  • Required pages   │
│    existence check  │
└──────────┬──────────┘
           │ (on success)
           ▼
┌─────────────────────┐
│   CD: Deploy        │
│  • FTP upload to    │
│    InfinityFree     │
│  • Excludes .git,   │
│    node_modules     │
└─────────────────────┘
```

### How It Works
- Every `git push` to the `main` branch triggers the workflow automatically.
- The **CI job** runs `php -l` on every `.php` file to catch syntax errors, verifies the database schema file exists, and confirms all required pages are present.
- The **CD job** only runs if the CI job passes and only on pushes to `main` (not on pull requests). It uses `SamKirkland/FTP-Deploy-Action` to upload all project files directly to the InfinityFree hosting via FTP.
- FTP credentials (`FTP_HOST`, `FTP_USER`, `FTP_PASS`) are stored as encrypted GitHub repository secrets — never exposed in code.

### Setting Up GitHub Secrets
In your GitHub repository → Settings → Secrets and variables → Actions, add:
- `FTP_HOST` — your InfinityFree FTP hostname
- `FTP_USER` — your InfinityFree FTP username
- `FTP_PASS` — your InfinityFree FTP password

---

## 11. Challenges Encountered

| Challenge | Solution |
|---|---|
| Order ID tracking without reversible hash | Built `findOrderByDisplayId()` that iterates orders and matches `md5`-based display IDs |
| Duplicate product handles on insert | Added a uniqueness loop that appends `-2`, `-3`, etc. automatically |
| Image input conflict (URL vs file upload) | Used a hidden form field populated by JS on submit, with tab switching to isolate inputs |
| `enctype="multipart/form-data"` breaking URL field | Decoupled the URL text input from the `name="images"` attribute; used a hidden field as the actual carrier |
| InfinityFree `BASE_URL` path mismatch | Changed `BASE_URL` from `/fashion-shop-modern` to `''` for root deployment |
| Cart state persistence across pages | Used browser `localStorage` with a custom `Cart` JS class for stateless PHP backend |
| Order status defaulting to `paid` on creation | Changed checkout-process to insert with `pending` status; admin manually confirms payment |

---

## 12. Future Work

- **Real payment gateway integration** — Connect to actual MTN MoMo API or Flutterwave for Rwanda
- **Email notifications** — Send order confirmation and status update emails to customers
- **SMS notifications** — SMS alerts via Africa's Talking API when order status changes
- **Product search with filters** — Price range, size, and tag-based filtering
- **Customer reviews and ratings** — Per-product review system
- **Discount codes and promotions** — Coupon system at checkout
- **Multi-image products** — Gallery of images per product with swipe support
- **Inventory alerts** — Low-stock notifications for admin
- **Analytics dashboard** — Sales charts, revenue trends, top products
- **PWA support** — Make the storefront installable as a mobile app

---

## 13. Conclusion

KigaliThreads successfully delivers a complete, production-ready e-commerce platform tailored for the Rwandan fashion market. All core project requirements have been met: a responsive UI, product management, shopping cart, full checkout process, database integration, live deployment, and a CI/CD pipeline via GitHub Actions.

The system is built on simple, maintainable PHP without heavy frameworks, making it easy to extend and host on affordable infrastructure. The admin dashboard gives store owners full control over products and orders, while customers enjoy a smooth shopping experience from browsing to order tracking and receipt generation.

The project demonstrates practical application of web development, database design, server-side programming, version control, and DevOps principles in a real-world context.

---

*Report prepared for project submission — KigaliThreads E-Commerce Platform*
*Live URL: https://kigalithread.gt.tc/*
