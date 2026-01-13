# 📖 Diary App

A comprehensive, multi-language PHP diary application designed for preserving memories. Features deep integration with **Immich** for photos, **AI-powered** context extraction, and beautiful **PDF exports**.

## 🚀 Features

*   **✍️ Rich Text Editor**: Write beautifully with a TipTap-based editor supporting formatting and auto-saving.
*   **🖼️ Immich Integration**: Automatically fetches your daily photos from your Immich server. Includes a gallery to select photos and add captions.
    *   *Reference Only*: Keeps your storage clean by linking to Immich assets by default.
    *   *Optional Backup*: Settings allow for local backup of selected photos.
*   **🧠 AI Context Extraction**: 
    *   **Auto-Scan**: Detects people mentioned in your text as you type.
    *   **Hybrid Logic**: Uses local keyword matching + optional LLM (OpenAI-compatible) for smarter detection.
*   **📄 PDF Export**: Generate professional PDFs of your diary entries, complete with photos and front page.
    *   Supports large date ranges via background processing.
*   **👶 Children's Logs**: Dedicated section to log the daily status of your children (e.g., "School", "Home", "Sick").
*   **📧 Inactivity Reminders**: Sends a friendly email if you haven't written for a configurable number of days.
*   **⚙️ System Configuration UI**: Manage all settings (AI, Reminders, Immich) directly from the application.

---

## 🛠️ Installation & Setup

The application is containerized using Docker for easy deployment.

### Prerequisites

*   Docker & Docker Compose
*   An existing Immich instance (for photo integration)

### Quick Start

1.  **Clone the repository**:
    ```bash
    git clone <repository-url>
    cd diary
    ```

2.  **Environment Setup**:
    Copy the example environment file and configure your database/Immich credentials.
    ```bash
    cp .env.example .env
    ```
    *Update `IMMICH_URL` and `IMMICH_KEY` in `.env`.*

3.  **Start Containers**:
    This will spin up Nginx, PHP (App, Scheduler, Worker), MySQL, and Redis.
    ```bash
    docker-compose up -d --build
    ```

4.  **Initialize Application**:
    ```bash
    docker-compose exec app php artisan key:generate
    docker-compose exec app php artisan migrate --seed
    ```

5.  **Access**:
    Visit `http://localhost:8000` (or your configured port).
    *   **Default Admin**: `admin@example.com` / `password`

---

## ⚙️ Configuration

The application uses a **Hybrid Configuration System**:
1.  **.env**: For sensitive secrets (DB passwords, API Keys).
2.  **Database / UI Settings**: For runtime behavior. Go to **Settings** in the menu to configure:
    *   **AI**: Model name, Endpoint, Auto-scan delay.
    *   **Notifications**: Enable/Disable reminders, days threshold.
    *   **Immich**: Toggle "Copy Photos Locally".

---

## 🏗️ Infrastructure

The `docker-compose.yml` defines the following services:

*   **`app`**: The main PHP application server.
*   **`web`**: Nginx web server, proxying requests to `app`.
*   **`db`**: MySQL 8.0 database.
*   **`redis`**: Cache and Queue driver.
*   **`scheduler`**: Runs `php artisan schedule:work` (Cron jobs).
    *   *Tasks*: Immich Sync (Hourly), Reminders (Daily 19:00).
*   **`worker`**: Runs `php artisan queue:work`.
    *   *Tasks*: PDF Generation, Email sending.

## 📚 Commands

*   **Manually Sync Immich**: `php artisan app:sync-immich`
*   **Manually Send Reminders**: `php artisan app:send-inactivity-reminders`
