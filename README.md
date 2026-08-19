# As-Salam Meeting Minute Management System

A simple web-based meeting minute management system developed using Laravel to help organizations record, manage, track, and organize meeting minutes and follow-up actions.

## Project Overview

The As-Salam Meeting Minute Management System is a web-based application designed to simplify the process of managing meeting minutes.

The system allows users to record meeting information, document discussions and decisions, manage action items, and keep track of important follow-up dates.

The system was developed using the Laravel framework with a MySQL database.

## Features

- Create and manage meeting records
- Record meeting minutes
- Record decisions and action items
- Track action taken as well as deadline
- Edit and delete meeting records
- Search and view meeting records

## Technology Stack

- **Framework:** Laravel
- **Backend:** PHP
- **Frontend:** Blade Templates, HTML, CSS, JavaScript
- **CSS/UI:** Bootstrap, Tailwind CSS
- **Database:** MySQL
- **Build Tool:** Vite
- **Development Environment:** XAMPP
- **Version Control:** Git / GitHub

## Database Design

The system uses a MySQL relational database.

The database structure was designed using an Entity Relationship Diagram (ERD).

## Requirements

Before running the project, make sure the following are installed:

- PHP
- Composer
- Node.js and npm
- MySQL
- XAMPP
- Git

## Installation & Setup

### 1. Clone the repository

git clone YOUR_REPOSITORY_URL

cd your-project-folder

### 2. Install PHP dependencies

composer install

### 3. Create the environment file

Copy `.env.example` to `.env`.

### 4. Generate the application key

php artisan key:generate

### 5. Configure the database

Open the `.env` file and configure the MySQL database:

DB_DATABASE=as_salam_meeting
DB_USERNAME=root
DB_PASSWORD=

### 6. Run database migrations

php artisan migrate

### 7. Install frontend dependencies

npm install

### 8. Build frontend assets

npm run build

### 9. Start the Laravel development server

php artisan serve

### 10. Create Laravel runtime directories

If the `storage` directories are not present after cloning, create the required Laravel runtime directories before starting the application.

Using git create:
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

## Running the Application

After starting the Laravel development server, open:

http://127.0.0.1:8000

## Project Structure

---text
app/          Application logic
bootstrap/    Laravel framework bootstrap files
config/       Application configuration
database/     Migrations and database-related files
public/       Publicly accessible files
resources/    Views and frontend resources
routes/       Application routes
tests/        Automated tests
---

## Future Improvements

- Add audio automated meeting transcription
- Improve meeting reminder and notification functionality
- Add role-based access control
- Add LLM summarizer to summarize meeting minutes
- Enhance UI/UX
