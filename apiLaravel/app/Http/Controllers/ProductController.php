<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $data=$request->all();
        $page=$data['page'] ?? 1;
        $limit=$data['limit'] ?? 8;
        $offset=($page-1)*$limit;
        $products=Product::with('media')
        ->when(!auth()->user()->hasPermissionTo('View All|Product'),function($q){
            $q->where('user_id',auth()->id());
        });

        $totalProducts=$products->count();
        $products=$products->latest()->offset($offset)->limit($limit)->get();
        return response()->json([
            'response' => true,
            'products' => $products,
            'totalQuries' => $totalProducts
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

    public function NoAuthProduct(Request $request)
    {
        try {
            $data=$request->all();
            $page=$data['page'] ?? 1;
            $limit=$data['limit'] ?? 12;
            $offset=($page-1)*$limit;
            $products=Product::with('media');
            $data['totalProducts']=$products->count();
            $data['products']=$products->latest()->offset($offset)->limit($limit)->get();
            return $this->jsonResponse($data,null,true,200);
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,200);

        }
       
    }
    public function NoAuthProductDetail($id)
    {
        try {
            $product=Product::with('media')->findOrFail($id);
            return $this->jsonResponse($product,null,true,200);
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,200);

        }
       
    }
}
