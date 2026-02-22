<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index()
    {
        $products = Product::orderBy('name')->paginate(20);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'opening_stock' => 'required|integer|min:0',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'purchase_price' => $validated['purchase_price'],
            'sell_price' => $validated['sell_price'],
            'opening_stock' => $validated['opening_stock'],
            'current_stock' => $validated['opening_stock'], // Initially same as opening stock
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load('sales');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'opening_stock' => 'required|integer|min:0',
        ]);

        // Calculate the sold quantity before update
        $soldQuantity = $product->opening_stock - $product->current_stock;
        
        // Validate that new opening stock is not less than sold quantity
        if ($validated['opening_stock'] < $soldQuantity) {
            return back()->withErrors([
                'opening_stock' => "Opening stock cannot be less than already sold quantity ({$soldQuantity} units)."
            ])->withInput();
        }

        // Calculate new current stock
        $newCurrentStock = $validated['opening_stock'] - $soldQuantity;

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'purchase_price' => $validated['purchase_price'],
            'sell_price' => $validated['sell_price'],
            'opening_stock' => $validated['opening_stock'],
            'current_stock' => $newCurrentStock,
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
