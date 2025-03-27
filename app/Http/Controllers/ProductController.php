<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Product::with('variants');

        if ($request->search) {
            $search = $request->search;
            $query->where('nama_produk', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('variants', function($q) use ($search) {
                      $q->where('sku', 'LIKE', '%' . $search . '%')
                        ->orWhere('warna', 'LIKE', '%' . $search . '%');
                  });
        }

        // Status filter
        if ($request->status && in_array($request->status, ['Request', 'proses', 'selesai'])) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(8);
        
        // Get counts for status cards
        $counts = [
            'request' => Product::where('status', 'Request')->count(),
            'proses' => Product::where('status', 'proses')->count(),
            'selesai' => Product::where('status', 'selesai')->count(),
        ];

        if ($request->ajax()) {
            return view('products.table-rows', compact('products'));
        }

        return view('products.list', compact('products', 'counts'));
    }

    public function showRequestForm()
    {
        return view('products.request');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|max:100',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|numeric',
            'variants.*.warna' => 'required|max:255',
            'variants.*.harga' => 'required|numeric',
            'status' => 'required|in:Request,proses,selesai',
        ]);

        $product = Product::create([
            'nama_produk' => $validated['nama_produk'],
            'status' => $validated['status']
        ]);

        foreach ($validated['variants'] as $variant) {
            $product->variants()->create($variant);
        }

        try {
            $this->notificationService->notifyNewProduct($product);
        } catch (\Exception $e) {
            \Log::error('Notification failed: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product request has been submitted successfully.'
            ]);
        } else {
            return redirect()->route('products.list')
                ->with('success', 'Product request has been submitted successfully.');
        }
    }

    public function edit(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('variants')
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|max:100',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|numeric',
            'variants.*.warna' => 'required|max:255',
            'variants.*.harga' => 'required|numeric',
            'status' => 'required|in:Request,proses,selesai'
        ]);

        $product->update([
            'nama_produk' => $validated['nama_produk'],
            'status' => $validated['status']
        ]);

        // Send notification if status is changed to 'selesai'
        if ($validated['status'] === 'selesai') {
            $this->notificationService->notifyCompletedProduct($product);
        }

        // Delete existing variants
        $product->variants()->delete();

        // Create new variants
        foreach ($validated['variants'] as $variant) {
            $product->variants()->create($variant);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        } else {
            return redirect()->route('products.list')
                ->with('success', 'Product updated successfully.');
        }
    }
}