<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * List active products (public).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::active()->ordered();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate($request->integer('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $products->through(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'price' => $p->price,
                'formatted_price' => $p->formatted_price,
                'image' => $p->image_url,
                'average_rating' => $p->average_rating,
                'rating_count' => $p->rating_count,
                'created_at' => $p->created_at?->toISOString(),
                'updated_at' => $p->updated_at?->toISOString(),
            ])->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Show a single product (public).
     */
    public function show(Product $product): JsonResponse
    {
        if (!$product->is_active) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'image' => $product->image_url,
                'average_rating' => $product->average_rating,
                'rating_count' => $product->rating_count,
                'created_at' => $product->created_at?->toISOString(),
                'updated_at' => $product->updated_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Store a new product (admin).
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image_url,
            ],
            'message' => 'Produk berhasil ditambahkan.',
        ], 201);
    }

    /**
     * Update a product (admin). Uses POST + _method for multipart.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image_url,
            ],
            'message' => 'Produk berhasil diperbarui.',
        ]);
    }

    /**
     * Delete a product (admin, soft delete).
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}
