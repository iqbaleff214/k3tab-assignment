# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Backend (Laravel)
- `composer run dev` - Start development server with hot reloading (runs Laravel serve, Pail logs, and Vite dev in parallel)
- `php artisan serve` - Start Laravel development server
- `php artisan migrate --seed` - Run database migrations with seeders
- `php artisan pail --timeout=0` - Monitor application logs
- `php artisan test` - Run PHP tests using Pest
- `composer run test` - Run tests with config clearing
- `php artisan key:generate` - Generate application key
- `php artisan config:clear` - Clear configuration cache

### Frontend (Vue.js/Vite)
- `npm run dev` - Start Vite development server with hot reloading
- `npm run build` - Build production assets
- `npm run build:ssr` - Build with server-side rendering
- `npm run lint` - Run ESLint with auto-fix
- `npm run format` - Format code with Prettier
- `npm run format:check` - Check code formatting

### SSR Development
- `composer run dev:ssr` - Start development with server-side rendering

## Architecture Overview

### Technology Stack
- **Backend**: Laravel 12 with PHP 8.2+
- **Frontend**: Vue 3 + TypeScript + Inertia.js + Vite
- **Styling**: Tailwind CSS 4.x
- **UI Components**: Reka UI, PrimeVue, custom shadcn/ui-style components
- **Database**: MySQL/SQLite with Eloquent ORM
- **Authentication**: Laravel Sanctum with role-based permissions
- **Real-time**: Laravel Reverb with Pusher
- **AI Integration**: Gemini API for content generation
- **External Integrations**: WhatsApp API, Midtrans, Xendit payments

### Application Structure

#### User Types & Role System
- **Admin**: Full system access, manages all modules and users
- **Assessor**: Evaluates assessments and manages schedules  
- **Assessee**: Takes modules and submits assessments
- Role-based permissions managed via `app/Enum/Permission.php` with Spatie Laravel Permission

#### Core Modules

**Assessment System**:
- Modules with media files and questions (`app/Models/Module.php`)
- Post-tests and performance evaluations
- Assessment scheduling and submission workflow
- PDF generation for assessment reports

**User Management**:
- Multi-language support (6 languages: en, id, ar, ja, ko, zh-CN)  
- WhatsApp integration for notifications
- Activity logging with Spatie ActivityLog
- Phone number formatting with international support

**Communication**:
- WhatsApp integration via custom binary (`app/Binary/whatsapp/`)
- Real-time notifications via Laravel Reverb
- Email notifications for key events

### Key Patterns

#### Models
All models extend base functionality with traits:
- `Filterable`: Query filtering capabilities
- `Sortable`: Column sorting functionality  
- `Paginate`: Custom pagination rendering
- Activity logging for audit trails

#### Controllers
Follow resource controller pattern with:
- Form request validation (`app/Http/Requests/`)
- Permission middleware via enum system
- Consistent error handling and success messaging
- Mass operations support (mass delete, etc.)

#### Frontend Architecture
- **Inertia.js**: Seamless Laravel-Vue integration
- **Component Structure**: Reusable UI components in `resources/js/components/ui/`
- **Layouts**: Modular layouts for different sections (auth, app, configuration)
- **Type Safety**: Full TypeScript integration with defined interfaces
- **State Management**: Pinia for complex state
- **Internationalization**: Vue i18n with JSON locale files

#### Routing Structure
- `/a/` prefix for admin-only routes
- Middleware-based access control with user type restrictions
- RESTful resource routes with permission-gated actions
- Separate route files by functionality (auth.php, settings.php, etc.)

### File Upload System
- Media management via `app/Models/Media.php`
- File storage in `storage/app/public/modules/`
- Support for documents (PDF, DOCX), images (PNG, WEBP)

### Testing
- **Framework**: Pest PHP for backend testing
- **Coverage**: Feature and unit tests in `tests/` directory
- **CI Integration**: Database tests use SQLite in-memory

### Development Workflow
1. Use `composer run dev` for full-stack development
2. Follow existing patterns when adding new modules
3. Always implement proper permission checks
4. Add multilingual support for user-facing text
5. Include activity logging for important model changes
6. Run tests before committing changes

### Performance Considerations
- Asset optimization via Vite with code splitting
- Database indexing on frequently queried columns
- Lazy loading for Vue components and routes
- Efficient pagination with custom `Paginate` trait