<div align="center">
    <a href="https://404notfound.fun" target="_blank">
        <img src="./public/404NFID.png" 
            width="100" 
            alt="404NFID Logo">
    </a>
</div>

# Sistem Manajemen Asesmen K3TAB

> Platform digital untuk pengelolaan asesmen kompetensi dengan fitur lengkap untuk dosen (asesor), mahasiswa (asesi), dan administrator.

## 📋 Ringkasan Aplikasi

**Sistem Manajemen Asesmen K3TAB** adalah platform komprehensif yang dirancang untuk mendigitalkan proses asesmen kompetensi di lingkungan akademik. Sistem ini memfasilitasi interaksi antara tiga peran utama:

- **👨‍🏫 Dosen (Asesor)**: Mengelola jadwal asesmen, mengevaluasi hasil kerja mahasiswa, dan memberikan penilaian berdasarkan performance guide
- **👨‍🎓 Mahasiswa (Asesi)**: Mengakses modul pembelajaran, mengerjakan post-test, dan mensubmit tugas asesmen
- **🔧 Administrator**: Mengelola data pengguna, modul pembelajaran, performance guide, dan konfigurasi sistem

### ✨ Fitur Utama

- **📚 Manajemen Modul**: Upload dan kelola materi pembelajaran dengan berbagai format file (PDF, DOCX, gambar)
- **📝 Sistem Post-Test**: Kuis otomatis dengan berbagai tipe soal untuk evaluasi pemahaman
- **📋 Performance Guide**: Panduan penilaian terstruktur yang dihasilkan dengan bantuan AI (Gemini API)
- **📅 Jadwal Asesmen**: Sistem penjadwalan yang fleksibel dengan notifikasi real-time
- **📊 Dashboard Interaktif**: Visualisasi data dengan kalender dan statistik komprehensif
- **💬 Notifikasi WhatsApp**: Integrasi WhatsApp untuk komunikasi dan reminder otomatis
- **🌐 Multi-bahasa**: Dukungan 6 bahasa (Indonesia, English, Arabic, Japanese, Korean, Chinese)
- **📱 Responsive Design**: Antarmuka yang optimal untuk desktop dan mobile
- **🔐 Sistem Permission**: Kontrol akses berbasis peran yang granular
- **📈 Activity Logging**: Pencatatan seluruh aktivitas untuk audit trail

## 🛠️ Tech Stack

- **Laravel 12** – Backend framework dengan PHP 8.2+
- **Vue 3 + TypeScript** – Frontend framework dengan type safety
- **Inertia.js** – Full-stack framework tanpa API
- **Vite** – Build tool modern untuk development yang cepat
- **Tailwind CSS 4.x** – Utility-first CSS framework
- **PostgreSQL 16** – Database dengan Eloquent ORM
- **Pusher/Reverb** – Real-time communication
- **Gemini AI** – AI-powered content generation
- **WhatsApp API** – Notification system
- **Midtrans/Xendit** – Payment gateway integration

## 📦 Installation

### 🐳 Docker (Recommended)

```bash
# Dev (with hot reload)
docker compose -f docker-compose.yaml up -d

# Production
docker compose -f docker-compose.yml up -d
```

Database runs on PostgreSQL 16 at `localhost:5432` (`k3tab2025` / `root` / `root`).

### 💻 Local

```bash
# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate
```

### 🔐 Environment Configuration

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=k3tab2025
DB_USERNAME=root
DB_PASSWORD=root
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

## 🔄 Database Migration Notes

### Question Table — Essay & Combined Media Update

Migration `2026_04_25_000001_update_questions_table_add_essay_and_media.php` adds:
- `type` column (default `multiple_choice`) — safe, all existing rows auto-filled
- `question_image` column (nullable) — safe, existing rows get `null`
- `choices_images` column (nullable) — safe, existing rows get `null`
- `choices` and `correct_answer_index` made nullable — safe, existing values untouched

Run the migration normally:

```bash
php artisan migrate
```

**⚠️ Image-only questions (existing data fix)**

Old image-only questions stored the image URL directly in the `question` text column. After this migration, the frontend no longer detects URLs in that column — it renders `question` as plain text and `question_image` as the image.

If you have existing image-only questions, run this one-time fix after migrating:

```bash
php artisan tinker --execute="
DB::table('questions')
    ->whereRaw(\"question LIKE 'http%'\")
    ->update(['question_image' => DB::raw('question'), 'question' => null]);
echo 'Done: ' . DB::table('questions')->whereNotNull('question_image')->count() . ' records fixed.';
"
```

Or via raw SQL:

```sql
UPDATE questions
SET question_image = question, question = NULL
WHERE question LIKE 'http%';
```

## 🧪 Development

```bash
composer run dev
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

---

# 📄 Product Requirements Document (PRD)

## 1. Visi Produk

Menciptakan platform asesmen digital yang komprehensif dan user-friendly untuk meningkatkan efisiensi dan akurasi proses evaluasi kompetensi di lingkungan pendidikan tinggi.

## 2. Tujuan Bisnis

### 2.1 Objektif Utama
- **Digitalisasi Proses**: Mengotomatisasi proses asesmen yang sebelumnya manual
- **Peningkatan Efisiensi**: Mengurangi waktu administrasi hingga 70%
- **Standardisasi Penilaian**: Memastikan konsistensi evaluasi melalui performance guide
- **Aksesibilitas**: Menyediakan akses 24/7 dari berbagai perangkat
- **Transparansi**: Memberikan visibility penuh atas proses asesmen

### 2.2 Key Performance Indicators (KPIs)
- User adoption rate: >90% dalam 3 bulan
- Time to complete assessment: <50% dari proses manual
- User satisfaction score: >4.5/5.0
- System uptime: >99.5%
- Response time: <2 detik untuk operasi standar

## 3. Target Pengguna

### 3.1 Primary Users
- **👨‍🏫 Dosen (Asesor)**: 50-200 pengguna aktif
  - Kebutuhan: Manajemen jadwal, evaluasi efisien, reporting
  - Pain points: Proses manual, tracking progress, komunikasi dengan mahasiswa
  
- **👨‍🎓 Mahasiswa (Asesi)**: 500-2000 pengguna aktif
  - Kebutuhan: Akses materi, submission tugas, tracking progress
  - Pain points: Keterbatasan waktu, akses informasi, feedback delayed

### 3.2 Secondary Users  
- **🔧 Administrator**: 2-5 pengguna
  - Kebutuhan: User management, system configuration, reporting

## 4. Functional Requirements

### 4.1 Core Features

#### A. User Management System
- **Registration & Authentication**
  - Multi-role registration (Admin, Assessor, Assessee)
  - Email verification dengan resend capability
  - Password reset via email
  - Session management dengan auto-logout

- **Profile Management**
  - Multi-language preferences (6 bahasa)
  - Phone number dengan WhatsApp integration
  - Avatar generation otomatis
  - Activity history tracking

#### B. Module Management
- **Content Management**
  - Upload multiple file formats (PDF, DOCX, images)
  - Drag & drop file upload
  - File preview dan download
  - Version control untuk updates

- **Module Structure**
  - Hierarchical organization
  - Module code generation
  - Description dengan rich text editor
  - Publication status control

#### C. Assessment System
- **Post-Test Engine**
  - Multiple question types (pilihan ganda, essay, praktik)
  - Randomized question order
  - Time limits dengan auto-submit
  - Instant scoring untuk objective questions
  - Retry mechanism dengan attempt limits

- **Performance Guide Integration**
  - AI-generated assessment criteria via Gemini API
  - Customizable rubrics
  - Scoring templates
  - Performance indicators mapping

#### D. Scheduling System
- **Calendar Management**
  - Interactive calendar view (FullCalendar.js)
  - Drag & drop scheduling
  - Conflict detection
  - Recurring schedule support

- **Assessment Workflow**
  - Multi-stage assessment process
  - Status tracking (Draft → Scheduled → In Progress → Submitted → Evaluated)
  - Automatic transitions dengan business rules
  - Notification triggers

#### E. Communication System
- **WhatsApp Integration**
  - Automated notifications untuk milestones
  - Custom message templates
  - Delivery status tracking
  - Bulk messaging capability

- **In-app Notifications**
  - Real-time notifications via Pusher
  - Notification center dengan read/unread status
  - Email fallback untuk critical notifications
  - Notification preferences per user

### 4.2 Advanced Features

#### A. Reporting & Analytics
- **Dashboard Widgets**
  - Assessment completion rates
  - Performance trends
  - User activity metrics
  - System usage statistics

- **Export Capabilities**
  - PDF report generation dengan custom templates
  - Excel export untuk data analysis
  - Printable assessment forms
  - Performance certificates

#### B. AI Integration
- **Gemini AI Features**
  - Performance guide generation
  - Content summarization
  - Question suggestion
  - Personalized feedback

#### C. Internationalization
- **Multi-language Support**
  - 6 bahasa: ID, EN, AR, JA, KO, ZH-CN
  - RTL support untuk Arabic
  - Locale-specific formatting (dates, numbers)
  - Cultural adaptation untuk UI elements

## 5. Non-Functional Requirements

### 5.1 Performance
- **Response Time**: <2 detik untuk 95% requests
- **Throughput**: Support 1000+ concurrent users
- **File Upload**: Support files sampai 50MB
- **Database**: Optimized queries dengan indexing

### 5.2 Security
- **Authentication**: Multi-factor authentication (optional)
- **Authorization**: Role-based access control (RBAC)
- **Data Protection**: Encryption at rest dan in transit
- **Audit Trail**: Comprehensive logging untuk compliance

### 5.3 Scalability
- **Horizontal Scaling**: Support untuk load balancing
- **Database Optimization**: Query optimization dan caching
- **File Storage**: Cloud storage integration
- **CDN Support**: Asset delivery optimization

### 5.4 Reliability
- **Uptime**: 99.5% availability target
- **Backup**: Daily automated backups dengan point-in-time recovery
- **Error Handling**: Graceful degradation dengan user-friendly messages
- **Monitoring**: Application performance monitoring (APM)

## 6. Technical Architecture

### 6.1 System Architecture
- **Frontend**: Vue.js 3 + TypeScript + Inertia.js
- **Backend**: Laravel 12 + PHP 8.2+
- **Database**: PostgreSQL 16 dengan Eloquent ORM
- **Caching**: Redis untuk session dan application cache
- **Queue**: Redis-based job processing
- **Storage**: Local storage dengan S3 backup option

### 6.2 Integration Requirements
- **WhatsApp Business API**: Untuk messaging
- **Gemini AI API**: Untuk content generation
- **Payment Gateway**: Midtrans/Xendit integration
- **Email Service**: SMTP dengan queue processing
- **Push Notifications**: Laravel Reverb dengan WebSocket

## 7. Implementation Phases

### Phase 1: Core System (MVP) - 8 weeks
- User management dan authentication
- Basic module management
- Simple assessment workflow
- Basic reporting

### Phase 2: Enhanced Features - 6 weeks  
- WhatsApp integration
- Advanced scheduling
- Performance guide system
- Detailed analytics

### Phase 3: AI & Advanced Features - 4 weeks
- Gemini AI integration
- Advanced reporting
- Mobile optimization
- Performance tuning

### Phase 4: Production & Scale - 2 weeks
- Production deployment
- Load testing
- Security audit
- User training

## 8. Success Metrics

### 8.1 User Metrics
- Monthly Active Users (MAU)
- User retention rate
- Feature adoption rate
- User satisfaction (NPS score)

### 8.2 Technical Metrics
- System uptime percentage
- Average response time
- Error rate (<1%)
- Security incidents (target: 0)

### 8.3 Business Metrics
- Reduction in manual processing time
- Increase in assessment completion rate
- Cost savings from automation
- ROI measurement

---

## 📄 License

This project is proprietary software.  
© 2025 [404NotFoundIndonesia](https://github.com/404NotFoundIndonesia) – All rights reserved.  
Use of this software is governed by the [Laravel Vue Starter Kit Software License Agreement](LICENSE).

