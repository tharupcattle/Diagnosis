# Tharup - Cattle Health Monitoring System

Project "Tharup" is designed to protect cattle from modern health issues, specifically focusing on digestive system anomalies like Acidosis and Bloat.

## Key Features
- **Real-time Monitoring**: Tracks pH levels, Rumination rates, and Body temperature.
- **Instant Alerts**: Notifies farmers when critical health thresholds are breached.
- **AI Diagnostics**: Predictive indicators for digestive issues.
- **Premium Dashboard**: Simple yet powerful UI for farmers.

## Technical Stack
- **Backend**: Laravel 12
- **Database**: SQLite (Default)
- **Frontend**: Tailwind CSS
- **Assets**: AI-generated cinematic farming imagery.

## Getting Started
1. **Navigate to project**:
   ```bash
   cd tharup
   ```
2. **Install Dependencies**:
   ```bash
   composer install
   npm install && npm run dev
   ```
3. **Database Setup**:
   ```bash
   php artisan migrate --seed
   ```
4. **Run Server**:
   ```bash
   php artisan serve
   ```

## Architecture
See `app/Models/Cattle.php`, `HealthLog.php`, and `Alert.php` for the core data structure.
The UI is defined in `resources/views/welcome.blade.php` and `dashboard.blade.php`.
