<h1 align="center"><a href="https://github.com/ronknight/InventorySystem">Inventory Management System</a></h1>

<h3 align="center">
  <img
    alt="Image of a keycap with the letter K on it in pink tones"
    title="Kanata"
    height="160"
    src="design/images/favicon/android-chrome-512x512.png"
  />
</h3>

<h4 align="center">An Inventory System using Bootstrap, JavaScript, MySQL & PHP</h4>

# Inventory Management System

This Inventory Management System allows you to efficiently manage products, users, and providers. It provides comprehensive features for inventory tracking, user management, and report generation using a clean, user-friendly interface.

## Features

- **Normal and Admin Users**: Each user type has different privileges, with admin users having full access to user and provider management, while normal users can manage everything else.
- **Dashboard**: Provides an overview of key metrics, such as total products, total stock, and total users.
- **Product Management**: Add, edit, or delete products from the system.
- **Product Recycle Bin**: Restore products or permanently delete them from the system.
- **Provider Management**: Admins can add and update provider details.
- **User Management**: Admins can manage system users, including adding, editing, or removing users.
- **Reports**: View and export reports for products, users, deleted products, and providers.

## Screenshots

### Home Page
![Home Page1](screenshots/1_page_accueil.jpg)
![Home Page2](screenshots/2_page_accueil.jpg)
![Home Page3](screenshots/3_page_accueil.png)
![Home Page4](screenshots/4_page_accueil.jpg)
![Home Page5](screenshots/5_page_accueil.png)

### Login Page
![Login Page](screenshots/6_page_de_connexion.png)


### Dashboard
![Dashboard](screenshots/7_tableau_bord.jpg)

### Product Management
![Products Page](screenshots/8_page_produits.jpg)
![Edit Product](screenshots/9_formulaire_modification.jpg)
![Add Product](screenshots/10_page_ajout_produit.jpg)

### Product Recycle Bin
![Deleted Products](screenshots/11_page_produits_supprimes.jpg)

### Reports
![Reports Page](screenshots/13_page_rapports.jpg)
![Print Interface](screenshots/12_interface_impression.jpg)

### Admin Options
![Admin Choices](screenshots/14_admin_choix.jpg)

### User Management
![Manage Users](screenshots/15_gestion_utilisateurs.jpg)

### Provider Management
![Manage Providers](screenshots/16_gestion_fournisseurs.jpg)


## Credentials

Normal User :

  - **Email**: admin@admin.com
  - **Password**: password

Admin User :

  - **Email**: admin@admin.com
  - **Password**: password

## Installation

1. **Clone or download the repository**:
    ```bash
    git clone https://github.com/migi-gluttony/inventory_management.git
    ```
2. **Move files to the root folder** of your server (e.g., for XAMPP, the location is `C:\xampp\htdocs\InventorySystem`).
3. **Set up the database**:
   - Open PhpMyAdmin from your local application server.
   - Create a new database named `stock_management`.
   - Import the `stock_management.sql` file from the `database` folder into the `stock` database.
4. **Start Apache and MySQL** on your application server's control panel.
5. **Access the system**: Go to `http://localhost/inventory_management/` in your browser.

## Requirements

- **PHP** version 8.0.0 or newer.
- **Database**: MySQL or compatible.
- **Application Server**: Choose from LAMP, MAMP, or XAMPP.

## Resources

- **Original Source Code**: Placeholder for the original source.
- **Framework**: CodeIgniter - Web Framework.

---

Feel free to contribute or suggest improvements. Fork the repository and submit a pull request for any major changes.
