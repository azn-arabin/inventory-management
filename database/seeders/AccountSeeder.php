<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates a basic Chart of Accounts for the inventory system
     */
    public function run(): void
    {
        $accounts = [
            // Assets
            [
                'code' => '1000',
                'name' => 'Assets',
                'type' => 'asset',
                'parent_id' => null,
            ],
            [
                'code' => '1100',
                'name' => 'Current Assets',
                'type' => 'asset',
                'parent_id' => 1, // Assets
            ],
            [
                'code' => '1110',
                'name' => 'Cash',
                'type' => 'asset',
                'parent_id' => 2, // Current Assets
            ],
            [
                'code' => '1120',
                'name' => 'Accounts Receivable',
                'type' => 'asset',
                'parent_id' => 2, // Current Assets
            ],
            [
                'code' => '1130',
                'name' => 'Inventory',
                'type' => 'asset',
                'parent_id' => 2, // Current Assets
            ],

            // Liabilities
            [
                'code' => '2000',
                'name' => 'Liabilities',
                'type' => 'liability',
                'parent_id' => null,
            ],
            [
                'code' => '2100',
                'name' => 'Current Liabilities',
                'type' => 'liability',
                'parent_id' => 6, // Liabilities
            ],
            [
                'code' => '2110',
                'name' => 'Accounts Payable',
                'type' => 'liability',
                'parent_id' => 7, // Current Liabilities
            ],
            [
                'code' => '2120',
                'name' => 'VAT Payable',
                'type' => 'liability',
                'parent_id' => 7, // Current Liabilities
            ],

            // Equity
            [
                'code' => '3000',
                'name' => 'Equity',
                'type' => 'equity',
                'parent_id' => null,
            ],
            [
                'code' => '3100',
                'name' => 'Owner\'s Equity',
                'type' => 'equity',
                'parent_id' => 10, // Equity
            ],
            [
                'code' => '3200',
                'name' => 'Retained Earnings',
                'type' => 'equity',
                'parent_id' => 10, // Equity
            ],

            // Revenue
            [
                'code' => '4000',
                'name' => 'Revenue',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => '4100',
                'name' => 'Sales Revenue',
                'type' => 'revenue',
                'parent_id' => 13, // Revenue
            ],
            [
                'code' => '4200',
                'name' => 'Discount Given',
                'type' => 'revenue',
                'parent_id' => 13, // Revenue (contra-revenue)
            ],

            // Expenses
            [
                'code' => '5000',
                'name' => 'Expenses',
                'type' => 'expense',
                'parent_id' => null,
            ],
            [
                'code' => '5100',
                'name' => 'Cost of Goods Sold',
                'type' => 'expense',
                'parent_id' => 16, // Expenses
            ],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
