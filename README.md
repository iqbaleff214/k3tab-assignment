<div align="center">
    <a href="https://404notfound.fun" target="_blank">
        <img src="./public/404NFID.png" 
            width="100" 
            alt="404NFID Logo">
    </a>
</div>

# Laravel Vue Starter Kit

## 🛠️ Tech Stack

- **Laravel 12** – Backend API
- **Vue 3 + Vite** – Reactive frontend
- **Tailwind CSS** – Utility-first CSS framework
- **MySQL** – Relational database
- **AI Agent** – Gemini API

## 📦 Installation

```bash
# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate
```

### 🔐 Environment Configuration

```
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password
GEMINI_API_KEY=
```

### ▶️ Finishing

```bash
# Run migrations
php artisan migrate --seed

# Install frontend dependencies
npm install
npm run dev
```

## 🧪 Development

```bash
php artisan serve
php artisan queue:work
npm run dev
```

## 📄 License

This project is proprietary software.  
© 2025 [404NotFoundIndonesia](https://github.com/404NotFoundIndonesia) – All rights reserved.  
Use of this software is governed by the [Laravel Vue Starter Kit Software License Agreement](LICENSE).

