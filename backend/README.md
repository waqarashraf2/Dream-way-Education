# Dream Way Education — Laravel Backend API

A clean, standalone Laravel backend API for **Dream Way Education** contact inquiries and lead management.

---

## 🚀 Features

- **Contact Form Submission Endpoint**: `POST /api/contact` with validation, IP logging, user agent tracking, and JSON responses.
- **Inquiries Management Endpoints**:
  - `GET /api/contact-inquiries` — List inquiries with search, pagination, and status filters.
  - `GET /api/contact-inquiries/stats` — Summary metrics (total, unread, read, contacted, closed, today).
  - `GET /api/contact-inquiries/{id}` — View single inquiry (auto marks as read).
  - `PATCH /api/contact-inquiries/{id}/status` — Update inquiry status (`unread`, `read`, `in_progress`, `contacted`, `closed`).
  - `DELETE /api/contact-inquiries/{id}` — Delete inquiry.
- **Healthcheck**: `GET /api/health`
- **CORS Configured**: Ready for frontend connection on Vite (`http://localhost:5173` or any custom domain).
- **Database**: Configured with MySQL (`dreamway_education` database in XAMPP).

---

## 🛠️ Requirements

- PHP >= 8.2
- Composer
- MySQL / MariaDB (via XAMPP)

---

## 📦 Setup & Installation

1. Navigate to the backend directory:
   ```bash
   cd backend
   ```

2. Make sure your `.env` is configured with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dreamway_education
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Run migrations:
   ```bash
   php artisan migrate
   ```

4. Start the Laravel development server:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

---

## 📡 API Endpoints Overview

### 1. Submit Contact Form
- **Endpoint**: `POST /api/contact`
- **Headers**: `Content-Type: application/json`, `Accept: application/json`
- **Request Body**:
  ```json
  {
    "name": "Ali Hassan",
    "email": "alihassan@example.com",
    "phone": "+923013037141",
    "country": "United Kingdom",
    "service": "University Admissions",
    "message": "I want to apply for Fall 2026 intake."
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Thank you for contacting Dream Way Education! Our counselors will review your profile and get back to you shortly.",
    "data": {
      "id": 1,
      "name": "Ali Hassan",
      "email": "alihassan@example.com",
      "phone": "+923013037141",
      "country": "United Kingdom",
      "service": "University Admissions",
      "created_at": "2026-09-30T06:18:28+00:00"
    }
  }
  ```

### 2. List All Inquiries
- **Endpoint**: `GET /api/contact-inquiries`
- **Query Params**:
  - `status`: `unread` | `read` | `contacted` | `in_progress` | `closed`
  - `search`: keyword to search across name, email, phone, message
  - `per_page`: number of records per page (default: 15)

### 3. Summary Statistics
- **Endpoint**: `GET /api/contact-inquiries/stats`
