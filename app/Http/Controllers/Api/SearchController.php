<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (empty(trim($query))) {
            return response()->json([
                'results' => [],
            ]);
        }

        $results = Product::search($query)
            ->query(fn ($builder) => $builder->with(['basePrice', 'mainCategory', 'images']))
            ->take(6)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->getSlug(),
                    'url' => route('product.show', $product->getSlug()),
                    'image' => $product->image
                        ? asset('storage/'.$product->image)
                        : ($product->images->first()?->path
                            ? asset('storage/'.$product->images->first()->path)
                            : null),
                    'price' => $product->basePrice?->price,
                    'category' => $product->main_category?->name,
                    'brand' => $product->attributeValues?->firstWhere('attribute_id', 58)?->value ?? null,
                ];
            });

        return response()->json([
            'results' => $results,
            'total' => $results->count(),
        ]);
    }
}
