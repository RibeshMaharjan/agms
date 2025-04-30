# Art Gallery Management System

## Installation Steps

### 1. Database Setup

1. Open your Laragon/XAMPP control panel and start Apache and MySQL services
2. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
3. Create a new database named `agms`
4. Import the database schema from `database/newagms.sql`

### 2. Project Setup

1. Clone or download this repository
2. Place the project folder in your Laragon/XAMPP's www directory:
   - For Laragon: `C:\laragon\www\`
   - For XAMPP: `C:\xampp\htdocs\`

### 3. Configuration

1. Open `includes/dbconnection.php` and update the database credentials if needed:
   ```php
   $dbhost = "localhost";
   $dbuser = "root";      // Default XAMPP/Laragon username
   $dbpass = "";          // Default XAMPP/Laragon password
   $dbname = "agms";
   ```

### 4. Access the Application

1. Start your Laragon/XAMPP services
2. Open your web browser and navigate to:
   - For Laragon: `http://agms.test`
   - For XAMPP: `http://localhost/agms`

## Default Login Credentials

### Admin Panel
- URL: `http://localhost/agms/admin`
- Username: `admin`
- Password: `admin`

### User Registration
- Users can register through the registration form
- After registration, users can log in to make purchases
