<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'response' => true,
            'products' => Product::with('media')->latest()->get()
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'title' => 'required',
            'description' => 'required',
            'file' => 'nullable|mimes:jpg,png',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'response' => false,
                'message' => $validation->errors()
            ]);
        }
        try {
            DB::transaction(function () use ($data) {
                $product = Product::create([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'tags' => $data['tags'],
                    'user_id' => auth()->id(),
                ]);
                if (array_key_exists('file', $data)) {
                    $product->addMedia($data['file'])->toMediaCollection('product-image');
                }
            });
            return response()->json([
                'response' => true,
                'message' => 'Product Created Successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function update(Request $request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'title' => 'required',
            'description' => 'required',
            'file' => 'nullable|mimes:jpg,png',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'response' => false,
                'message' => $validation->errors()
            ]);
        }
        try {
            DB::transaction(function () use ($data) {
                $product=Product::findOrFail($data['id']);
                $product->update([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'tags' => $data['tags'],
                    'user_id' => auth()->id(),
                ]);
                if (array_key_exists('file', $data)) {
                    if($product->hasMedia('product-image')){
                        $product->clearMediaCollection('product-image');
                    }
                    $product->addMedia($data['file'])->toMediaCollection('product-image');
                }
            });
            return response()->json([
                'response' => true,
                'message' => 'Product updated Successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function delete($id)
    {
        try {
            Product::findOrFail($id)->delete();
            return response()->json([
                'response' => true,
                'message' => 'Product deleted Successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
