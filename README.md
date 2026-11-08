# 🍽️ Restaurant Supply Chain and Inventory Hub

**CSE470 — Software Engineering | Section 9 | BRAC University**

A comprehensive restaurant inventory management & procurement system built with **Laravel 11**, **Bootstrap 5**, and **SQLite**.

👉 **Live GitHub Repository:** [https://github.com/zhnverse/Restaurant-Supply-Chain-and-Inventory-Hub](https://github.com/zhnverse/Restaurant-Supply-Chain-and-Inventory-Hub)

---

## 📋 Implemented Features (6 Features + Auth System)

### 🔐 Authentication System (Login & Logout)
- Full authentication with email & password, session regeneration, and remember-me.
- Role-based views & permissions (**Manager** vs **Staff**).
- One-click quick login buttons for instant role testing during demonstrations.

### F1 — Recipe-Based Inventory Deduction
- Define dish recipes specifying exact ingredient quantities required per serving.
- Kitchen POS sale recording verifies usable stock in real-time.
- Automatically deducts ingredient stock atomically across batches (FIFO).
- **Strict Stock Guard:** Immediately rejects the sale if usable inventory is insufficient.

### F2 — Batch Expiry Monitoring
- Track individual ingredient batches with expiry dates, quantities, and cost.
- Configurable warning window filter (3, 7, 14, 30 days) with critical countdown alerts.
- **SRS Food Safety Rule:** Expired stock is automatically quarantined and excluded from usable inventory.

### F3 — Automatic Reorder Drafting
- Automatically evaluates usable stock against minimum thresholds.
- Computes replenishment quantity needed to reach configured `target_stock`.
- Generates draft purchase order with line-item cost estimations.
- **Duplicate Prevention:** Prevents creating duplicate drafts for ingredients already in a pending order.

### F4 — Purchase-Order Approval
- Managers review reorder drafts with complete stock context (current stock, threshold, target).
- Managers can adjust order quantities before authorizing.
- Modal approval and rejection workflows with mandatory manager decision notes.
- Only approved orders are authorized for supplier dispatch.

### F5 — Physical Stock Reconciliation
- Staff submit physical stock count audits with selective ingredient row toggling.
- Real-time client-side variance calculation: surplus (🟢 `+X.XX`), shortage (🔴 `-X.XX`), or balanced (⚪ `0.00`).
- Manager approval posts atomic stock adjustments; rejection preserves inventory intact.
- Complete decision trail with timestamps and reviewer notes.

### F6 — Waste Reporting and Approval
- Staff report damaged, spoiled, or expired ingredients linked to specific batches.
- Auto-calculates estimated financial loss in BDT (`quantity × unit_cost`).
- Manager reviews and approves, deducting stock atomically from inventory and batch records.

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.4, Laravel 11 (MVC Architecture) |
| **Frontend** | Blade Templates, Bootstrap 5, Font Awesome 6, Vanilla JS |
| **Database** | SQLite (development/demonstration) & MySQL compatible |
| **Business Logic** | Dedicated Domain Service Classes (`SaleService`, `ReorderService`, `PurchaseOrderService`, `StockReconciliationService`, `WasteReportService`) |

---

## 🚀 Setup & Installation

```bash
# 1. Clone repository
git clone https://github.com/zhnverse/Restaurant-Supply-Chain-and-Inventory-Hub.git
cd Restaurant-Supply-Chain-and-Inventory-Hub

# 2. Install dependencies
composer install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Migrate database & seed complete sample data
touch database/database.sqlite
php artisan migrate --seed

# 5. Start development server
php artisan serve
```

Visit: **http://localhost:8000**

### 🔑 Demo Credentials
| Role | Email | Password |
|---|---|---|
| **Manager** | `manager@restaurant.com` | `password` |
| **Staff** | `staff1@restaurant.com` | `password` |

*(Or use the 1-click Quick Login buttons on the `/login` screen)*

---

## 👥 Prepared By
- **Md Zahid Hasan Nerob** (ID: 24341207)
- **Kumar Shagata Biswas** (ID: 23301563)
- **Shuddho Sadik** (ID: 19301025)
