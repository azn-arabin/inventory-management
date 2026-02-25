# Inventory Management System with Accounting

A complete Laravel-based inventory management system featuring **double-entry bookkeeping**, comprehensive financial reports, automated journal entries for every transaction, and a full **payment collection** workflow.

## 🎯 Project Overview

This system implements a professional-grade inventory management solution with proper accounting principles:

- **Product Management**: Track products with purchase/sell prices and stock levels
- **Sales Recording**: Record sales with automatic stock adjustment and partial/full payments
- **Payment Collection**: Collect due payments from customers with proper journal entries
- **Double-Entry Accounting**: Every transaction creates proper debit/credit journal entries
- **Gross Revenue Method**: Sales Revenue is always recorded at GROSS amount; discounts are tracked as a separate contra-revenue entry
- **Financial Reports**: Date-wise filtering for sales, expenses, and profit analysis
- **Chart of Accounts**: Standard accounting structure (Assets, Liabilities, Equity, Revenue, Expenses)
- **Automated VAT Calculation**: 5% VAT with automatic journal entries

## 📊 Key Features

### 1. **Complete Accounting System**

- Implements proper double-entry bookkeeping principles
- **On Product Creation** — 2 journal entries:
  - Inventory (Debit) — inventory value added
  - Owner's Equity (Credit) — capital invested
- **On Sale** — up to 7 journal entries:
  - Cash (Debit) — paid amount
  - Accounts Receivable (Debit) — due amount
  - Sales Revenue (Credit) — **gross subtotal** (before discount)
  - Discount Given (Debit) — contra-revenue
  - VAT Payable (Credit) — 5% on after-discount amount
  - Cost of Goods Sold (Debit)
  - Inventory (Credit)
- **On Payment Collection** — 2 journal entries:
  - Cash (Debit) — amount received
  - Accounts Receivable (Credit) — balance cleared

### 2. **Financial Reporting**

- **Financial Report**: Net sales, total expenses (COGS), gross profit, profit margin with **date filtering on journal entries**
- **Journal Entries**: Complete audit trail of all transactions (purchases, sales, payments)
- **Chart of Accounts**: Full account hierarchy with running balances
- **Sales Report**: Detailed sales analysis with payment status tracking
- **Inventory Report**: Stock levels, values, potential revenue, and profitability analysis

### 3. **Inventory Management**

- Real-time stock tracking
- Low stock alerts (< 10 units)
- Product profitability analysis
- Opening stock vs current stock management

### 4. **Payment Collection**

- Partial and full payment support
- Multiple payment methods: Cash, Bank Transfer, Mobile Banking
- Payment history per sale with amount + date + method
- Automatic "Fully Paid" badge when due is cleared
- Collect Payment button on sales list and sale detail pages

## 🏗️ System Architecture

### Database Schema

```
┌─────────────┐     ┌─────────────┐     ┌─────────────────┐
│  Products   │     │   Sales     │     │ Journal Entries │
├─────────────┤     ├─────────────┤     ├─────────────────┤
│ id          │────<│ product_id  │    ┌┤ account_id      │
│ name        │     │ quantity    │    │├─────────────────┤
│ purchase_   │     │ unit_price  │    ││ transaction_type│
│  price      │     │ subtotal    │    ││  (purchase/     │
│ sell_price  │     │ discount    │    ││   sale/payment) │
│ opening_    │     │ vat_amount  │    ││ transaction_id  │
│  stock      │     │ total_amount│    ││ debit_amount    │
│ current_    │     │ paid_amount │    ││ credit_amount   │
│  stock      │     │ due_amount  │    │└─────────────────┘
└─────────────┘     │ sale_date   │    │
                    │ customer_   │    │  ┌─────────────┐
                    │  name       │    │  │  Accounts   │
                    └──────┬──────┘    └─>│─────────────│
                           │              │ code        │
                    ┌──────┴──────┐       │ name        │
                    │  Payments   │       │ type        │
                    ├─────────────┤       │ balance     │
                    │ sale_id     │       └─────────────┘
                    │ amount      │
                    │ payment_date│
                    │ payment_    │
                    │  method     │
                    │ notes       │
                    └─────────────┘
```

### Chart of Accounts Structure

```
1000 - Assets
  ├─ 1110 - Cash
  ├─ 1120 - Accounts Receivable
  └─ 1130 - Inventory

2000 - Liabilities
  └─ 2120 - VAT Payable

3000 - Equity
  └─ 3100 - Owner's Equity

4000 - Revenue
  ├─ 4100 - Sales Revenue
  └─ 4200 - Discount Given

5000 - Expenses
  └─ 5100 - Cost of Goods Sold
```

## 📝 How Double-Entry Accounting Works

### Transaction 1: Product Purchase (Adding Inventory)

When a product is created with opening stock, the system invests capital into inventory:

**Example**: Add "Laptop" — Purchase Price: ৳60, Opening Stock: 10

| Account        | Debit   | Credit  |
| -------------- | ------- | ------- |
| Inventory      | ৳600.00 |         |
| Owner's Equity |         | ৳600.00 |
| **TOTALS**     | ৳600.00 | ৳600.00 |

✅ **Debits = Credits** (Balanced!)

### Transaction 2: Sale with Partial Payment (Gross Revenue Method)

**Example**: Sale of 10 units @ ৳100 each (Purchase price: ৳60, Discount: ৳50, Paid: ৳800)

**Calculations:**

- Subtotal: 10 × ৳100 = **৳1,000** (gross)
- Discount: ৳50
- After Discount: ৳950
- VAT (5%): ৳47.50
- **Total: ৳997.50**
- Paid: ৳800
- Due: ৳197.50
- COGS: 10 × ৳60 = ৳600

**Journal Entries Created (Gross Revenue Method):**

| Account             | Debit     | Credit        |
| ------------------- | --------- | ------------- |
| Cash                | ৳800.00   |               |
| Accounts Receivable | ৳197.50   |               |
| Sales Revenue       |           | **৳1,000.00** |
| Discount Given      | ৳50.00    |               |
| VAT Payable         |           | ৳47.50        |
| Cost of Goods Sold  | ৳600.00   |               |
| Inventory           |           | ৳600.00       |
| **TOTALS**          | ৳1,647.50 | ৳1,647.50     |

✅ **Debits = Credits** (Balanced!)

> **Note**: Sales Revenue is credited at the **GROSS** subtotal (৳1,000), not the net. The discount is tracked separately as a contra-revenue entry (Discount Given ৳50). Net Revenue = ৳1,000 − ৳50 = ৳950.

### Transaction 3: Payment Collection

When the customer pays the ৳197.50 due:

| Account             | Debit   | Credit  |
| ------------------- | ------- | ------- |
| Cash                | ৳197.50 |         |
| Accounts Receivable |         | ৳197.50 |
| **TOTALS**          | ৳197.50 | ৳197.50 |

✅ **Debits = Credits** (Balanced!)

After this payment:

- Cash: ৳800 + ৳197.50 = **৳997.50** (total received)
- Accounts Receivable: ৳197.50 − ৳197.50 = **৳0** (fully paid)

## 🚀 Installation & Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 5.7+
- Git

### Step 1: Clone and Setup

```bash
# Navigate to project directory
cd task2-inventory-management

# Install dependencies
composer install

# Create environment file
cp .env.example .env
```

### Step 2: Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=

VAT_RATE=0.05
```

### Step 3: Create Database

```bash
mysql -u root -p
CREATE DATABASE inventory_db;
exit
```

### Step 4: Run Migrations and Seeders

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the Chart of Accounts
php artisan db:seed --class=AccountSeeder
```

### Step 5: Start Development Server

```bash
php artisan serve --port=8002
```

Visit: **http://localhost:8002**

## 🎮 Usage Guide

### 1. Add Products

Navigate to **Products → Add Product**

Fill in:

- Product Name
- Description (optional)
- Purchase Price (cost you paid)
- Sell Price (price you sell for, must be ≥ purchase price)
- Opening Stock (initial quantity)

> When saved, the system automatically creates **purchase journal entries**: Debit Inventory / Credit Owner's Equity for the total inventory value.

### 2. Record Sales

Navigate to **Sales → New Sale** (or click "Sell This Product" from product detail)

The system will:

- Pre-select the product if navigated from "Sell This Product"
- Show available products with stock levels
- Calculate VAT automatically (5%)
- Show real-time sale summary
- Validate: discount cannot exceed subtotal, sufficient stock required
- Create all 7 journal entries automatically
- Accept partial or full payment

> **Quick links**: "Don't see your product?" → Add New Product link in the sale form

### 3. Collect Payments

Navigate to **Sales → Collect Payment** (button appears on sales with outstanding due)

Features:

- Shows sale summary (total, already paid, remaining due)
- Shows payment history (all previous payments)
- Enter amount (cannot exceed due amount)
- Choose payment method: Cash / Bank Transfer / Mobile Banking
- Creates journal entries: Debit Cash / Credit Accounts Receivable
- Sale automatically marked "Fully Paid" when due reaches ৳0

### 3. View Financial Reports

Navigate to **Reports → Financial Report**

Features:

- Date-wise filtering (start date and end date)
- Total sales and expenses
- Gross profit calculation
- Profit margin percentage
- VAT payable summary
- Outstanding due amounts

### 4. Check Journal Entries

Navigate to **Reports → Journal Entries**

View:

- All double-entry transactions
- Debit and credit amounts
- Account details
- Transaction references
- Link to original sale

### 5. Inventory Analysis

Navigate to **Reports → Inventory Report**

Analyze:

- Current stock levels
- Inventory value (at purchase price)
- Potential revenue (at sell price)
- Profit margins per product
- Low stock alerts

## 🔍 Key Code Components

### SaleController - Journal Entry Creation (Gross Method)

Located in: `app/Http/Controllers/SaleController.php`

The `createJournalEntries()` method implements the double-entry logic:

```php
private function createJournalEntries(Sale $sale, Product $product)
{
    // 1. Debit Cash for paid amount
    // 2. Debit Accounts Receivable for due amount
    // 3. Credit Sales Revenue at GROSS subtotal (before discount)
    // 4. Debit Discount Given (contra-revenue)
    // 5. Credit VAT Payable
    // 6. Debit Cost of Goods Sold
    // 7. Credit Inventory
    // All account balances update automatically
}
```

### PaymentController - Due Collection

Located in: `app/Http/Controllers/PaymentController.php`

```php
public function store(Request $request, Sale $sale)
{
    // Validates amount <= due_amount
    // Creates Payment record
    // Updates Sale paid_amount / due_amount
    // Creates 2 journal entries:
    //   Debit Cash (amount received)
    //   Credit Accounts Receivable (balance cleared)
}
```

### ProductController - Purchase Journal Entries

Located in: `app/Http/Controllers/ProductController.php`

```php
public function store(Request $request)
{
    // Creates product
    // Creates 2 journal entries:
    //   Debit Inventory (purchase_price × opening_stock)
    //   Credit Owner's Equity (capital invested)
}
```

### Account Model - Balance Tracking

Located in: `app/Models/Account.php`

```php
public function updateBalance($amount, $isDebit)
{
    // Determines whether to add or subtract based on account type
    // Assets/Expenses: Debit increases, Credit decreases
    // Liabilities/Revenue/Equity: Credit increases, Debit decreases
}
```

### Sale Model - Amount Calculations

Located in: `app/Models/Sale.php`

```php
public static function calculateSaleAmounts($quantity, $unitPrice, $discount, $vatRate)
{
    $subtotal = $quantity * $unitPrice;
    $afterDiscount = $subtotal - $discount;
    $vatAmount = $afterDiscount * $vatRate;
    $totalAmount = $afterDiscount + $vatAmount;

    return compact('subtotal', 'vatAmount', 'totalAmount');
}
```

## 📦 Project Structure

```
task2-inventory-management/
├── app/
│   ├── Models/
│   │   ├── Account.php          # Accounting ledger
│   │   ├── JournalEntry.php     # Transaction records
│   │   ├── Payment.php          # Payment collections
│   │   ├── Product.php          # Product catalog
│   │   ├── Sale.php             # Sales transactions
│   │   └── User.php             # Authentication
│   └── Http/Controllers/
│       ├── DashboardController.php  # Summary stats
│       ├── PaymentController.php    # Payment collection
│       ├── ProductController.php    # Product CRUD + purchase entries
│       ├── ReportController.php     # All reports (date-filtered)
│       └── SaleController.php       # Sales + sale journal entries
├── database/
│   ├── migrations/              # 7 migration files
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_accounts_table.php
│   │   ├── ..._create_products_table.php
│   │   ├── ..._create_sales_table.php
│   │   ├── ..._create_journal_entries_table.php
│   │   └── ..._create_payments_table.php
│   └── seeders/
│       └── AccountSeeder.php    # Chart of Accounts (17 accounts)
├── resources/views/
│   ├── layout.blade.php         # Base template with navbar
│   ├── dashboard.blade.php      # Main dashboard
│   ├── products/                # Product management views
│   │   ├── index.blade.php      #   List with sell/edit buttons
│   │   ├── create.blade.php     #   Add product form
│   │   ├── edit.blade.php       #   Edit product form
│   │   └── show.blade.php       #   Detail + purchase journal entries
│   ├── sales/                   # Sales recording views
│   │   ├── index.blade.php      #   List with collect payment buttons
│   │   ├── create.blade.php     #   New sale form (product pre-select)
│   │   └── show.blade.php       #   Detail + journal entries + payments
│   ├── payments/
│   │   └── create.blade.php     # Payment collection form + history
│   └── reports/                 # Financial reporting views
│       ├── index.blade.php      #   Report hub
│       ├── financial.blade.php  #   P&L with date filtering
│       ├── journal_entries.blade.php  # Audit trail
│       ├── chart_of_accounts.blade.php # Account balances
│       ├── sales.blade.php      #   Sales analysis
│       └── inventory.blade.php  #   Stock & profitability
└── routes/
    └── web.php                  # All application routes
```

## 🧪 Testing the System

### Test Scenario 1: Complete Sale & Payment Lifecycle

1. **Add a product**: "Laptop" — Purchase: ৳50,000, Sell: ৳75,000, Stock: 10
   - ✅ Verify: Inventory account += ৳500,000, Owner's Equity += ৳500,000
2. **Record a sale**: Quantity: 2, Discount: ৳1,000, Paid: ৳100,000
   - Expected:
     - Subtotal: ৳150,000 (gross)
     - After Discount: ৳149,000
     - VAT (5%): ৳7,450
     - Total: ৳156,450
     - Due: ৳56,450
   - ✅ Verify: 7 journal entries created, Sales Revenue = ৳150,000 (gross)
   - ✅ Verify: Discount Given = ৳1,000, Cash = ৳100,000, A/R = ৳56,450
3. **Collect payment**: ৳56,450 via Bank Transfer
   - ✅ Verify: Cash += ৳56,450, A/R = ৳0 (cleared), Sale shows "Fully Paid"
4. **Check financial report**: All debits = all credits (balanced)

### Test Scenario 2: Multiple Partial Payments

1. Create product, make sale with ৳0 initial payment (fully on credit)
2. Collect 3 partial payments over different dates
3. ✅ Verify: Each payment creates 2 journal entries (Cash debit / AR credit)
4. ✅ Verify: Sale's due amount decreases correctly with each payment

### Test Scenario 3: Financial Report Date Filtering

1. Record sales on different dates
2. Go to **Reports → Financial Report**
3. Filter by date range
4. ✅ Verify:
   - Revenue/expense figures change when dates change (computed from filtered journal entries)
   - Net Sales = Sum of filtered Sales Revenue − Discount Given
   - Total Expenses = Sum of filtered COGS
   - Gross Profit = Net Sales − Total Expenses
   - Profit Margin = (Gross Profit / Net Sales) × 100

### Test Scenario 4: Validation Checks

1. Try selling more than available stock → **Error: Insufficient stock**
2. Try discount > subtotal → **Error: Discount cannot exceed subtotal**
3. Try paying more than due → **Error: Amount cannot exceed due amount**
4. Try sell price < purchase price → **Error: Sell price must be ≥ purchase price**

## 🛠️ Troubleshooting

### Issue: "Insufficient stock" error

**Solution**: Check product current stock. Add more opening stock or wait for stock replenishment.

### Issue: Journal entries not balanced

**Solution**: This should never happen due to automated calculations. Check the `createJournalEntries()` method logic.

### Issue: Negative account balances

**Solution**: Verify the account type's normal balance side in the `isDebitNormal()` method.

### Issue: VAT calculation incorrect

**Solution**: Check `VAT_RATE` in `.env` file. Default is 0.05 (5%).

## 📊 Default Accounts Configuration

The system seeds these accounts automatically:

| Code | Account Name        | Type      | Normal Balance |
| ---- | ------------------- | --------- | -------------- |
| 1110 | Cash                | Asset     | Debit          |
| 1120 | Accounts Receivable | Asset     | Debit          |
| 1130 | Inventory           | Asset     | Debit          |
| 2120 | VAT Payable         | Liability | Credit         |
| 3100 | Owner's Equity      | Equity    | Credit         |
| 4100 | Sales Revenue       | Revenue   | Credit         |
| 4200 | Discount Given      | Revenue   | Debit          |
| 5100 | Cost of Goods Sold  | Expense   | Debit          |

## 🎓 Learning Outcomes

This project demonstrates:

1. ✅ **Laravel Framework**: Models, Controllers, Views, Migrations, Seeders, Relationships
2. ✅ **Database Design**: Relational data with foreign keys, polymorphic-like journal references
3. ✅ **Accounting Principles**: Double-entry bookkeeping, Chart of Accounts, Gross Revenue Method
4. ✅ **Business Logic**: Transaction processing, inventory management, payment collection
5. ✅ **Financial Reporting**: Date-filtered P&L, journal entry audit trail, account balances
6. ✅ **Frontend Development**: Blade templates, dynamic forms with JavaScript, responsive CSS
7. ✅ **Data Integrity**: DB transactions, server-side + client-side validation, balanced entries
8. ✅ **Payment Workflow**: Partial/full payments, multi-method support, AR reconciliation

## 📄 Routes Summary

| Method   | URI                           | Controller                       | Description                              |
| -------- | ----------------------------- | -------------------------------- | ---------------------------------------- |
| GET      | `/`                           | DashboardController@index        | Dashboard with summary stats             |
| GET/POST | `/products`                   | ProductController@index/store    | List / create products                   |
| GET      | `/products/create`            | ProductController@create         | New product form                         |
| GET      | `/products/{id}`              | ProductController@show           | Product detail + journal entries         |
| GET/PUT  | `/products/{id}/edit`         | ProductController@edit/update    | Edit product                             |
| DELETE   | `/products/{id}`              | ProductController@destroy        | Delete product                           |
| GET/POST | `/sales`                      | SaleController@index/store       | List / create sales                      |
| GET      | `/sales/create`               | SaleController@create            | New sale form (supports ?product_id)     |
| GET      | `/sales/{id}`                 | SaleController@show              | Sale detail + journal entries + payments |
| GET      | `/sales/{id}/payments/create` | PaymentController@create         | Payment collection form                  |
| POST     | `/sales/{id}/payments`        | PaymentController@store          | Process payment                          |
| GET      | `/reports`                    | ReportController@index           | Report hub                               |
| GET      | `/reports/financial`          | ReportController@financial       | P&L with date filter                     |
| GET      | `/reports/journal-entries`    | ReportController@journalEntries  | All journal entries                      |
| GET      | `/reports/chart-of-accounts`  | ReportController@chartOfAccounts | Account balances                         |
| GET      | `/reports/sales`              | ReportController@sales           | Sales report with date filter            |
| GET      | `/reports/inventory`          | ReportController@inventory       | Inventory analysis                       |

## 📄 License

This is an academic project created for educational purposes.

## 👨‍💻 Developer

Created as part of academic coursework demonstrating full-stack development with Laravel and accounting integration.

---

**Note**: This system implements simplified accounting suitable for small business inventory management. For enterprise applications, additional features like multi-currency support, tax jurisdictions, and audit trails would be recommended.
