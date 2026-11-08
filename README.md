# 🍽️ Restaurant Supply Chain and Inventory Hub

**CSE470 — Software Engineering | Section 9 | BRAC University**

A web-based restaurant inventory management system built with **Laravel 11**, **Bootstrap 5**, and **SQLite**. This project implements Features F5 and F6 as part of the Restaurant Supply Chain and Inventory Hub SRS.

---

## 📋 Implemented Features

### F5 — Physical Stock Reconciliation
> Staff submit physical stock counts. The system calculates differences from recorded stock, and managers approve or reject the proposed adjustments. Approved adjustments update actual stock.

**How it works:**
1. Inventory staff selects ingredients and enters physically counted quantities
2. The system auto-calculates variance (surplus 🟢 or shortage 🔴) in real-time
3. Manager reviews the reconciliation and either **Approves** (stock updated via DB transaction) or **Rejects** (stock unchanged)
4. Full audit trail with timestamps and manager notes

### F6 — Waste Reporting and Approval
> Staff report damaged or spoiled ingredients against a batch. Approved reports deduct stock and record the reason, quantity, and estimated loss.

**How it works:**
1. Staff selects ingredient, optionally links to a batch, picks a reason (Damaged/Spoiled/Expired/Contaminated/Other)
2. Estimated financial loss is auto-calculated from cost × quantity
3. Manager reviews and either **Approves** (stock deducted via DB transaction) or **Rejects**
4. Full audit trail with financial loss tracking in BDT

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.4, Laravel 11 |
| Frontend | Blade, Bootstrap 5, Font Awesome 6, Vanilla JS |
| Database | SQLite (easy setup) / MySQL (production) |
| Architecture | MVC — Models, Controllers, Service Classes, Blade Views |

---

## 🚀 Setup & Installation

### Prerequisites
- PHP 8.4+ with extensions: `pdo_sqlite`, `mbstring`, `xml`, `dom`
- Composer

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/zhnverse/Restaurant-Supply-Chain-and-Inventory-Hub.git
cd Restaurant-Supply-Chain-and-Inventory-Hub

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Create SQLite database
touch database/database.sqlite

# 6. Run migrations and seed sample data
php artisan migrate --seed

# 7. Start development server
php artisan serve
```

Then visit: **http://localhost:8000**

### Sample Login Credentials
| Role | Email | Password |
|------|-------|----------|
| Manager | manager@restaurant.com | password |
| Staff | staff1@restaurant.com | password |

---

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── StockReconciliationController.php   ← F5
│   └── WasteReportController.php           ← F6
├── Models/
│   ├── Ingredient.php
│   ├── IngredientBatch.php
│   ├── StockReconciliation.php             ← F5
│   ├── StockReconciliationItem.php         ← F5
│   └── WasteReport.php                    ← F6
└── Services/
    ├── StockReconciliationService.php      ← F5 business logic
    └── WasteReportService.php             ← F6 business logic

database/migrations/
├── ..._create_ingredients_table.php
├── ..._create_ingredient_batches_table.php
├── ..._create_stock_reconciliations_table.php
├── ..._create_stock_reconciliation_items_table.php
└── ..._create_waste_reports_table.php

resources/views/
├── layouts/app.blade.php                  ← Modern sidebar layout
├── dashboard.blade.php
├── stock-reconciliations/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php
└── waste-reports/
    ├── index.blade.php
    ├── create.blade.php
    └── show.blade.php
```

---

## 🎨 UI Features
- **Dark sidebar** navigation with live pending-count badges
- **Dashboard** with stat cards, recent activity, low-stock alerts
- **Real-time variance** calculation as you type physical counts
- **Status badges** — color-coded Pending/Approved/Rejected
- **Modal confirmations** for approve/reject with manager notes
- **Activity timeline** on detail pages
- Fully **responsive** (Bootstrap 5)

---

## 👥 Team Members

| Student ID | Name |
|-----------|------|
| 23301563 | Kumar Shagata Biswas |
| 19301025 | Shuddho Sadik |
| 24341207 | Md Zahid Hasan Nerob |

---

## 📝 AI Tool Usage Disclosure
*(As required by Section 9 of the SRS)*

| Feature | AI Tool | How Used | Can Explain? |
|---------|---------|----------|--------------|
| F5, F6 | Antigravity (Google DeepMind) | Generated scaffolding code for models, controllers, service classes, and Blade views | Yes |
