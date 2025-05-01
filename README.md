<div align="center">
    <a href="https://404notfound.fun" target="_blank">
        <img src="./public/404NFID.png" 
            width="100" 
            alt="404NFID Logo">
    </a>
</div>

# SaaS Starter Kit

SaaS boilerplate built with **Laravel** and **Vue.js**, designed to help you kickstart your next Software-as-a-Service project with ease. It comes preloaded with essential features so you can focus on what matters most — building your product.

## ✨ Features

- 🚀 Full authentication flow (Login, Register, Forgot Password)
- 🧾 Pricing plans and subscription management
- 💳 Payment integration via **Midtrans**
- 📄 Invoice generation and download
- 👤 User profile & account settings
- 🎨 Frontend built with Vue.js + Tailwind CSS
- 🧱 Backend powered by Laravel + Sanctum

## 🛠️ Tech Stack

- **Laravel 12** – Backend API
- **Vue 3 + Vite** – Reactive frontend
- **Tailwind CSS** – Utility-first CSS framework
- **MySQL** – Relational database

## 📦 Installation

```bash
# Clone the repository
git clone https://github.com/404NotFoundIndonesia/laravel-vue-saas-starter-kit.git
cd laravel-vue-saas-starter-kit

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

MERCHANT_ID=
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=

PROFILE_ADDRESS=
PROFILE_CITY=
PROFILE_EMAIL=
PROFILE_PHONE=
```

### 💳 Setup Midtrans

1. Go to your [Midtrans Dashboard](https://dashboard.midtrans.com/)
2. Navigate to `Settings > Configuration`
3. Set the **Payment Notification URL** to `https://<domain.com>/api/webhook/payment/midtrans`


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

### ✨ Add New Feature for Pricing Plan
`Feature` here refers to the functionalities provided under a pricing plan that users can subscribe to. Each subscribed plan comes with its own feature limitations. For example, a built-in feature like `max_device_login` controls how many devices can log in using the same account.  

To create a new feature, use the following command:

```bash
php artisan make:feature
```

Once done, open the file `app/Enum/Feature.php` and adjust the `defaultValue` and `current` methods accordingly.

```php
enum Feature: string
{
    // ...

    public function defaultValue(): string|int
    {
        return match ($this) {
            self::MaxDeviceLogin => 1,
            // default value for new feature here
            default => '',
        };
    }

    public static function current(User $user): array
    {
        return [
            self::MaxDeviceLogin->value => DB::table('sessions')
                ->where('user_id', $user->id)
                ->count('id'),
            // current user usage for new feature here
        ];
    }
}
```

## 📄 License

This project is proprietary software.  
© 2025 [404NotFoundIndonesia](https://github.com/404NotFoundIndonesia) – All rights reserved.  
Use of this software is governed by the [SaaS Starter Kit Software License Agreement](LICENSE).


---

**SaaS Starter Kit** — Let your special moments orbit the stars. 🌙
