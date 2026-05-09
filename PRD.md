# Product Requirements Document
## Competency Assessment Management System (CAMS)

**Version:** 1.0  
**Date:** 2026-05-10  
**Status:** Active Development

---

## 1. Overview

CAMS is a web-based platform for managing competency-based training and assessment. It supports a structured flow: assessees study training modules, take post-tests, then undergo formal skill assessments conducted by assessors — culminating in a competency verdict and a signed PDF report.

### 1.1 Problem Statement

Training organizations need a unified system to:
- Deliver structured learning materials to trainees
- Evaluate knowledge retention through automated and manually-graded tests
- Conduct formal competency assessments with proper documentation and signatures
- Track and report competency outcomes across assessees and modules

### 1.2 Goals

- Reduce manual paperwork in competency assessments
- Provide assessors a clear workflow from scheduling to evaluation
- Give assessees self-service access to modules, test results, and assessment status
- Give admins full visibility and control over all platform activity

---

## 2. User Roles

| Role | Description | Access Prefix |
|------|-------------|---------------|
| **Admin** | Full platform control. Manages modules, questions, users, assessors, and all records. | `/a/` |
| **Assessor** | Evaluates assessees. Schedules assessments, grades essay answers, submits competency verdicts. | `/r/` |
| **Assessee** | Trainee. Accesses modules, takes post-tests, proposes and views assessment results. | `/e/` |

---

## 3. Core Features

### 3.1 Authentication & Account Management

**All users:**
- Email/password login (supports email or phone number as identifier)
- Password reset via email link
- Email verification
- Remember-me session persistence
- Multi-session management (view and revoke active sessions)
- Profile update (name, email, phone, locale preference)
- Password change
- Account deletion

**Account fields:** name, email, phone, NIM (ID number), locale (6 languages), user type.

---

### 3.2 Module Management (Admin)

Modules are the primary learning units. Each module contains study materials and a question bank.

**Module fields:**
| Field | Description |
|-------|-------------|
| `title` | Module name |
| `code` | Short identifier code |
| `slug` | URL-safe identifier (auto-generated from title) |
| `description` | Brief summary |
| `body` | Full learning content (rich text) |
| `duration_estimation` | Estimated study time |
| `minimum_score` | Passing threshold (0–100) |
| `questions_count` | Number of questions to present per post-test attempt |
| `equipment_required` | Tools/equipment needed for practical assessment |
| `procedure` | Step-by-step procedure |
| `reference` | Source materials / references |
| `performance` | Performance criteria |

**Admin actions:** create, edit, delete (single and mass delete), view.

**Media attachments:** Admins upload files (PDF, DOCX, PNG, WEBP) to a module. Assessees can download them after accessing the module.

---

### 3.3 Question Bank (Admin)

Each module has a pool of questions used to generate post-tests. The post-test draws a configurable number of questions (`questions_count`) from the pool, shuffled each attempt.

#### Question Types

**Multiple Choice**
- Admin defines 1–N choice options (text + optional image per choice)
- Admin marks one correct answer
- Auto-graded at submission

**Essay**
- Assessee writes a free-text answer
- Manually graded by assessor or admin (marked correct/incorrect per answer)
- Score calculated only after all essays are graded

**Fill in the Blank**
- Assessee types a free-text answer
- Admin defines accepted answer variants (e.g., `0.3mm`, `0,3mm`, `0.3 mm`)
- Auto-graded at submission: answer is trimmed, lowercased, and compared against all variants
- No partial credit; any matching variant = correct

**Question fields:** title (optional), type, question text (optional), question image (optional), choices, choices images (MC only), correct answer index (MC only).

---

### 3.4 Post-Test System

Assessees take post-tests from the module detail page.

#### Assessee Flow

1. Open module → read materials, download files
2. Start post-test (locks file downloads during test)
3. Answer all questions in a randomized, shuffled order
4. Submit answers → test finishes

#### Scoring Logic

| Scenario | Score | `is_passed` | `is_graded` |
|----------|-------|-------------|-------------|
| MC/fill-in-the-blank only | Calculated immediately: `(correct / total) × 100` | Set immediately | `true` |
| Contains any essay | `0` (pending) | `null` (pending) | `false` |

When a test contains essays, score and pass/fail remain pending until an assessor or admin grades all essay answers.

#### Grading Essays (Assessor / Admin)

- View ungraded post-tests via Post-test list (filtered by `is_graded = false`)
- Open grade modal: each essay question shows the question text and assessee's answer
- Mark each essay answer as correct or incorrect
- On submit: recalculate score across all answers (MC + essay), set `is_passed`, update assessee's module score

#### Result Display (Assessee)

- Each answer shows a badge: **Correct** (green), **Incorrect** (red), or **Pending Grading** (yellow)
- Latest score shows numeric score or "Pending Grading" text if ungraded
- Full answer history available for previous attempts

#### Post-test Records (Admin)

- List all post-test submissions with filters: module, pass/fail, date range
- Delete post-test records
- Grading status column: **Graded** / **Pending Grading**

---

### 3.5 Performance Guide (Admin)

A Performance Guide is a skill-based task checklist linked to a module. It defines the formal assessment criteria used during practical skill evaluation.

**Fields:** skill number (unique identifier), title, performance task description, tasks (structured checklist of observable behaviors).

Each module has one performance guide. The skill number is the primary reference used in assessments and PDF reports.

---

### 3.6 Assessment Workflow

The formal assessment is a multi-step process between assessee and assessor, ending with a signed PDF record.

#### Step 1 — Assessee Proposes Assessment

- Assessee selects: skill number (performance guide), assessor, ID number, school/organization
- Proposes 1–N available date/time slots
- System creates assessment with status `pending` and sends email notification to assessor

**Constraint:** Assessee can only delete a pending assessment (before assessor responds).

#### Step 2 — Assessor Responds

- Assessor reviews proposed dates
- Accepts one date (status → `scheduled`) or rejects all (status → `cancelled`)
- If cancelled, assessor provides feedback explaining the reason
- When scheduled: the selected task list from the performance guide is copied into the assessment record

#### Step 3 — Assessor Conducts and Submits Evaluation

- Assessor records start time, marks each task as `completed`, `not_completed`, or `not_available`
- Records verdict: **Competent** or **Not Competent**
- Adds comments and feedback
- Records signatures: assessor, assessee, supervisor, data recorder (name + signature + date for each)
- Submits → status → `completed`
- System sends email notification to assessee with result

#### Assessment Statuses

| Status | Meaning |
|--------|---------|
| `pending` | Submitted by assessee, awaiting assessor response |
| `scheduled` | Assessor accepted a date |
| `cancelled` | Assessor rejected all proposed dates |
| `completed` | Evaluation submitted |

#### Assessment Results

| Result | Meaning |
|--------|---------|
| `competent` | Assessee passed the skill assessment |
| `not_competent` | Assessee did not pass |

---

### 3.7 PDF Reports

Both assessors and assessees can generate and download PDF reports of a completed assessment.

**Report contents:** skill number, assessee info (name, ID, school), assessor info, task checklist with statuses, verdict, comments, feedback, all four signature blocks (with dates).

---

### 3.8 User Management

#### Assessee Management (Admin + Assessor)
- Create, edit, delete (single and mass) assessee accounts
- Fields: name, email, phone, NIM, WhatsApp flag, locale

#### Assessor Management (Admin only)
- Create, edit, delete (single and mass) assessor accounts
- Same fields as assessee

#### Role & Permission System
- Built on Spatie Laravel Permission
- Role-based permission gates control access to specific admin actions
- Permissions enumerated in `app/Enum/Permission.php`

---

### 3.9 Dashboard (Admin)

Summary statistics and charts visible to admins.

**Metrics:**
- Total users (split: assessors / assessees)
- Total modules
- Total assessments (split: pending / in progress / completed)
- Total post-tests taken

**Charts:**
- Assessment status distribution (pie/bar)
- Monthly assessment trend (last 6 months)
- User registration trend (last 30 days)
- Top performance guides by usage (top 5)

**Tables:**
- Recent assessments (last 5): assessee, assessor, skill number, status, date
- Module completion rates (top 5): enrolled count, completions, % rate

---

### 3.10 Notifications

| Event | Channel | Recipient |
|-------|---------|-----------|
| Assessee proposes assessment | Email | Assessor |
| Assessor responds (scheduled/cancelled) | Email | Assessee |
| Assessment result submitted | Email | Assessee |

In-app real-time notifications delivered via Laravel Reverb (WebSocket).

---

### 3.11 Activity Log

All significant model changes are logged (using Spatie ActivityLog):
- Created / updated / deleted events for Users
- Logged fields: name, email, phone (only changed fields)
- Admin can view activity log list

---

### 3.12 Internationalization

UI is fully translated in 6 languages, selectable per user account:

| Code | Language |
|------|----------|
| `en` | English |
| `id` | Indonesian (Bahasa Indonesia) |

---

## 4. Data Model Summary

```
User
  ├── type: admin | assessor | assessee
  ├── ModuleAssessee (pivot: assessee ↔ module)
  │     ├── score, is_doing_test, downloaded_files, read_at
  │     └── status: read | doing_test | not_competent | competent
  ├── PostTest (answers, score, is_passed, is_graded)
  └── Assessment (as assessee or assessor)

Module
  ├── Media (uploaded files)
  ├── Question (multiple_choice | essay | fill_in_the_blank)
  ├── ModuleAssessee (enrolled assessees)
  ├── PostTest (submissions)
  └── PerformanceGuide (1:1)

Assessment
  ├── AssessmentSchedule (proposed dates, accepted/rejected)
  ├── assessor (User)
  ├── assessee (User)
  └── guide (PerformanceGuide)
```

---

## 5. Technical Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue 3, TypeScript, Inertia.js, Vite |
| Styling | Tailwind CSS 4.x |
| UI Components | Reka UI, PrimeVue |
| Database | PostgreSQL (production), SQLite (testing) |
| Auth | Laravel Sanctum + Spatie Permission |
| Real-time | Laravel Reverb (WebSocket) |
| PDF | DomPDF (barryvdh/laravel-dompdf) |
| Queue | Laravel Queue |
| Logging | Spatie ActivityLog |

---

## 6. Non-Functional Requirements

| Requirement | Detail |
|-------------|--------|
| Multi-language | 6 languages, per-user locale setting |
| Audit trail | Activity log for all critical model changes |
| Access control | Middleware-enforced role-type restrictions per route prefix |
| File security | Module files only accessible after assessee reads the module; blocked during active post-test |
| PDF generation | Server-side, downloadable |
| Pagination | All list pages paginated with configurable page size |
| Sorting & filtering | All list pages support column sorts and filter combinations |

---

## 7. Out of Scope

The following are explicitly excluded from this PRD:

- Gemini AI integration
- WhatsApp messaging integration
- Payment gateway (Midtrans, Xendit, or any billing/subscription feature)
