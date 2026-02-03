# Ghas Paat Restro (Online Ordering System)

## Live Website Link
Paste your student server link here:
https://student.heraldcollege.edu.np/~NP03CS4A240171/restaurant/

---

## Admin Login
**Admin URL:**  
https://student.heraldcollege.edu.np/~NP03CS4A240171/restaurant/admin/login.php

**Username:** admin  
**Password:** admin123



---

## Setup Instructions (Local - XAMPP)

1. Copy project folder to:
   C:\xampp\htdocs\restaurant

2. Start XAMPP:
   - Apache ON
   - MySQL ON

3. Open phpMyAdmin:
   http://localhost/phpmyadmin

4. Create a database:
   restaurant_db

5. Import SQL file:
   - Select the database
   - Go to **Import**
   - Choose `database.sql`
   - Click **Go**

6. Run the website:
   http://localhost/restaurant/

---

## Setup Instructions (College Server)

1. Upload your project folder into:
   ~/public_html/restaurant

2. Upload images into:
   ~/public_html/restaurant/assets/images/

3. Import the SQL into your server database using phpMyAdmin:
   - Database name: YOUR_STUDENT_ID
   - Username: YOUR_STUDENT_ID

4. Update DB settings in:
   `config.php`

---

## Features Implemented

### User Side
- View menu items with images
- Filter/search menu items (price/cuisine/availability/name search if implemented)
- Add to cart (AJAX)
- Update/remove cart items
- Checkout and place order
- Order success page
- Track order status

### Admin Side
- Admin login/logout (session-based)
- Manage categories (Add/Edit/Delete)
- Manage menu items (Add/Edit/Delete)
- View orders and order details
- Update order status (Pending → Preparing → Ready → Completed)

---

## Folder Structure (Important)
restaurant/
- admin/
- assets/
  - images/
  - style.css
  - app.js
- index.php
- menu.php
- cart.php
- checkout.php
- order_success.php
- config.php

---

## Known Issues (if any)
- If a new image does not show on the student server, ensure the image file is uploaded inside:
  `assets/images/`
  and filename matches exactly (case-sensitive).
- after adding a menu items in the cart if i login admin the cart still shows the cart items which it should not suppose to show

---

## Security Notes (Basic)
- Prepared statements used to prevent SQL Injection.
- Sessions used for admin authentication.
- CSRF protection: 
