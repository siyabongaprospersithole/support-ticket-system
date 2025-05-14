# Support Ticket System

A modern, feature-rich support ticket management system built with Laravel and Vue.js.

![Support Ticket System](https://img.shields.io/badge/Version-1.0.0-blue)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green)
![PHP](https://img.shields.io/badge/PHP-8.1+-purple)
![License](https://img.shields.io/badge/License-MIT-yellow)

## Key Features

- **Interactive Setup Wizard**: User-friendly first-time setup with database and mail configuration
- **Responsive Design**: Modern UI that works on desktops, tablets, and mobile devices
- **Ticket Management**: Create, view, filter, and search support tickets
- **Status and Priority**: Track tickets by status (Open/Closed) and priority (Low/Medium/High/Critical)
- **Comment System**: Add comments to tickets with real-time updates
- **Email Notifications**: Get notified about ticket updates via email
- **User Authentication**: Secure registration and login with password reset
- **Profile Management**: Edit user profiles and account settings
- **Persistent State**: No data loss during form submission or page refreshes
- **Caching**: Efficient data caching for improved performance
- **Comprehensive Unit Tests**: Extensive test coverage for core functionality

## Requirements

- PHP 8.1 or higher
- MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3+
- Node.js 14+ and NPM
- Composer 2.0+
- Web server (Apache/Nginx)

## Quick Installation

### Option 1: Using the Setup Wizard (Recommended)

1. Clone the repository
   ```bash
   git clone https://github.com/siyabongaprospersithole/support-ticket-system.git
   cd support-ticket-system
   ```

2. Install PHP dependencies
   ```bash
   composer install
   ```

3. Rename .env.example to .env
   ```bash
   # For Linux/Mac
   cp .env.example .env
   
   # For Windows (Command Prompt)
   copy .env.example .env
   
   # For Windows (PowerShell)
   Copy-Item .env.example .env
   ```

4. Generate environment key
   ```bash
   php artisan key:generate
   ```

5. Install JavaScript dependencies
   ```bash
   npm install
   ```

6. Build the frontend assets
   ```bash
   npm run build
   ```

7. Start the server
   ```bash
   php artisan serve
   ```

8. Visit `http://localhost:8000` and follow the interactive setup wizard

### Option 2: Manual Setup

1. Clone the repository
   ```bash
   git clone https://github.com/username/support-ticket-system.git
   cd support-ticket-system
   ```

2. Install dependencies
   ```bash
   composer install
   npm install
   ```

3. Copy the environment file and generate key
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the database in `.env`
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. Configure email (Mailtrap recommended for testing)
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=support@example.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. Run migrations and seed the database
   ```bash
   php artisan migrate --seed
   ```

7. Build the frontend assets
   ```bash
   npm run build
   ```

8. Start the server
   ```bash
   php artisan serve
   ```

9. Visit `http://localhost:8000` in your browser

## Running Tests

This application has comprehensive test coverage including feature tests and unit tests.

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Files

```bash
php artisan test --filter=AuthenticationTest
php artisan test --filter=TicketManagementTest
php artisan test --filter=CommentManagementTest
```

### Run Tests with Coverage Report

```bash
php artisan test --coverage
```

## Usage Guide

### User Roles

- **Guests**: Can view the welcome page
- **Authenticated Users**: Can create tickets, add comments, and manage their own tickets

### Ticket Management

1. **Creating a Ticket**:
   - Navigate to "New Ticket" in the navigation
   - Fill in the title, description, and select priority
   - Submit the form to create a ticket

2. **Viewing Tickets**:
   - Navigate to "Tickets" to see all tickets
   - Use filters to sort by status, priority, or date
   - Search tickets by title or content

3. **Ticket Details**:
   - Click on a ticket to view its details
   - See comments and ticket history
   - Change ticket status (open/closed)

4. **Adding Comments**:
   - Navigate to a ticket's detail page
   - Scroll to the comment section
   - Add your comment in the text area and submit

### Profile Management

1. **Editing Profile**:
   - Click on your profile name in the navigation
   - Select "Profile" from the dropdown
   - Update your information and save

2. **Changing Password**:
   - Go to your profile page
   - Click on "Update Password"
   - Enter your current and new password

## Application Structure

### Backend (Laravel)

- **Controllers**: Handle request/response logic
  - `TicketController`: Manages ticket CRUD operations
  - `CommentController`: Handles comments on tickets
  - `SetupController`: Manages the setup wizard
  - `ProfileController`: User profile management

- **Models**: Database structure and relationships
  - `User`: Authentication and user information
  - `Ticket`: Support ticket details and relationships
  - `Comment`: Ticket comments with user relationships

- **Middleware**: Request filters
  - `PreventEnvReload`: Prevents environment reloads during setup

- **Routes**: Application endpoints
  - `web.php`: Web interface routes
  - `auth.php`: Authentication routes

### Frontend (Vue.js)

- **Pages**: Main view components
  - `Dashboard`: User dashboard with stats
  - `Tickets/Index`: List of tickets with filters
  - `Tickets/Show`: Detailed ticket view with comments
  - `Tickets/Create`: Ticket creation form
  - `Setup/Index`: Setup wizard interface

- **Components**: Reusable UI elements
  - Form inputs
  - Buttons
  - Modals
  - Navigation

- **Layouts**: Page structures
  - `AuthenticatedLayout`: Layout for authenticated users
  - `GuestLayout`: Layout for unauthenticated users

### Storage

- **Migrations**: Database structure
- **Seeders**: Initial data population

## Detailed Feature Breakdown

### Setup Wizard

- **Requirements Check**: Verifies PHP extensions and server requirements
- **Database Configuration**: Configure database connection settings
- **Migration Runner**: Run database migrations through the interface
- **Mail Configuration**: Setup email for notifications
- **Finalization**: Complete setup and begin using the application
- **State Persistence**: Automatically saves progress in case of page refreshes

### Authentication

- **Registration**: Create a new account with name, email, and password
- **Login**: Secure login with email and password
- **Password Reset**: Forgot password functionality via email
- **Email Verification**: Optional email verification for new accounts
- **Profile Management**: Update name, email, and password

### Ticket Management

- **Create Tickets**: Submit new support requests
- **View Tickets**: List all tickets with filtering and sorting
- **Ticket Details**: View complete ticket information and history
- **Status Updates**: Change ticket status (open/closed)
- **Priority Levels**: Assign importance (low, medium, high, critical)
- **Search**: Find tickets by title, content, or status

### Comment System

- **Add Comments**: Contribute to ticket discussions
- **View History**: See all comments in chronological order
- **User Attribution**: Comments linked to specific users
- **Timestamps**: Track when comments were added

### Email Notifications

- **Ticket Creation**: Email notifications sent when a new ticket is created
- **Status Changes**: Get notified when ticket status changes from open to closed or vice versa
- **Comment Notifications**: Email notifications when new comments are added to tickets
- **Smart Recipients**:
  - Ticket creators receive all notifications about their tickets
  - Admin users receive notifications about new tickets
  - Users who have commented on a ticket receive status update notifications
  - Users don't receive notifications for their own actions
- **Detailed Emails**: Notifications include ticket details, status information, and direct links
- **Mailtrap Integration**: Easy email testing in development

## 🙏 Acknowledgements

- [Laravel](https://laravel.com/) - The PHP framework
- [Vue.js](https://vuejs.org/) - The JavaScript framework
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework
- [Inertia.js](https://inertiajs.com/) - Modern monolith architecture
- [Pinia](https://pinia.vuejs.org/) - Vue.js state management
