# Bioskop Lara

A Modern, highly-functional Cinema Management and Point-of-Sale (POS) System built with Laravel 12.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white)

---

## Overview

**Bioskop Lara** is a comprehensive application designed for cinemas to handle daily operations smoothly. It offers an intuitive **Single Page Application (SPA)** feel utilizing AJAX, providing users with a seamless, fast, and modern interface.

With dedicated interfaces for both **Administrators** and **Cashiers**, the system handles everything from managing movie schedules and studio configurations to processing ticket transactions and tracking business analytics.

## Key Features

### Role-Based Access Control

- **Admin**: Full access to master data (Movies, Studios, Schedules), comprehensive analytics, and system settings.
- **Cashier**: Focused interface for daily operations—selling tickets, managing shift schedules, and viewing daily transaction summaries.

### Interactive Point-of-Sale (POS)

- Real-time seat layout visualization and selection.
- Dynamic pricing logic (weekend surcharges, VIP studio premiums).
- Seamless payment handling (cash calculations, change tracking, transfer options).

### Powerful Analytics Dashboard

- Track daily/monthly revenue and active cashiers.
- Visual insights via **ApexCharts**: Peak transaction hours, most popular movies, and studio distribution.
- Real-time activity logging utilizing `spatie/laravel-activitylog`.

### SPA-like Experience

- Fast navigation using AJAX page transitions without full page reloads.
- Beautiful UI components via Bootstrap 5, SweetAlert2, and Select/Choices.js.

## Tech Stack

**Backend:**

- [Laravel 12.x](https://laravel.com/)
- [PHP 8.2+](https://www.php.net/)
- Packages: `spatie/laravel-activitylog`, `yajra/laravel-datatables-oracle`

**Frontend:**

- Blade Templates
- Bootstrap 5 (Azira Template)
- jQuery & Modern ES6+ JavaScript
- ApexCharts (Data Visualization)
- SweetAlert2 & Choices.js

## Installation & Setup

### 1. **Clone the repository**

```bash
  git clone https://github.com/wafley/bioskop-lara.git
  cd bioskop-lara
```

### 2. **Install dependencies**

```bash
  composer update
```

### 3. **Environment Setup**

```bash
  cp .env.example .env
  php artisan key:generate
```

_Configure your database credentials in the `.env` file._

### 4. **Database Migration & Seeding**

This will populate the database with dummy movies, studios, schedules, and users.

```bash
  php artisan migrate
  php artisan db:seed DatabaseSeeder
```

### 4. Setup Assets (Important)

Since image assets and frontend libraries are not included in the Git repository, you need to download them manually:

1. **Download Assets**: Open the following Google Drive link:
   [https://drive.google.com/file/d/1JWO4uBdn4bjp7cZk75SNBzyx-DHZNGPZ/view?usp=sharing](https://drive.google.com/file/d/1JWO4uBdn4bjp7cZk75SNBzyx-DHZNGPZ/view?usp=sharing)
2. **Extract File**: Extract the downloaded `.zip` file
3. Move/copy the extracted folder into: `public/`

### 6. **Run the Application**

```bash
php artisan serve
```

## Default Credentials (Seeded)

| Role    | Username  | Password  |
| ------- | --------- | --------- |
| Admin   | `admin`   | `admin`   |
| Cashier | `winandi` | `winandi` |

## 🤝 Contributing

Contributions, issues, and feature requests are highly welcome! This project aims to demonstrate clean architecture, robust features, and modern Laravel practices.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'feat: Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.
