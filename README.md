# pdilo

This repository contains a PHP implementation of a simple digital menu for local businesses.
If PHP is not installed, you can install it on Ubuntu with:

```bash
sudo apt-get update && sudo apt-get install -y php-cli php-mysql
```

Run a local server with:

```bash
php -S localhost:8000
```

Make sure PHP has the MySQL extension installed (`php-mysql`).

Then open [http://localhost:8000/index.php](http://localhost:8000/index.php) in your browser to test.

The application uses a MySQL database (user and database `u102838416_pdilo`, password `Rovetto5!`).

Visit [http://localhost:8000/admin.php](http://localhost:8000/admin.php) and use **admin123** as password to manage products, categories and appearance settings including the WhatsApp number.
