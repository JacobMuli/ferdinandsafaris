# Ferdinand Safaris

Ferdinand Safaris is a luxury safari booking and management platform built with Laravel 12. It provides a seamless experience for users to browse, book, and manage high-end safari tours.

## Features

- **Luxury Tours:** Browse a curated selection of premium safari experiences.
- **Booking Management:** Real-time booking and itinerary tracking.
- **AI Integration:** Enhanced user experience through OpenAI-powered recommendations and assistants.
- **Secure Payments:** Integrated with Stripe for safe and reliable transactions.
- **Real-time Updates:** Powered by Laravel Reverb for live notifications and status updates.

## Prerequisites

### Local Development

- PHP 8.2+
- Node.js & NPM
- Composer
- MySQL Database
- Redis (Optional but recommended)

### Server (Production)

See the [Deployment Checklist](deployment_checklist.md) for full production requirements and setup.

## Local Development Setup

1. **Clone the repository:**

   ```bash
   git clone <repository-url>
   cd ferdinand-safaris
   ```

2. **Run the setup script:**

   This script installs dependencies, sets up the environment, generates an application key, runs migrations, and builds frontend assets.

   ```bash
   composer setup
   ```

3. **Configure Environment Variables:**

   Update your `.env` file with necessary credentials for:
   - OpenAI (API Key)
   - Stripe (Public/Secret Keys)
   - Database connection

4. **Start Development Environment:**

   To start the development server, queue listener, logs, and Vite watcher simultaneously, run:

   ```bash
   npm run dev
   ```

The application will be accessible at `http://localhost:8000`.

## Server Deployment

For production deployment, we provide a dedicated [Deployment Checklist](deployment_checklist.md) and a deployment script.

### Automated Deployment

If your server is already configured, you can use the provided script:

```bash
./deploy.sh
```

### Manual Deployment Steps

Briefly:

1. `composer install --no-dev --optimize-autoloader`

2. `npm install && npm run build`

3. `php artisan migrate --force`

4. `php artisan optimize`

## Testing

Run the test suite using PHPUnit and Pest:

```bash
composer test
```

## License

This project is proprietary and private. All rights reserved by Ferdinand Safaris.
