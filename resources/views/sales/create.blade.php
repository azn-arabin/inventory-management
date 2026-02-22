@extends('layout')

@section('title', 'New Sale')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h1 class="card-header">Record New Sale</h1>

    <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
        @csrf

        <div class="form-group">
            <label for="product_id" class="form-label">Select Product *</label>
            <select class="form-control" id="product_id" name="product_id" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-price="{{ $product->sell_price }}"
                            data-stock="{{ $product->current_stock }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} - Stock: {{ $product->current_stock }} - Price: ৳{{ number_format($product->sell_price, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity" class="form-label">Quantity *</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                <small id="stockInfo" class="text-muted"></small>
            </div>

            <div class="form-group">
                <label for="discount" class="form-label">Discount (৳)</label>
                <input type="number" step="0.01" class="form-control" id="discount" name="discount" value="{{ old('discount', 0) }}" min="0">
            </div>

            <div class="form-group">
                <label for="paid_amount" class="form-label">Paid Amount (৳) *</label>
                <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', 0) }}" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Walk-in Customer">
        </div>

        <div class="form-group">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
        </div>

        <div class="card" style="background-color: #f3f4f6; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.2rem; margin-bottom: 1rem;">Sale Summary</h3>
            <table style="font-size: 0.95rem;">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right" id="subtotalDisplay">৳0.00</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td class="text-right" id="discountDisplay">-৳0.00</td>
                </tr>
                <tr>
                    <td>After Discount:</td>
                    <td class="text-right" id="afterDiscountDisplay">৳0.00</td>
                </tr>
                <tr>
                    <td>VAT ({{ $vatRate * 100 }}%):</td>
                    <td class="text-right" id="vatDisplay">৳0.00</td>
                </tr>
                <tr style="font-weight: bold; font-size: 1.1rem; border-top: 2px solid #d1d5db;">
                    <td>Total Amount:</td>
                    <td class="text-right" id="totalDisplay">৳0.00</td>
                </tr>
                <tr style="color: #10b981;">
                    <td>Paid:</td>
                    <td class="text-right" id="paidDisplay">৳0.00</td>
                </tr>
                <tr style="color: #ef4444; font-weight: 600;">
                    <td>Due:</td>
                    <td class="text-right" id="dueDisplay">৳0.00</td>
                </tr>
            </table>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Record Sale</button>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    const vatRate = {{ $vatRate }};
    
    function calculateSale() {
        const productSelect = document.getElementById('product_id');
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const paid = parseFloat(document.getElementById('paid_amount').value) || 0;
        
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const unitPrice = parseFloat(selectedOption.dataset.price) || 0;
        const stock = parseInt(selectedOption.dataset.stock) || 0;
        
        if (quantity > stock) {
            document.getElementById('stockInfo').textContent = `⚠️ Insufficient stock! Available: ${stock}`;
            document.getElementById('stockInfo').style.color = '#ef4444';
        } else {
            document.getElementById('stockInfo').textContent = `Available stock: ${stock}`;
            document.getElementById('stockInfo').style.color = '#10b981';
        }
        
        const subtotal = quantity * unitPrice;
        const afterDiscount = subtotal - discount;
        const vat = afterDiscount * vatRate;
        const total = afterDiscount + vat;
        const due = total - paid;
        
        document.getElementById('subtotalDisplay').textContent = `৳${subtotal.toFixed(2)}`;
        document.getElementById('discountDisplay').textContent = `-৳${discount.toFixed(2)}`;
        document.getElementById('afterDiscountDisplay').textContent = `৳${afterDiscount.toFixed(2)}`;
        document.getElementById('vatDisplay').textContent = `৳${vat.toFixed(2)}`;
        document.getElementById('totalDisplay').textContent = `৳${total.toFixed(2)}`;
        document.getElementById('paidDisplay').textContent = `৳${paid.toFixed(2)}`;
        document.getElementById('dueDisplay').textContent = `৳${due.toFixed(2)}`;
    }
    
    document.getElementById('product_id').addEventListener('change', calculateSale);
    document.getElementById('quantity').addEventListener('input', calculateSale);
    document.getElementById('discount').addEventListener('input', calculateSale);
    document.getElementById('paid_amount').addEventListener('input', calculateSale);
    
    calculateSale();
</script>
@endsection
