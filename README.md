# Laravel Application Setup Guide

This guide walks you through the steps to clone and set up a Laravel application with MySQL.

## Prerequisites

Make sure you have the following installed:

- PHP >= 8.2
- Composer
- Laravel CLI (optional but helpful)
- MySQL or MariaDB


## Steps:

```bash
git clone https://github.com/ValonKadriaj/event-ticket.git
cd event-ticket
cp .env.example .env
php artisan key:generate
composer install
php artisan migrate --seed
php artisan serve
