# QuoteShare

A PHP MVC web application, developed as part of the **Web Development course at FMI**.

## ℹ️ About the project

This is a **quote sharing platform** that allows users to share and interact with quotes. The main functionalities include:

- 📝 Posting custom quotes
- ❤️ Interacting (liking, saving, and reporting) with quotes
- 📚 Organizing quotes into collections
- 📄 Exporting collections as PDF files

Admins have access to an advanced dashboard to:

- 📊 Visualize quote-related statistics (e.g., most liked or reported quotes)
- 👥 Manage user roles
- 🗑️ Delete user accounts
- 🕵️ Monitor system activity via logs

---

## 🔧 Technologies used

- **PHP** for the server-side scripts used to build the core application logic, following the MVC (Model-View-Controller) architectural pattern

- **JavaScript** for interactivity and improved user experience.

- **MariaDB (MySQL-compatible)** for database

- **Docker & Docker Compose** for containerization

- **Composer** - PHP dependency manager used to install and manage libraries and autoloading.

---

## ⚙️ Quick setup

> 💡 **Prerequisites:** Docker, Docker Compose, Bash

1. **Clone the repository**
   ```
   git clone https://github.com/nikola-enter21/php-project.git
   ```
2. **Go into the project directory**
   ```
   cd php-project
   ```
3. **Run the setup script for running the Docker containers for the database and the PHP server**
   ```
   ./start.sh
   ```
4. **Open [localhost:8000](localhost:8000) in your browser**

---

## 📁 Project structure

```
├── app/ → Models, views, controllers
├── core/ → Internal logic (routing, database, utilities)
├── config/ → App/database config
├── public/ → CSS and JS files
├── migrations/ → Database schema changes
├── seeds/ → Seed database with initial/sample data
├── docker-compose.yml
├── Dockerfile
├── index.php → Dependency injection + setting up app routes
└── start.sh → Script for setting up the application
```
