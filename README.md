# DBQ – PHP Query Builder for MySQL

## Description

**DBQ** is a lightweight and extendable **PHP Query Builder** designed to facilitate seamless interaction with MySQL databases. It provides an intuitive interface for:

- **Table Management**: Effortlessly create and manage database tables.
- **CRUD Operations**: Perform Create, Read, Update, and Delete operations with minimal code.
- **Query Building**: Construct complex SQL queries using a fluent and expressive syntax.

This framework can be easily integrated into any PHP project using Composer.

## Features

- **Database Connection**: Simplified methods to connect to MySQL databases.
- **Table Creation**: Define tables with specified attributes and constraints.
- **Data Insertion**: Insert records into tables with ease.
- **Data Retrieval**: Fetch records with support for custom conditions.
- **Data Update**: Update existing records based on specified conditions.
- **Data Deletion**: Remove records from tables with specified conditions.
- **Query Builder**: Build complex SQL queries using a fluent interface.

## Installation via Composer

To use the **DBQ Query Builder** in your project, follow these steps:

### 1. Install Composer

Ensure that [Composer](https://getcomposer.org/) is installed on your system. If it's not installed yet, you can install it using the following command:

```bash
curl -sS https://getcomposer.org/installer | php
````

### 2. Add the Package to Your Project

```json
{
    "require": {
        "archerbyte/php-dbq": "dev-develop"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/archerbyte/php-dbq.git"
        }
    ],
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Then run:

```bash
composer install
```

This will install the package from the `develop` branch and download all necessary files.

### 3. Enable Autoloading

Ensure that Composer's autoloader is included in your project. Add this line at the beginning of your PHP file:

```php
require 'vendor/autoload.php';
```

### 4. Use the Package

You can now use the package in your project. For more information on how to use it, refer to the [Usage](#usage) section of this documentation.

## Usage

### Example: Define Table and Insert Data

```php
use archerbyte\DBQ;
use archerbyte\DBAttribute;

$dbq = new DBQ();
$dbq->connectMySQL('localhost', 'database_name', 'username', 'password');

// Define table attributes
$attributes = [
    new DBAttribute('ID', 'INT', 11, false, true),
    new DBAttribute('username', 'VARCHAR', 255, true),
];

// Create table
$dbq->createTable('users', $attributes);

// Insert data
$dbq->insert('users', [
    'username' => 'testuser',
]);
```

### Example: Select Data with Conditions

```php
use archerbyte\QB;

$qb = (new QB())->add('username', 'testuser');
$users = $dbq->selectWithQB('users', $qb);
```

### Example: Update Data

```php
$qb = (new QB())->add('ID', 1);
$dbq->updateWithQB('users', $qb, ['username' => 'updateduser']);
```

### Example: Delete Data

```php
$qb = (new QB())->add('ID', 1);
$dbq->deleteWithQB('users', $qb);
```

## Available Methods

* **connectMySQL**: Establishes a connection to the MySQL database.
* **createTable**: Creates a new table with specified attributes.
* **insert**: Inserts data into a specified table.
* **select**: Retrieves data from a specified table.
* **selectWithQB**: Retrieves data from a specified table with query builder conditions.
* **updateWithQB**: Updates data in a specified table with query builder conditions.
* **deleteWithQB**: Deletes data from a specified table with query builder conditions.

## License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

### Contributing

Feel free to fork this project, make improvements, and submit pull requests. Contributions are always welcome!

---
