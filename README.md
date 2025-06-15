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
- **JavaScript** for interactivity and improved user experience
- **MariaDB (MySQL-compatible)** for the database
- **Docker & Docker Compose** for containerization

---

## ⚙️ Quick setup

> 💡 **Prerequisites:**  
> For Method 1: XAMPP  
> For Method 2: Docker

### ✅ Method 1: XAMPP

1. **Move the project into your `htdocs` directory**  
   Example:
   ```
   /path/to/htdocs/quoteshare
   ```

2. **Configure the database connection**  
   Edit `config/database.php` and update it with your local database credentials.

3. **Create the database tables**  
   Run the migration script:
   ```
   ./migrations/db.sql
   ```

4. **Seed the database with an initial admin user**  
   Run the seed script:
   ```
   ./seeds/seed.sql
   ```

5. **Open the app in your browser**  
   Visit: [http://localhost/quoteshare](http://localhost/quoteshare)

---

### 🐳 Method 2: Run using Docker (alternative)

1. **Start the app in Docker by running:**
   ```
   ./start.sh
   ```

2. **Open the app in your browser**
   Visit: [http://localhost:8000](http://localhost:8000)

---

## 📁 Project structure

```
├── app/              → Models, views, controllers
├── core/             → Internal logic (routing, database, utilities)
├── config/           → App/database config
├── public/           → CSS, JS, Images
├── migrations/       → Database schema changes
├── seeds/            → Seed database with initial/sample data
├── docker-compose.yml
├── Dockerfile
├── index.php         → Entrypoint
└── start.sh          → One-click setup
```
