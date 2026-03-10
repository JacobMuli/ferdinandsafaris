# Ferdinand Safaris

Ferdinand Safaris is a luxury safari booking and management platform built with Laravel 12. It provides a seamless experience for users to browse, book, and manage high-end safari tours.

## Features

- **Luxury Tours:** Browse a curated selection of premium safari experiences.
- **Booking Management:** Real-time booking and itinerary tracking.
- **AI Integration:** Enhanced user experience through OpenAI-powered recommendations and assistants.
- **Secure Payments:** Integrated with Stripe for safe and reliable transactions.
- **Real-time Updates:** Powered by Laravel Reverb for live notifications and status updates.

## Prerequisites

- PHP 8.4+
- Node.js & NPM
- Composer
- SQLite (or another supported database)

## Installation & Setup

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

## Development

To start the development server, queue listener, logs, and Vite watcher simultaneously, run:

```bash
npm run dev
```

The application will be accessible at `http://localhost:8000`.

## Testing

Run the test suite using PHPUnit and Pest:

```bash
composer test
```

## License

This project is proprietary and private. All rights reserved by Ferdinand Safaris.
