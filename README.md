<h1 align="center">
  <img src="https://readme-typing-svg.herokuapp.com?size=28&duration=4000&color=3FA9F5&center=true&vCenter=true&width=600&lines=Progress+Tracker;Build+Habits.;Track+Progress.+Win+Consistently." />
</h1>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-f9322c?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38bdf8?style=for-the-badge&logo=tailwindcss&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-5.x-646cff?style=for-the-badge&logo=vite&logoColor=white" />
  <img src="https://img.shields.io/badge/Status-Active-brightgreen?style=for-the-badge" />
</p>


# Progress Tracker 🎯

A modern goal tracking system built with Laravel 11, featuring a premium dark UI with glass morphism design. Track your goals, maintain streaks, and build consistent habits.

# 📸 Screenshots

### 🏡 Homepage  
<img src="/home-page.png"/>

---

### 🔑 Login Page  
<img src="/login-page.png"/>

---

### 📝 Register Page  
<img src="/registe-page.png"/>

---

### 📊 Dashboard  
<img src="/dashboard-page.png"/>

---

### 🎯 Goals List  
<img src="/goals-page.png"/>

---

### ➕ Create Goal  
<img src="/create-goal-page.png"/>

---

## Features

- **Goal Management**: Create, edit, complete, and reopen goals with ease
- **Progress Tracking**: Visual progress bars with percentage completion
- **Streak System**: Daily streak tracking to maintain consistency
- **User Authentication**: Secure login and registration with Laravel Breeze
- **Premium UI**: Dark theme with glass morphism, neon gradients, and smooth animations
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Dashboard Analytics**: Overview of total goals, completed goals, and active streaks

## Tech Stack

| Layer | Technologies |
|-------|-------------|
| Frontend | TailwindCSS 3.x, Alpine.js 3.x, Vite 5.x |
| Backend | Laravel 11, PHP 8.2+, Eloquent ORM |
| Authentication | Laravel Breeze |
| Database | MySQL 8.0+ / SQLite |
| UI Design | Glass Morphism, Neon Glow Effects, Dark Mode |

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's display name
- `email` - Unique email address
- `password` - Hashed password
- `created_at`, `updated_at` - Timestamps

### Goals Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `title` - Goal title (max 255 chars)
- `description` - Detailed goal description (nullable)
- `target_value` - Target number for completion (default: 1)
- `current_value` - Current progress (default: 0)
- `status` - Enum: 'active', 'completed', 'archived'
- `completed_at` - Timestamp when goal was completed (nullable)
- `deleted_at` - Soft delete timestamp (nullable)
- `created_at`, `updated_at` - Timestamps

### Streaks Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `current_streak` - Current consecutive days (default: 0)
- `longest_streak` - Best streak achieved (default: 0)
- `last_activity_date` - Last date user completed a goal
- `created_at`, `updated_at` - Timestamps

## Project Structure

```
progress-tracker/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── GoalController.php      # CRUD operations for goals
│   │   │   ├── DashboardController.php # Dashboard stats and overview
│   │   │   └── ProfileController.php   # User profile management
│   │   └── Requests/
│   │       ├── StoreGoalRequest.php    # Validation for creating goals
│   │       └── UpdateGoalRequest.php   # Validation for updating goals
│   ├── Models/
│   │   ├── User.php                    # User model with relationships
│   │   ├── Goal.php                    # Goal model with soft deletes
│   │   └── Streak.php                  # Streak tracking model
│   └── Services/
│       └── StreakService.php           # Business logic for streak calculation
├── database/
│   ├── migrations/
│   │   ├── 2024_xx_create_goals_table.php
│   │   └── 2024_xx_create_streaks_table.php
│   └── seeders/
│       └── GoalSeeder.php              # Sample data for development
├── resources/
│   ├── views/
│   │   ├── dashboard.blade.php         # Main dashboard
│   │   ├── goals/
│   │   │   ├── index.blade.php        # Goals list
│   │   │   ├── create.blade.php       # Create goal form
│   │   │   ├── edit.blade.php         # Edit goal form
│   │   │   └── show.blade.php         # Single goal view
│   │   └── layouts/
│   │       └── app.blade.php          # Main layout with navigation
│   ├── css/
│   │   └── app.css                    # Tailwind and custom styles
│   └── js/
│       ├── app.js                     # Alpine.js components
│       └── streak.js                  # Streak calculation logic
├── routes/
│   └── web.php                        # Application routes
├── tests/
│   ├── Feature/
│   │   ├── GoalManagementTest.php     # Feature tests for goals
│   │   └── StreakCalculationTest.php  # Feature tests for streaks
│   └── Unit/
│       └── StreakServiceTest.php      # Unit tests for streak service
└── README.md
```

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher
- MySQL 8.0+ or SQLite
- Git

### Setup Instructions

1. **Clone the repository**
```bash
git clone https://github.com/ShAiDSk/Progress_Tracker.git
cd Progress_Tracker
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install JavaScript dependencies**
```bash
npm install
```

4. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure your database**

Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=progress_tracker
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

For SQLite (simpler for development):
```env
DB_CONNECTION=sqlite
# DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD can be commented out
```

If using SQLite, create the database file:
```bash
touch database/database.sqlite
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Seed sample data (optional)**
```bash
php artisan db:seed
```

8. **Build frontend assets**
```bash
npm run dev
```

9. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Usage

### Creating a Goal
1. Navigate to the Goals page
2. Click "Create New Goal"
3. Fill in the title, description, and target value
4. Submit the form

### Tracking Progress
1. Click on any goal to view details
2. Click "Update Progress" button
3. Increment the current value
4. Progress bar updates automatically

### Streak System
- Streaks increment when you complete at least one goal per day
- Missing a day resets your current streak
- Your longest streak is always saved
- View your current and best streaks on the dashboard

## Validation Rules

### Goal Creation
- **Title**: Required, max 255 characters
- **Description**: Optional, max 1000 characters
- **Target Value**: Required, integer, minimum 1, maximum 1000

### Goal Update
- **Current Value**: Integer, minimum 0, cannot exceed target value
- **Status**: Must be one of: 'active', 'completed', 'archived'

## Error Handling

The application includes comprehensive error handling:
- **404 Errors**: Custom page for not found resources
- **422 Validation Errors**: User-friendly validation messages
- **500 Server Errors**: Graceful error page with support info
- **Authorization**: Middleware ensures users can only access their own goals

## Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## API Routes

The application uses standard RESTful routing:

```
GET    /dashboard          # Dashboard overview
GET    /goals              # List all goals
GET    /goals/create       # Show create form
POST   /goals              # Store new goal
GET    /goals/{id}         # Show single goal
GET    /goals/{id}/edit    # Show edit form
PUT    /goals/{id}         # Update goal
DELETE /goals/{id}         # Soft delete goal
POST   /goals/{id}/complete # Mark goal as complete
POST   /goals/{id}/reopen   # Reopen completed goal
```

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code:
- Follows PSR-12 coding standards
- Includes appropriate tests
- Updates documentation as needed

## Security

If you discover any security vulnerabilities, please email [your-email@example.com] instead of using the issue tracker.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

**Designed & Developed by** Shaid SK ([itz-shaidsk](https://github.com/ShAiDSk))

## Support

If you find this project helpful, please give it a ⭐ on GitHub!

---

**Note**: Remember to add actual screenshots to the `docs/screenshots/` directory and update the image paths in this README.

---

# ❤️ Credits

Designed & Developed by **Shaid SK (itz-shaidsk)**

---

# ⭐ If you like this project

Give it a **star** on GitHub 🌟
Your support keeps the project moving forward 🚀

