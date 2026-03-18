# PHP_Laravel12_ERD


## Project Description

PHP_Laravel12_ERD is a simple Laravel 12 application that demonstrates how to generate an Entity Relationship Diagram (ERD) from database tables and model relationships.

This project uses the laravel-erd package (recca0120/laravel-erd) to automatically visualize database structure, including tables, columns, and relationships.

It helps developers easily understand how different tables are connected in a Laravel application.


## Features

- Generate ER Diagram automatically from database

- Supports Eloquent relationships (hasMany, belongsTo)

- Visual representation of tables and foreign keys

- Beginner-friendly implementation

- Works with Laravel 12

- Clean and interactive UI for ERD visualization

- Helps in database design and debugging



## Technologies Used

- PHP 8+

- Laravel 12

- MySQL

- Composer

- laravel-erd package



## How It Works

1. Migrations create database tables (users, posts, comments)
2. Models define relationships using Eloquent ORM
3. The ERD package scans database schema and relationships
4. It generates a visual ER diagram automatically
5. The diagram is displayed in the browser





---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_ERD "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_ERD

```

#### Explanation:

This command creates a fresh Laravel 12 project using Composer. 

It sets up the basic folder structure and required dependencies to start development.




## STEP 2: Database Setup 

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_ERD
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_ERD

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

Here, we configure the database connection in the .env file. 

Running migrations creates default Laravel tables in the database.





## STEP 3: Install Laravel ERD Package

### Install package:

```
composer require recca0120/laravel-erd --dev --ignore-platform-req=ext-sqlite3

```



#### Explanation:

This installs the laravel-erd package, which automatically generates an ER diagram from your database schema and relationships.





## STEP 4: Create Models + Migrations

### Run:

```
php artisan make:model Post -m

php artisan make:model Comment -m

```


### Edit: database/migrations/create_posts_table.php

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};


```


### Edit: database/migrations/create_comments_table.php

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};


```


### Then Run:

```
php artisan migrate

```

#### Explanation:

This step creates models and their corresponding migration files. 

Migrations define the database table structure like columns and foreign keys.





## STEP 5: Add Relationships 

### User Model

```
public function posts()
{
    return $this->hasMany(Post::class);
}

```

### Post Model

```
public function user()
{
    return $this->belongsTo(User::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}

```


### Comment Model

```
public function post()
{
    return $this->belongsTo(Post::class);
}

```

#### Explanation:

Relationships connect tables using Eloquent ORM. 

These relationships (hasMany, belongsTo) are used by the ERD package to draw connections between tables.





## STEP 6: Generate ERD

### Run:

```
php artisan erd:generate

```

#### Explanation:

This command scans your database and model relationships, then generates a visual Entity Relationship Diagram automatically.




## STEP 7: Run the App

### Start dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000/laravel-erd

```

#### Explanation:

This starts the Laravel development server and allows you to view the generated ERD in your browser.




## Expected Output:


<img src="screenshots/Screenshot 2026-03-18 124948.png" width="900">

<img src="screenshots/Screenshot 2026-03-18 124958.png" width="900">




---

## Project Folder Structure:

```
PHP_Laravel12_ERD/
│
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Comment.php
│   │
│   ├── Http/
│   ├── Providers/
│
├── bootstrap/
│
├── config/
│   ├── app.php
│   ├── database.php
│
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 2024_XX_XX_create_users_table.php
│   │   ├── 2024_XX_XX_create_posts_table.php
│   │   ├── 2024_XX_XX_create_comments_table.php
│   │
│   ├── seeders/
│
├── public/
│   ├── index.php
│
├── resources/
│   ├── views/
│
├── routes/
│   ├── web.php
│
├── storage/
│   ├── framework/
│   │   ├── cache/
│   │   │   └── laravel-erd/   ← ERD generated files stored here
│
├── screenshots/
│   ├── Screenshot 2026-03-18 124948.png
│   ├── Screenshot 2026-03-18 124958.png
│
├── .env
├── artisan
├── composer.json
├── package.json
├── README.md

```
