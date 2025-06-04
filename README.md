# pdilo

This repository contains a PHP implementation of a simple digital menu for local businesses.
If PHP is not installed, you can install it on Ubuntu with:

```bash
sudo apt-get update && sudo apt-get install -y php-cli php-mysql
```

For local testing you also need MySQL. Install it with:

```bash
sudo apt-get install -y mysql-server
```

Run a local server with:

```bash
php -S localhost:8000
```

Make sure PHP has the MySQL extension installed (`php-mysql`).

Then open [http://localhost:8000/index.php](http://localhost:8000/index.php) in your browser to test.

The application uses a MySQL database (user and database `u102838416_pdilo`, password `Rovetto5!`).
Run the `schema.sql` script to create the required tables:

```bash
mysql -u u102838416_pdilo -p u102838416_pdilo < schema.sql
```

Visit [http://localhost:8000/admin.php](http://localhost:8000/admin.php) and use **admin123** as password to manage products, categories and appearance settings including the WhatsApp number.
The admin panel uses the same color scheme as your menu for a consistent look.
