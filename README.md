# ShopSmart — E-Commerce Website (PHP + MySQL)

A student-friendly, full-stack e-commerce demonstration built with **HTML, CSS, JavaScript, PHP, MySQL, and PDO**. It is designed for local development with XAMPP and VS Code.

> This is an educational demo, not a production commerce platform. Checkout supports Cash on Delivery (COD) only. It does not collect card details or process real payments.

## Features

- Responsive home page and product catalogue
- Product search and category filtering
- Product details, stock display, session-based cart, quantity updates, and item removal
- Customer registration and login using PHP password hashing
- Checkout form and transactional order creation
- Order history for signed-in customers
- Admin dashboard with counts and order value
- Admin product create/edit/activate/hide workflows
- Admin category creation and order status management
- MySQL schema with sample categories and 12 sample products
- PDO prepared statements, HTML output escaping, CSRF tokens, session ID regeneration, and stock validation

## Folder structure

```text
E-Commerce-Website/
├── index.php
├── products.php
├── product-details.php
├── cart.php
├── checkout.php
├── orders.php
├── login.php
├── register.php
├── logout.php
├── config/database.php
├── includes/{header.php,footer.php,functions.php}
├── admin/{_guard.php,dashboard.php,products.php,categories.php,orders.php}
├── assets/css/style.css
├── assets/js/script.js
├── assets/images/product-placeholder.svg
├── database/ecommerce.sql
└── docs/College_Project_Report.{docx,pdf}
```

## Requirements

- Windows 10/11 (or another OS that supports XAMPP)
- XAMPP with Apache, PHP 8.1+ recommended, and MySQL/MariaDB
- VS Code
- Browser such as Chrome or Edge

## Setup in XAMPP and VS Code

### 1. Start XAMPP
1. Install XAMPP if it is not already installed.
2. Open **XAMPP Control Panel**.
3. Click **Start** beside **Apache** and **MySQL**.
4. If either service fails to start, resolve its port conflict before continuing.

### 2. Copy the project
1. Extract the project ZIP.
2. Copy the `E-Commerce-Website` folder to `C:\\xampp\\htdocs\\`.
3. The main file should be at `C:\\xampp\\htdocs\\E-Commerce-Website\\index.php`.
4. In VS Code, choose **File → Open Folder** and select that folder.

### 3. Import the database
1. Open `http://localhost/phpmyadmin` in your browser.
2. Select **Import**.
3. Choose `database/ecommerce.sql` from the project folder.
4. Click **Import** / **Go** and wait for the success message.
5. The SQL file creates `ecommerce_portal`, tables, categories, and 12 sample products. Importing it again drops and recreates the project tables, so do not re-import after storing data you want to keep.

### 4. Check database connection
Open `config/database.php`. The defaults are:
- Host: `127.0.0.1`
- Database: `ecommerce_portal`
- Username: `root`
- Password: blank

These are common local XAMPP defaults. If your MySQL root account has a password, enter it in `$password`. Do not use a blank root password on an internet-facing server.

### 5. Open the website
Visit:

`http://localhost/E-Commerce-Website/`

Do not open PHP files using `file:///...`; PHP must be served through Apache.

### 6. Create a customer account
1. Click **Create account**.
2. Enter your name, email, and a password of at least 8 characters.
3. Sign in and add products to your cart.
4. Checkout requires a name, phone, address, city, and postal code.

### 7. Create your admin account
For safety, the database does **not** include a shared default admin password.

1. Register your own account through the website.
2. Open phpMyAdmin and select the `ecommerce_portal` database.
3. Open the **SQL** tab and run the following query after replacing the email with the email you registered:

```sql
UPDATE users
SET role = 'admin'
WHERE email = 'your-email@example.com';
```

4. Log out and log in again so the role is refreshed in the session.
5. The **Admin** link will appear. Use it to manage products, categories, and orders.

Only promote accounts you control. Do not expose an admin account on a public server.

## How the database works

- `users`: customer and admin accounts; passwords are stored as hashes.
- `categories`: product categories.
- `products`: descriptions, prices, available stock, image path, and active/hidden flag.
- `orders`: customer delivery details, total, COD payment method, status, and creation date.
- `order_items`: snapshot of product name, unit price, quantity, and line total for each order.

Relationships: one category can have many products; one user can have many orders; one order can have many order items. Checkout runs inside a database transaction, locks stock rows while validating inventory, inserts the order and order items, then decreases stock. If a validation or SQL operation fails, the transaction rolls back.

## How to test manually

- [ ] Home page loads and sample products appear.
- [ ] Product search and category filtering work.
- [ ] Product details show price and stock.
- [ ] Registration rejects invalid email and short password.
- [ ] Login rejects an incorrect password.
- [ ] Add/update/remove cart items works.
- [ ] A guest is redirected to login before checkout.
- [ ] Valid COD checkout creates an order and decreases stock.
- [ ] Customer sees only their own orders.
- [ ] Admin can add/edit/hide products and add categories.
- [ ] Admin can update order status.
- [ ] Invalid form tokens are rejected.

Record actual results and screenshots after running the application. Do not state that a test passed until you have performed it in your environment.

## Troubleshooting

**Database connection failed**
- Check MySQL is running in XAMPP.
- Confirm the database import completed.
- Check the host, database name, username, and password in `config/database.php`.

**404 Not Found**
- Confirm the folder is under `C:\\xampp\\htdocs\\E-Commerce-Website`.
- Check the URL spelling and that Apache is running.

**Admin link is missing**
- Promote your registered account using the SQL query above, then log out and back in.

**Images do not appear**
- The sample data uses `assets/images/product-placeholder.svg`. Confirm that file exists and that the app is opened from localhost.

**Port 80/443 or 3306 is busy**
- Close the conflicting service or change XAMPP's port settings, then use the correct local URL.

## Security and scope notes

This code includes basic educational safeguards but is not a security audit or production-ready shop. Before deployment, add HTTPS, secure cookie configuration, rate limiting, email verification, password reset, audit logging, stricter authorization review, upload validation if product uploads are added, backups, privacy/legal pages, tax/shipping rules, and a payment gateway with server-side verification. Never store card numbers or CVVs yourself.

## Tech stack

- Front end: HTML5, CSS3, vanilla JavaScript
- Back end: PHP (PDO)
- Database: MySQL/MariaDB
- Local environment: XAMPP
- Editor: VS Code

## Academic report

See `docs/College_Project_Report.docx` and `docs/College_Project_Report.pdf`. Replace the placeholders with your institution, guide, and actual testing evidence before submission.
