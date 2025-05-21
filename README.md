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

## 👟 Get Started

Let's walk through how to create a basic `Product` module with the following attributes: `id`, `name`, `price`, `created_at`, and `updated_at`.

---

### 🛠️ Migration & Model

First, generate the model and migration file:

```shell
php artisan make:model Product -m
```

You should see an output like this:

```
INFO  Model [app/Models/Product.php] created successfully.  
INFO  Migration [database/migrations/2025_05_21_092236_create_products_table.php] created successfully.
```

Next, open the generated migration file at
`database/migrations/2025_05_21_092236_create_products_table.php`
and update the schema as follows:

```php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('price')->default(0);
            $table->timestamps();
        });
    }

    // ...
};
```

Then, update the model located at `app/Models/Product.php`:

```php
namespace App\Models;

use App\Traits\Models\Filterable;
use App\Traits\Models\Paginate;
use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Filterable, Sortable, Paginate;

    protected $fillable = [
        'name', 'price'
    ];
}
```

---

### 🔐 Permissions & Localization

Add product-related permissions in the `app/Enum/Permission.php` enum:

```php
case ViewProduct = 'view_product';
case AddProduct = 'add_product';
case EditProduct = 'edit_product';
case DeleteProduct = 'delete_product';
case ApproveProduct = 'approve_product';

...

public static function module(): array
{
    return [
        'user', 'role',
        'product', // Add product module here
    ];
}
```

Then update the locale file `lang/id/menu.php` by adding the new module:

```php
return [
    "dashboard" => "Dasbor",
    "user" => "Pengguna",
    "role" => "Jabatan",
    "setting" => "Pengaturan",
    "profile" => "Profil",
    "password" => "Kata Sandi",
    "session" => "Sesi",
    "appearance" => "Tampilan",
    "delete_account" => "Hapus Akun",
    "logout" => "Keluar",
    "activity_log" => "Log",
    "product" => "Produk"
];
```

Repeat this update for other locale files such as `en`, `ar`, `ko`, `ja`, and `zh-CN`.

---

### ✅ Form Requests

Create form request classes for storing and updating products:

```shell
php artisan make:request Product/StoreRequest
```

Update the generated file:

```php
namespace App\Http\Requests\Product;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \App\Enum\Permission::AddProduct->isAllowed();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ];
    }
}
```

Repeat for update:

```shell
php artisan make:request Product/UpdateRequest
```

Update the file:

```php
namespace App\Http\Requests\Product;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \App\Enum\Permission::AddProduct->isAllowed();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ];
    }
}
```

---

### 🧭 Controller & Routes

Generate the controller:

```shell
php artisan make:controller ProductController --model=Product --resource
```

Update your route file `routes/web.php`:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/product', [\App\Http\Controllers\ProductController::class, 'index'])->name('product.index')->middleware(\App\Enum\Permission::ViewProduct->asMiddleware());
    Route::post('/product', [\App\Http\Controllers\ProductController::class, 'store'])->name('product.store')->middleware(\App\Enum\Permission::AddProduct->asMiddleware());
    Route::put('/product/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('product.update')->middleware(\App\Enum\Permission::EditProduct->asMiddleware());
    Route::delete('/product', [\App\Http\Controllers\ProductController::class, 'massDestroy'])->name('product.mass-destroy')->middleware(\App\Enum\Permission::DeleteProduct->asMiddleware());
    Route::delete('/product/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('product.destroy')->middleware(\App\Enum\Permission::DeleteProduct->asMiddleware());
});
```

Then update `ProductController.php`:

```php
public function index(\Illuminate\Http\Request $request): \Inertia\Response
{
    return \Inertia\Inertia::render('product/Index', [
        'items' => Product::query()
            ->sort($request->query('sorts'))
            ->filter($request->query('filters'))
            ->render($request->query('size')),
    ]);
}
```

```php
public function store(\App\Http\Requests\Product\StoreRequest $request): \Illuminate\Http\RedirectResponse
{
    try {
        $input = $request->validated();
        Product::create($input);
        return back()->with('success', __('action.created', ['menu' => __('menu.product')]));
    } catch (\Throwable $exception) {
        \Log::error($exception->getMessage());
        return back()->with('error', $exception->getMessage());
    }
}
```

```php
public function update(\App\Http\Requests\Product\UpdateRequest $request, Product $product): \Illuminate\Http\RedirectResponse
{
    try {
        $input = $request->validated();
        $product->update($input);
        return back()->with('success', __('action.updated', ['menu' => __('menu.product')]));
    } catch (\Throwable $exception) {
        \Log::error($exception->getMessage());
        return back()->with('error', $exception->getMessage());
    }
}
```

```php
public function destroy(Product $product): \Illuminate\Http\RedirectResponse
{
    try {
        $product->delete();
        return back()->with('success', __('action.deleted', ['menu' => __('menu.product')]));
    } catch (\Throwable $exception) {
        \Log::error($exception->getMessage());
        return back()->with('error', $exception->getMessage());
    }
}
```

```php
public function massDestroy(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
{
    try {
        $ids = $request->input('ids');
        if (empty($ids)) {
            throw new \Exception('Empty ids');
        }

        Product::query()->whereIn('id', $ids)->delete();
        return back()->with('success', __('action.deleted', ['menu' => __('menu.product')]));
    } catch (\Throwable $exception) {
        \Log::error($exception->getMessage());
        return back()->with('error', $exception->getMessage());
    }
}
```

---

### 💻 Frontend

Define the TypeScript interface for `Product` in `resources/js/types/index.d.ts`:

```typescript
export interface Product {
    id: number;
    name: string;
    price: number;
    created_at: Date;
    updated_at: Date;
}
```

Then, update the frontend locale file at `resources/locale/id.json` by adding the following translations:

```json
{
    "menu": {
        "dashboard": "Dasbor",
        "user": "Pengguna",
        "role": "Jabatan",
        "setting": "Pengaturan",
        "profile": "Profil",
        "password": "Kata Sandi",
        "session": "Sesi",
        "appearance": "Tampilan",
        "delete_account": "Hapus Akun",
        "logout": "Keluar",
        "activity_log": "Log",
        "product": "Produk" 
    },
    "field": {
        "name": "Nama",
        "email": "Surel",
        "password": "Kata Sandi",
        "password_confirmation": "Konfirmasi Sandi",
        "role": "Jabatan",
        "current_password": "Kata Sandi Sekarang",
        "new_password": "Kata Sandi Baru",
        "description": "Deskripsi",
        "module": "Modul",
        "created_at": "Waktu Dibuat",
        "updated_at": "Waktu Diperbarui",
        "joined_at": "Bergabung Pada",
        "permission": "Hak Akses",
        "price": "Harga"
    }
}
```

Make sure to update the translations in other locale files (en.json, ar.json, ko.json, ja.json, zh-CN.json) accordingly, to ensure consistent multilingual support throughout the application.

Then create the page file at `resources/js/pages/product/Index.vue`. You may refer to the existing `resources/js/pages/user/Index.vue` for structure and implementation.

## 📄 License

This project is proprietary software.  
© 2025 [404NotFoundIndonesia](https://github.com/404NotFoundIndonesia) – All rights reserved.  
Use of this software is governed by the [Laravel Vue Starter Kit Software License Agreement](LICENSE).

