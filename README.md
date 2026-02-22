# Inventory Management System with Accounting

A complete Laravel-based inventory management system featuring **double-entry bookkeeping**, comprehensive financial reports, and automated journal entries for every transaction.

## 🎯 Project Overview

This system implements a professional-grade inventory management solution with proper accounting principles:

- **Product Management**: Track products with purchase/sell prices and stock levels
- **Sales Recording**: Record sales with automatic stock adjustment
- **Double-Entry Accounting**: Every transaction creates proper debit/credit journal entries
- **Financial Reports**: Date-wise filtering for sales, expenses, and profit analysis
- **Chart of Accounts**: Standard accounting structure (Assets, Liabilities, Equity, Revenue, Expenses)
- **Automated VAT Calculation**: Configurable VAT rate with automatic journal entries

## 📊 Key Features

### 1. **Complete Accounting System**
- Implements proper double-entry bookkeeping principles
- 7 journal entries per sale transaction:
  - Cash/Accounts Receivable (Debit)
  - Sales Revenue (Credit)
  - Discount Given (Debit)
  - VAT Payable (Credit)
  - Cost of Goods Sold (Debit)
  - Inventory (Credit)
  - Balance adjustment automatically

### 2. **Financial Reporting**
- **Financial Report**: Total sales, expenses, profit margin with date filtering
- **Journal Entries**: Complete audit trail of all transactions
- **Chart of Accounts**: Full account hierarchy with balances
- **Sales Report**: Detailed sales analysis with payment status
- **Inventory Report**: Stock levels, values, and profitability analysis

### 3. **Inventory Management**
- Real-time stock tracking
- Low stock alerts
- Product profitability analysis
- Opening stock vs current stock management

## 🏗️ System Architecture

### Database Schema

```
┌─────────────┐     ┌─────────────┐     ┌─────────────────┐
│  Products   │     │   Sales     │     │ Journal Entries │
├─────────────┤     ├─────────────┤     ├─────────────────┤
│ id          │────<│ product_id  │    ┌┤ account_id      │
│ name        │     │ quantity    │    │├─────────────────┤
│ purchase_   │     │ unit_price  │    ││ transaction_type│
│  price      │     │ subtotal    │    ││ transaction_id  │>─┐
│ sell_price  │     │ discount    │    ││ debit_amount    │  │
│ opening_    │     │ vat_amount  │    ││ credit_amount   │  │
│  stock      │     │ total_amount│    │└─────────────────┘  │
│ current_    │     │ paid_amount │    │                     │
│  stock      │     │ due_amount  │    │                     │
└─────────────┘     └─────────────┘    │  ┌─────────────┐   │
                                        │  │  Accounts   │   │
                                        └─>│─────────────│<──┘
                                           │ id          │
                                           │ code        │
                                           │ name        │
                                           │ type        │
                                           │ balance     │
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

### Example: Sale of 10 units @ ৳100 each (Purchase price: ৳60, Discount: ৳50, Paid: ৳800)

**Calculations:**
- Subtotal: 10 × ৳100 = ৳1,000
- Discount: ৳50
- After Discount: ৳950
- VAT (5%): ৳47.50
- **Total: ৳997.50**
- Paid: ৳800
- Due: ৳197.50
- COGS: 10 × ৳60 = ৳600

**Journal Entries Created:**

| Account              | Debit   | Credit  |
|----------------------|---------|---------|
| Cash                 | ৳800.00 |         |
| Accounts Receivable  | ৳197.50 |         |
| Sales Revenue        |         | ৳950.00 |
| Discount Given       | ৳50.00  |         |
| VAT Payable          |         | ৳47.50  |
| Cost of Goods Sold   | ৳600.00 |         |
| Inventory            |         | ৳600.00 |
| **TOTALS**           | ৳1,647.50 | ৳1,647.50 |

✅ **Debits = Credits** (Balanced!)

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
- Sell Price (price you sell for)
- Opening Stock (initial quantity)

### 2. Record Sales

Navigate to **Sales → New Sale**

The system will:
- Show available products with stock levels
- Calculate VAT automatically
- Show real-time sale summary
- Warn about insufficient stock
- Create all journal entries automatically

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

### SaleController - Journal Entry Creation

Located in: `app/Http/Controllers/SaleController.php`

The `createJournalEntries()` method implements the double-entry logic:

```php
private function createJournalEntries(Sale $sale, Product $product)
{
    // 1. Debit Cash for paid amount
    // 2. Debit Accounts Receivable for due amount
    // 3. Credit Sales Revenue
    // 4. Debit Discount Given
    // 5. Credit VAT Payable
    // 6. Debit Cost of Goods Sold
    // 7. Credit Inventory
    // All account balances update automatically
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
│   │   ├── Product.php          # Product catalog
│   │   └── Sale.php             # Sales transactions
│   └── Http/Controllers/
│       ├── ProductController.php    # Product CRUD
│       ├── SaleController.php       # Sales + Accounting
│       ├── ReportController.php     # All reports
│       └── DashboardController.php  # Summary stats
├── database/
│   ├── migrations/              # Database schema
│   └── seeders/
│       └── AccountSeeder.php    # Chart of Accounts setup
├── resources/views/
│   ├── layout.blade.php         # Base template
│   ├── dashboard.blade.php      # Main dashboard
│   ├── products/                # Product management views
│   ├── sales/                   # Sales recording views
│   └── reports/                 # Financial reporting views
└── routes/
    └── web.php                  # Application routes
```

## 🧪 Testing the System

### Test Scenario 1: Basic Sale

1. Add a product: "Laptop" - Purchase: ৳50,000, Sell: ৳75,000, Stock: 10
2. Record a sale: Quantity: 2, Discount: ৳1,000, Paid: ৳100,000
3. Expected Results:
   - Subtotal: ৳150,000
   - After Discount: ৳149,000
   - VAT (5%): ৳7,450
   - Total: ৳156,450
   - Due: ৳56,450
   - Current Stock: 8 units
   - 7 journal entries created
   - Cash account: +৳100,000
   - Accounts Receivable: +৳56,450

### Test Scenario 2: Financial Report

1. Record multiple sales over different dates
2. Go to Reports → Financial Report
3. Filter by date range
4. Verify:
   - Total Sales = Sum of all sale subtotals minus discounts
   - Total Expenses = Sum of COGS
   - Gross Profit = Total Sales - Total Expenses
   - Profit Margin = (Gross Profit / Total Sales) × 100

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

| Code | Account Name           | Type      | Normal Balance |
|------|------------------------|-----------|----------------|
| 1110 | Cash                   | Asset     | Debit          |
| 1120 | Accounts Receivable    | Asset     | Debit          |
| 1130 | Inventory              | Asset     | Debit          |
| 2120 | VAT Payable            | Liability | Credit         |
| 3100 | Owner's Equity         | Equity    | Credit         |
| 4100 | Sales Revenue          | Revenue   | Credit         |
| 4200 | Discount Given         | Revenue   | Debit          |
| 5100 | Cost of Goods Sold     | Expense   | Debit          |

## 🎓 Learning Outcomes

This project demonstrates:

1. ✅ **Laravel Framework**: Models, Controllers, Views, Migrations, Seeders
2. ✅ **Database Design**: Relational data with foreign keys
3. ✅ **Accounting Principles**: Double-entry bookkeeping, Chart of Accounts
4. ✅ **Business Logic**: Transaction processing, inventory management
5. ✅ **Financial Reporting**: Date filtering, calculations, analysis
6. ✅ **Frontend Development**: Blade templates, CSS, JavaScript
7. ✅ **Data Integrity**: Database transactions, validation, error handling

## 📄 License

This is an academic project created for educational purposes.

## 👨‍💻 Developer

Created as part of academic coursework demonstrating full-stack development with Laravel and accounting integration.

---

**Note**: This system implements simplified accounting suitable for small business inventory management. For enterprise applications, additional features like multi-currency support, tax jurisdictions, and audit trails would be recommended.
