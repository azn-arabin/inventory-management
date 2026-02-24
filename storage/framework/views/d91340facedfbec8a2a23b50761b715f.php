

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<h1 style="font-size: 2rem; margin-bottom: 2rem; color: #1f2937;">Welcome to Inventory Management System</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Total Products</div>
        <div class="stat-value"><?php echo e($totalProducts); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Sales</div>
        <div class="stat-value"><?php echo e($totalSales); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Revenue</div>
        <div class="stat-value">৳<?php echo e(number_format($totalRevenue, 2)); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Due</div>
        <div class="stat-value">৳<?php echo e(number_format($totalDue, 2)); ?></div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <div class="stat-title">Cash Balance</div>
        <div class="stat-value">৳<?php echo e(number_format($cashBalance, 2)); ?></div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <div class="stat-title">Inventory Value</div>
        <div class="stat-value">৳<?php echo e(number_format($inventoryBalance, 2)); ?></div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
        <div class="stat-title">Accounts Receivable</div>
        <div class="stat-value">৳<?php echo e(number_format($receivableBalance, 2)); ?></div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
        <div class="stat-title">Sales Revenue</div>
        <div class="stat-value">৳<?php echo e(number_format($revenueBalance, 2)); ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
    <div class="card">
        <h2 class="card-header">Low Stock Products</h2>
        <?php if($lowStockProducts->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($product->name); ?></td>
                            <td class="text-right">
                                <span class="badge <?php echo e($product->current_stock == 0 ? 'badge-danger' : 'badge-warning'); ?>">
                                    <?php echo e($product->current_stock); ?> units
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">All products have sufficient stock.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2 class="card-header">Recent Sales</h2>
        <?php if($recentSales->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($sale->product->name); ?> (<?php echo e($sale->quantity); ?>x)</td>
                            <td class="text-right">৳<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No sales recorded yet.</p>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top: 2rem; text-align: center;">
    <a href="<?php echo e(route('sales.create')); ?>" class="btn btn-primary" style="margin-right: 1rem;">Make a Sale</a>
    <a href="<?php echo e(route('products.create')); ?>" class="btn btn-success" style="margin-right: 1rem;">Add Product</a>
    <a href="<?php echo e(route('reports.financial')); ?>" class="btn btn-secondary">View Reports</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Arabin\Desktop\Projects\My Project\task\task2-inventory-management\resources\views/dashboard.blade.php ENDPATH**/ ?>