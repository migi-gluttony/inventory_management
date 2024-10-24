# Inventory Management System

This Inventory Management System allows you to efficiently manage products, users, and providers. It provides comprehensive features for inventory tracking, user management, and report generation using a clean, user-friendly interface.

## Features

- **Normal and Admin Users**: Each user type has different privileges, with admin users having full access to user and providers management , and the normal user can do everything else.
- **Dashboard**: Provides an overview of key metrics, such as total products, total stock, and total users.
- **Product Management**: Add, edit, or delete products from the system.
- **Product Recycle Bin**: Restore products or permanently delete them from the system.
- **Provider Management**: Admins can add and update provider details.
- **User Management**: Admins can manage system users, including adding, editing, or removing users.
- **Reports**: View and export reports for products, users, deleted products, and providers.

## Screenshots

### Main Page 

![Main Page 1](screenshots/page_accueil_1.png)

![Main Page 2](screenshots/page_accueil_2.png)


### Login
![Login](screenshots/page_de_connexion.png)


### Dashboard
![Dashboard](screenshots/tableau_bord.jpg)


### Product Management
![Products 1](screenshots/page_produits.jpg)
![Products 2](screenshots/formulaire_modification.jpg)


### Add Product
![Add Product](screenshots/page_ajout_produit.jpg)


### Provider Management
![Providers](screenshots/gestion_fournisseurs.jpg)


### Users Management
![Users](screenshots/gestion_utilisateurs.jpg)


### Reports Page
![Reports 1](screenshots/page_rapports.jpg)
![Reports 2](screenshots/interface_impression.jpg)


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
   - Create a new database named `stock`.
   - Import the `stock.sql` file from the `database` folder into the `stock` database.
4. **Start Apache and MySQL** on your application server's control panel.
5. **Access the system**: Go to `http://localhost/InventorySystem/` in your browser.

## Requirements

- **PHP** version 8.0.0 or newer (recommended).
- **Database**: MySQL or compatible.
- **Application Server**: Choose from LAMP, MAMP, or XAMPP.

## Resources

- **Original Source Code**: Placeholder for the original source.
- **Framework**: CodeIgniter - Web Framework.
- **Theme**: AdminLTE - Dashboard and Admin Theme.

---

Feel free to contribute or suggest improvements. Fork the repository and submit a pull request for any major changes.
