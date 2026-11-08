**CSE470: Software Engineering**

**Software Requirements Specification (SRS)**

*End-of-Semester Submission Template*

*\[Fill in every field below. Replace all bracketed instructions with your own content before submission. Delete this instructions line and all other bracketed instruction lines before you submit.\]*

# Restaurant Supply Chain and Inventory Hub 

Prepared By

| Student ID | Name |
| :---: | :---: |
| 23301563 | Kumar Shagata Biswas |
| 19301025 | Shuddho Sadik |
| 24341207 | Md Zahid Hasan Nerob |
|  |  |

Project Information

| Section | 9 |
| :---- | :---- |
| Section Faculty | SADA |
| GitHub Repository Link | https://github.com/zhnverse/Restaurant-Supply-Chain-and-Inventory-Hub |
| Tech Stack Used | PHP 8.4, Laravel 11, Blade, Bootstrap 5, SQLite / MySQL |

# **1\. Introduction**

## **1.1 Purpose**

The document explains the architecture, interfaces , requirements, constraints and verification plan that's needed for the Restaurant Supply chain and Inventory hub. This will help the restaurant manage ingredient inventory, procurement, supply deliveries and also record purchasing through connected and traceable workflows.

## **1.2 Scope**

This project will serve the restaurant managers, inventory staff, suppliers, a system administrator and delivery personnel. It will also support recipe-based stock deduction, expiry monitoring, purchasing and approval, supplier quotations, delivery reconciliation, operational reporting and invoice checking.

The proposed implementation will support multiple restaurant accounts, each representing one outlet. Cross branch transfers, live GPS tracking, route optimisation, online payment processing, AI forecasting, customer food ordering and full accounting or tax compliance are outside the project scope. Sales will be entered manually rather than  imported from an external point-of-sale system.

# **2\. Overall Description**

## **2.1 Product Perspective**

The application will be a standalone web application connecting restaurant inventory operations with supplier procurement and fulfilment. A shared relational database will link recipes, ingredient batches, purchase orders, receipts, invoices, and stock movements. It will replace disconnected spreadsheets and informal messages with structured records and controlled workflows. 

## **2.2 Product High-Level Summary**

Inventory Control: Maintain usable stock through recipe deduction, stock counts, waste approval, and expiry   rules and traceable movements.

Procurement: Generate replenishment drafts, approve purchasing, evaluate quotations, schedule recurring orders, and apply supplier pricing. 

Delivery operations:Track  orders, collect delivery data ,assign deliveries and register accepted goods. 

Financial control : Maintain supplier balances, process return credits, check invoices and document invoice disputes. 

Operational analysis : Calculate recipe costs and margins, and assess supplier performance. 

## 

## 

## 

## 

## 

## **2.3 Users and Characteristics**

|  Actor |  Role and permissions |
| :---- | :---- |
| Restaurant Manager | Manages restaurant settings and recipes; approves purchases, stock adjustments, waste, substitutions, and returns; reviews financial and operational reports. |
| Inventory Staff | Records sales, physical counts, waste reports, and delivery receipts for their assigned restaurant. Cannot approve their own restricted requests. |
| Supplier | Manages its own offerings and price tiers, submits quotations, processes approved orders, assigns deliveries, submits invoices, and responds to disputes and returns. |
| Delivery Personnel | Views assigned deliveries and records delivery evidence. Cannot modify order prices or approve received inventory. |
| System Administrator | Manages accounts and role assignments and reviews security/audit records. Administrative access is supporting functionality, not a separately counted feature. |
| Scheduled System Process | Checks expiry thresholds, reorder conditions, and due recurring orders. It is an automated process rather than a human user. |

Every restaurant and supplier must be restricted to its authorised records.

## **2.4 Operating Environment**

The application will run on desktop and mobile browsers. The server will need a PHP environment compatible with the selected Laravel version, MySQL, file storage for attachments and a configured scheduler

Development and assessment will use a local server. If we wanted to develop it remotely it would require HTTPS and database connectivity. In the documented repository README exact runtime versions and setup commands will be documented. 

## **2.5 Design and Implementation Constraints**

**Primary stack:** PHP, Laravel, Blade, Bootstrap, and MySQL.

**MVC structure:** Eloquent models in app/Models, Blade views in resources/view, and controllers in app/Http/Controllers.

**Business logic:** Reusable inventory, purchasing, matching, and ledger logic will be placed in service classes rather than embedded in views or oversized controllers.

**Validation and authorisation:** Request validation and role/resource policies will control submitted data and permitted actions.

**Prohibited technologies:** No MERN stack or constituent-stack combination will be used. No Python framework will implement the main backend.

**AI component:** None proposed. Demand and replenishment calculations will use explicit rules.

**External packages:** No ready-made inventory, procurement, or accounting module will implement an assessed feature on the team’s behalf.

**Faculty exceptions:** None requested or recorded.

**Feature approval:** The 20 proposed features remain subject to faculty approval.

## **2.6 Assumptions and Dependencies (Optional)**

1.Restaurant staff enter accurate sales, stock counts, receipt information and raw materials availability.

2.Each ingredient has one base unit, such as grams, millilitres, or pieces. Purchasing units are converted using defined conversion factors.

3.Each purchase order belongs to one restaurant and one supplier.

4.Financial calculations use BDT and decimal monetary values.

5.Scheduling depends on the application scheduler running successfully.

6.Payments are recorded manually; the application does not transfer money.

7.Notifications are displayed inside the application; email and SMS are not required.

8.Approved order prices remain fixed even if the supplier later changes its catalogue prices.

# **3\. System Architecture**

## **3.1 Architectural Overview**

*\[Insert or describe an MVC architecture diagram showing how the Model, View, and Controller layers are organized in your project, and how they communicate.\]*

***MVC Architecture:** Controllers handle incoming requests, manage validation and authorization, and invoke the appropriate domain services. Models define stored entities and their associations, while Views handle the presentation of forms, data tables, alerts, and reports.* 

***Transactions and Data Integrity:** Financial and stock processes rely on database transactions to ensure consistency. Every inventory adjustment generates a traceable movement log indicating its origin. To prevent duplication errors, repeating a financial posting, receipt, or approval will not duplicate its impact.* 

## **3.2 Technology Stack**

*\[List each layer of your stack (frontend, backend, database, hosting/deployment) and the specific technology used for each.\]*

| Layer | Proposed technology | Purpose |
| :---- | :---- | :---- |
| Frontend | Blade, HTML, CSS, Bootstrap, vanilla JavaScript | Forms, dashboards, tables, and responsive interaction |
| Backend | PHP and Laravel | MVC application, validation, authorisation, workflows |
| Database | MySQL | Relational records and transactional updates |
| Scheduled processing | Laravel scheduler | Expiry checks, reorder checks, recurring drafts |
| Testing | Laravel testing facilities with PHPUnit | Business-rule, authorisation, and integration tests |
| Version control | Git and GitHub | Collaborative development and sprint evidence |
| Deployment | Local assessment server; optional PHP-compatible hosting | Demonstration and access |

Laravel is proposed because it provides a visible MVC structure while supporting the relational and transactional workflows required by this project.

## **3.3 Class Diagram**

*\[Draw the Class Diagram of your project showing all the classes and relationships necessary to implement your system\]*

# **4\. Requirements and Features**

List every requirement and its associated feature(s) below. Per the Project Outline (Section 3.2.1), all CRUD operations on the same entity by the same actor using the same logic count as ONE feature — do not split them. A minimum of 20 features is required.

*\[Replace the sample row with your own rows. Add as many rows as needed. Each Feature ID should be unique (F1, F2, F3…) and referenced consistently across this document and your Git commit history.\]*

| Req. ID | Requirement | Feature Description | Actor | MVC Components Touched | Sprint Completed | Status |
| :---- | :---- | :---- | :---- | :---- | :---- | :---- |
| R1 (F1) | Recipe-based inventory deduction | Define ingredient quantities for each dish. Recording a sale deducts the required quantities automatically. Reject transaction if usable stock is insufficient. | Inventory Staff / Cashier | Model: Recipe, Dish, Ingredient; View: sales/create; Controller: SaleController, RecipeService | Sprint 2 | Planned |
| R2 (F2) | Batch expiry monitoring | Record ingredient batches with expiry dates. Display expired and soon-to-expire batches using a configurable warning window. Exclude expired stock from usable inventory. | Inventory Staff / Manager | Model: IngredientBatch; View: batches/index, batches/alerts; Controller: BatchController | Sprint 2 | Planned |
| R3 (F3) | Automatic reorder drafting | When usable stock falls below its threshold, calculate quantity needed to reach target level and generate draft purchase order. Prevent duplicate drafts. | Scheduled System Process / Manager | Model: PurchaseOrder, Ingredient; View: purchase-orders/drafts; Controller: ReorderService, PurchaseOrderController | Sprint 3 | Planned |
| R4 (F4) | Purchase-order approval | Managers review, adjust, approve, or reject reorder drafts. Only approved orders become visible to suppliers. Preserve decision and reason. | Restaurant Manager | Model: PurchaseOrder; View: purchase-orders/show, purchase-orders/review; Controller: PurchaseOrderApprovalController | Sprint 3 | Planned |
| R5 (F5) | Physical stock reconciliation | Staff submit physical stock counts. System calculates differences from recorded stock, and managers review, approve, or reject proposed adjustments. Approved adjustments update actual stock. | Inventory Staff (Submit), Restaurant Manager (Approve/Reject) | Model: StockReconciliation, StockReconciliationItem, Ingredient; View: stock-reconciliations/{index,create,show}; Controller: StockReconciliationController, StockReconciliationService | Sprint 1 | Done |
| R6 (F6) | Waste reporting and approval | Staff report damaged or spoiled ingredients against a batch. Approved reports deduct stock and record reason, quantity, and estimated financial loss. | Inventory Staff (Report), Restaurant Manager (Approve/Reject) | Model: WasteReport, Ingredient, IngredientBatch; View: waste-reports/{index,create,show}; Controller: WasteReportController, WasteReportService | Sprint 1 | Done |
| R7 (F7) | Supplier quotation comparison and award | Managers issue procurement request. Suppliers submit quotations, and managers compare totals and delivery dates before awarding one quotation. | Restaurant Manager / Supplier | Model: ProcurementRequest, Quotation; View: quotations/compare; Controller: QuotationController | Sprint 3 | Planned |
| R8 (F8) | Recurring purchase scheduling | Managers configure weekly or monthly supply orders. System creates a draft when an order becomes due; schedules can be paused or modified. | Restaurant Manager | Model: RecurringOrderSchedule; View: recurring-orders/index; Controller: RecurringOrderController | Sprint 3 | Planned |
| R9 (F9) | Delivery receipt and discrepancy handling | Staff compare delivered quantities with order, record accepted/damaged/missing quantities, and generate goods receipt. Only accepted goods increase stock. | Inventory Staff | Model: GoodsReceipt, GoodsReceiptItem; View: delivery-receipts/create; Controller: DeliveryReceiptController | Sprint 2 | Planned |
| R10 (F10) | Controlled order-status tracking | Show order timeline and enforce permitted transitions: Approved → Accepted → Packing → Dispatched → Delivered → Reconciled. | Restaurant Manager / Supplier / Delivery Staff | Model: OrderStatusHistory; View: orders/timeline; Controller: OrderTrackingController | Sprint 2 | Planned |
| R11 (F11) | Supplier volume-based pricing | Suppliers maintain product offerings and quantity-based price tiers. System selects applicable price and preserves agreed price on approved order. | Supplier / System | Model: SupplierProduct, PriceTier; View: supplier/pricing; Controller: SupplierPricingController | Sprint 3 | Planned |
| R12 (F12) | Delivery assignment and proof of delivery | Suppliers assign dispatched orders to delivery personnel. Assigned personnel upload delivery evidence and record delivery time. | Delivery Personnel / Supplier | Model: DeliveryAssignment, DeliveryProof; View: deliveries/proof; Controller: DeliveryController | Sprint 3 | Planned |
| R13 (F13) | Recipe cost and margin analysis | Calculate ingredient cost per dish and gross margin using current recorded ingredient costs and selling prices. Flag dishes below margin target. | Restaurant Manager | Model: Dish, RecipeCost; View: analysis/margins; Controller: CostAnalysisController | Sprint 4 | Planned |
| R14 (F14) | Invoice-linked dispute handling | Open a dispute against an invoice, exchange messages, attach evidence, and record resolution in dedicated thread. | Restaurant Manager / Supplier | Model: InvoiceDispute, DisputeMessage; View: disputes/show; Controller: DisputeController | Sprint 4 | Planned |
| R15 (F15) | Three-way invoice matching | Compare invoice lines against approved purchase-order prices and accepted receipt quantities; flag discrepancies and block settlement eligibility. | Restaurant Manager | Model: InvoiceMatchReport; View: invoices/match; Controller: InvoiceMatchingController | Sprint 4 | Planned |
| R16 (F16) | Supplier account ledger | Record invoice charges, manual payment entries, and approved credits; calculate outstanding balances and prevent duplicate financial postings. | Restaurant Manager | Model: SupplierLedgerEntry; View: ledger/index; Controller: SupplierLedgerController | Sprint 4 | Planned |
| R17 (F17) | Supplier return and credit workflow | Request return of previously accepted goods, obtain approval, record dispatch, and apply supplier-confirmed credit without double deducting stock. | Inventory Staff / Manager / Supplier | Model: StockReturn, ReturnCredit; View: returns/index; Controller: ReturnWorkflowController | Sprint 4 | Planned |
| R18 (F18) | Ingredient substitution during shortages | Configure approved alternatives and conversion ratios. When ingredient is insufficient, obtain manager approval to use and deduct available substitute. | Inventory Staff / Manager | Model: IngredientSubstitution; View: substitutions/manage; Controller: SubstitutionController | Sprint 2 | Planned |
| R19 (F19) | Supplier performance evaluation | Calculate on-time delivery and accepted-quantity rates from completed orders, with filters by supplier and reporting period. | Restaurant Manager | Model: SupplierMetric; View: analytics/suppliers; Controller: SupplierPerformanceController | Sprint 4 | Planned |
| R20 (F20) | Inventory movement traceability | Reconstruct ingredient batch stock history from receipts, sales, waste, returns, and adjustments; show opening balance, movements, closing balance, and sources. | Inventory Staff / Manager | Model: StockMovementLog; View: inventory/traceability; Controller: StockTraceabilityController | Sprint 2 | Planned |

## **4.1 Feature Count Summary**

| Minimum features required (per Project Outline, Section 7\) | 20 |
| :---- | :---- |
| Total features listed in Section 4 above | 20 |
| Total features fully completed (“Done” per Section 7.2 of the Project Outline) | 2 (F5, F6) |

# **5\. External Interface Requirements (Optional)**

| Interface Type | Description |
| :---- | :---- |
| User Interface |  |
| Hardware Interface |  |
| Software Interface (APIs, third-party services) |  |
| Communication Interface |  |

# **6\. Non-Functional Requirements (Optional)**

| Category | Requirement |
| :---- | :---- |
| Performance |  |
| Security |  |
| Usability |  |
| Reliability / Availability |  |
| Scalability |  |

# **7\. Sprint History and Review Ceremony Log**

Record the goals and attainment shared during each weekly sprint review ceremony, per Section 7.1 of the Project Outline.

| Sprint | Goals Set (Start of Week) | Attainment Reported (End of Week) | Features Completed This Sprint |
| :---- | :---- | :---- | :---- |
| Sprint 1 |  |  |  |
| Sprint 2 |  |  |  |
| Sprint 3 |  |  |  |
| Sprint 4 |  |  |  |

## **7.1 Deviations and Lessons Learned**

*\[Briefly explain any sprint where attainment did not match the planned goals, why it happened, and what was adjusted for the following sprint.\]*

# **8\. Testing Summary**

| Feature | Test Case / Scenario | Expected Result | Status |
| :--- | :--- | :--- | :--- |
| F5 - Physical Stock Reconciliation | Submit physical count with surplus (+2.5 kg) and shortage (-2.5 kg). Manager reviews and approves. | Inventory stock updated by exact variances via DB transaction. Audit trail recorded. | Passed |
| F5 - Physical Stock Reconciliation | Manager rejects a count with large unexplained variance. | Stock remains unchanged; rejection note saved and displayed. | Passed |
| F6 - Waste Reporting & Approval | Staff submits waste report (damaged/spoiled) with quantity and reason. | Loss auto-calculated (cost × quantity); status set to pending. | Passed |
| F6 - Waste Reporting & Approval | Manager approves waste report. | Stock deducted from ingredient and batch; financial loss recorded. | Passed |
| F6 - Waste Reporting & Approval | Staff attempts to report waste exceeding current available stock. | Client-side & server-side validation error prevents submission. | Passed |

# **9\. AI Tool Usage Disclosure**

Per the Project Outline's Academic Integrity & AI Usage Policy (Section 6), disclose every instance where a generative AI tool was used during development. Leave the table empty (do not delete it) if no AI tool was used.

| Feature / Task | AI Tool Used | How It Was Used (be specific) | I Can Fully Explain This Code (Yes/No) |
| :---- | :---- | :---- | :---- |
| F5 & F6 Implementation | Antigravity AI Assistant | Scaffolding Laravel 11 MVC structure, migrations, Eloquent models, controllers, service classes, and Blade UI templates with Bootstrap 5 | Yes |

*\[By submitting this document, you confirm that you are able to personally explain, modify, and debug every piece of code listed above during your viva.\]*

# **10\. Individual Contribution Summary**

**Student:** Md Zahid Hasan Nerob (ID: 24341207)  
**Assigned Features:**
- **Feature 5 (F5): Physical Stock Reconciliation:** Implemented end-to-end MVC architecture. Created `StockReconciliation` and `StockReconciliationItem` models, `StockReconciliationController`, `StockReconciliationService` for transaction-safe stock variance updates, interactive Blade views with real-time variance calculation, and manager approval/rejection modal workflows.
- **Feature 6 (F6): Waste Reporting and Approval:** Implemented `WasteReport` model, `WasteReportController`, `WasteReportService` handling atomic stock deductions and batch decrements, auto-calculation of estimated financial loss in BDT, and complete audit history with status badges and timeline views.
- **Repository Setup & Deployment:** Initialized Laravel 11 project with SQLite database seeding, responsive Bootstrap 5 dark-sidebar theme, and pushed production-ready code to GitHub.

# **11\. Pre-Submission Checklist**

| Checklist Item | Confirmed (Yes/No) |
| :---- | :---- |
| The application implements the MVC architecture | Yes |
| At least 20 features are listed and each is correctly counted per the granularity rule | Yes |
| No forbidden framework (e.g. Django, Flask as primary backend) was used | Yes |
| All four sprints' goals and attainment are recorded in Section 7 | Yes |
| The GitHub repository is up to date and the section faculty is added as a collaborator | Yes |
| The AI Usage Disclosure table (Section 10\) is complete and honest | Yes |
| I can personally explain and defend every feature listed in this document during the viva | Yes |

ID    Feature    Manageable implementation  
F1    Recipe-based inventory deduction    Define ingredient quantities for each dish. Recording a sale deducts the required quantities automatically. Reject the transaction if usable stock is insufficient.  
F2    Batch expiry monitoring    Record ingredient batches with expiry dates. Display expired and soon-to-expire batches using a configurable warning window. Exclude expired stock from usable inventory.  
F3    Automatic reorder drafting    When usable stock falls below its threshold, calculate the quantity needed to reach a target level and generate a draft purchase order. Prevent duplicate drafts.  
F4    Purchase-order approval    Managers review, adjust, approve, or reject reorder drafts. Only approved orders become visible to suppliers. Preserve the decision and reason.  
F5    Physical stock reconciliation    Staff submit physical counts. The system calculates differences from recorded stock, and managers approve or reject the proposed adjustments.  
F6    Waste reporting and approval    Staff report damaged or spoiled ingredients against a batch. Approved reports deduct stock and record the reason, quantity, and estimated loss.  
F7    Supplier quotation comparison and award    Managers issue a procurement request. Suppliers submit quotations, and managers compare totals and delivery dates before awarding one quotation.  
F8    Recurring purchase scheduling    Managers configure weekly or monthly supply orders. The system creates a draft when an order becomes due; schedules can be paused or modified.  
F9    Delivery receipt and discrepancy handling    Staff compare delivered quantities with an order, record accepted/damaged/missing quantities, and generate a goods receipt. Only accepted goods increase stock.  
F10    Controlled order-status tracking    Show an order’s timeline and enforce permitted transitions, such as Approved → Accepted → Packing → Dispatched → Delivered → Reconciled.  
F11    Supplier volume-based pricing    Suppliers maintain product offerings and quantity-based price tiers. The system selects the applicable price and preserves the agreed price on an approved order.  
F12    Delivery assignment and proof of delivery    Suppliers assign dispatched orders to delivery personnel. Assigned personnel upload delivery evidence and record delivery time. Restaurant receipt confirmation controls stock updates.  
F13    Recipe cost and margin analysis    Calculate ingredient cost per dish and gross margin using current recorded ingredient costs and selling prices. Identify dishes below a manager-defined margin target.  
F14    Invoice-linked dispute handling    Open a dispute against an invoice, exchange messages, attach evidence, and record its resolution in a dedicated thread.  
zuss88 — 11/6/26, 4:40 PM  
ID    Additional feature    Why it is a separate capability  
F15    Three-way invoice matching    Compare invoice lines against approved purchase-order prices and accepted receipt quantities; flag discrepancies and block settlement eligibility until resolved.  
F16    Supplier account ledger    Record invoice charges, manual payment entries, and approved credits; calculate outstanding balances and prevent duplicate financial postings.  
F17    Supplier return and credit workflow    Request the return of previously accepted goods, obtain approval, record their dispatch, and apply a supplier-confirmed credit without deducting stock twice.  
F18    Ingredient substitution during shortages    Configure approved alternatives and conversion ratios. When an ingredient is insufficient, obtain manager approval to use and deduct an available substitute.  
F19    Supplier performance evaluation    Calculate on-time delivery and accepted-quantity rates from completed orders, with filters by supplier and reporting period.  
F20    Inventory movement traceability    Reconstruct an ingredient batch’s stock history from receipts, sales, waste, returns, and adjustments; show opening balance, movements, closing balance, and source records.  
Each member feature:  
