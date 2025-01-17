![PHP](https://www.php.net/images/logos/new-php-logo.svg) 

# CV/Portfolio Project in PHP

## Description
This project is a CV/Portfolio website in PHP. Its objective is to allow users to create and manage their own CV and portfolio, with administration, personalization and user management functionalities. The project follows PHP development best practices and uses a database to store and persist information.
## Technologies

- **Server :** Docker, Nginx
- **Client :** PHP
- **Data Base :** MySql

##  Dependencies

- PHP 8.0 or higher ![Version](https://img.shields.io/badge/php->=_8.0-blue)
- Docker
- Bootstrap
- mPDF - [![GitHub](https://img.shields.io/badge/GitHub-000?logo=github&logoColor=white)](https://github.com/mpdf/mpdf)

## Features

- User creation and connection
- CV management
- Project management
- Manage your profile


## Installation

Clone project on Git

```bash
git clone https://github.com/erwnn20/PHP-TP.git
cd PHP-TP/
```

#### Install php dependencies
```bash
composer require mpdf/mpdf
```

#### Environment variables
Create a `.env` file in `PHP-TP/` and put this variables in it
```dotenv
## DB
MYSQL_DATABASE: "cv_db"
MYSQL_ROOT_PASSWORD: "" # choose db access password (user root). 
# If the chosen password doesn't work, try 'root'.
```

## Deployment

To deploy this project run

```bash
cd Docker/
docker-compose up
```

Then, open a browser and go to ```127.0.0.1```

### Setup

Before you can start browsing the site, you need to initialize the database.
To do this, go to ```127.0.0.1:8080```, log in and execute the requests of ```config/db.sql file``` there :
![Adminer screenshot](docs/img/sql_setup.png)

Also check that all ```.php``` files are encoded in ```UTF-8 without BOM```.

Once you've done all this, you're ready to go.
# Welcome to our CV creation website

### Home Page
![Home Page](docs/img/homepage.png)

### Resume Page
![Resume Page](docs/img/cv-resume-page.png)

### CV creation Page
![CV creation Page](docs/img/cv-edit-page.png)

### Project creation Page
![Project creation Page](docs/img/project-edit-page.png)


## Authors

- Erwann Varlet - [@erwnn_](https://www.github.com/erwnn20)

