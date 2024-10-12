# Inventory Management Website

This project is an **Inventory Management System** built using **PHP**, **JavaScript**, and **Bootstrap**, and runs on **XAMPP**. The system allows administrators to manage users, providers, and products in a simple and intuitive interface.

## Features

### User Authentication
- **Sign In**: Users sign in with their email and password. There is no registration feature as user management is handled by the admin.
- **User Roles**: 
  - **Normal Users**: Redirected to the dashboard.
  - **Admins**: Redirected to user and provider management pages.

### Dashboard (Normal User)
Once logged in, normal users are presented with a dashboard that includes:
- **Statistics**: Displays total numbers of products, users, providers, and total capital in stock.
- **Product Chart**: A chart displaying the number of products by provider.
- **Recent Products Table**: Shows the latest added products.
  
The sidebar includes links to the following sections:
1. **Dashboard**: Displays the summary and recent product activity.
2. **Products Page**: A table showcasing product details (ID, name, price, stock, provider, date added, date modified). Includes options to:
   - **Edit**: Modify existing product information.
   - **Delete**: Soft delete a product (moved to the "Deleted Products" page).
3. **Add Product**: Add new products to the inventory. The list of providers is pre-defined by the admin.
4. **Deleted Products**: A page for managing soft-deleted products. Options include:
   - **Restore**: Restore the product to the active products list.
   - **Delete Permanently**: Remove the product entirely from the database.
5. **Reports**: Generate PDF reports for products, deleted products, users, and providers.
6. **Sign Out**: Log out of the system.

### Admin Management Pages
Admins have access to two additional management pages:
1. **User Management**: Add, edit, or delete users.
2. **Provider Management**: Add, edit, or delete providers.

## Database Design

### Providers Table
```sql
CREATE TABLE Providers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    address TEXT,
    contact_info VARCHAR(255)
);
```

### Products Table
```sql
CREATE TABLE Products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    provider_id INT NOT NULL,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (provider_id) REFERENCES Providers(id)
);
```

### Users Table
```sql
CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE
);
```

## Getting Started

### Prerequisites
- **XAMPP**: Download and install [XAMPP](https://www.apachefriends.org/index.html).
- **PHP**: Ensure PHP is enabled in XAMPP.
- **Database**: Import the provided SQL schema to set up the database.

### Installation
1. Clone the repository.
2. Start XAMPP and ensure Apache and MySQL services are running.
3. Import the SQL database schema.
4. Open the project in your browser using `localhost`.

### Usage
- Admin can log in to manage users and providers.
- Normal users can log in to manage products and view reports.
