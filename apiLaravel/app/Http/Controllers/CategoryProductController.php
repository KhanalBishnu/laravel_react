<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryProductController extends Controller
{
    public function index(Request $request)
    {
        $data=$request->all();
        $page=$data['page'] ?? 1;
        $limit=$data['limit'] ?? 8;
        $offset=($page-1)*$limit;
        $category=CategoryProduct::with('media');
        $response['total']=$category->count();
        $response['items']=$category->latest()->offset($offset)->limit($limit)->get();
        return $this->jsonResponse($response,null,true,200);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'name' => 'required',
            'description' => 'nullable',
            'file' => 'nullable|mimes:jpg,png',
        ]);
        if ($validation->fails()) {
            return $this->jsonResponse(null,$validation->errors(),false,400);
        }
        try {
            DB::transaction(function () use ($data) {
                // dd($data);
                $category = CategoryProduct::create([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status'=>$data['status']=="false"?0:1,
                    'creator_id' => auth()->id(),
                ]);
                if (array_key_exists('file', $data)) {
                    $category->addMedia($data['file'])->toMediaCollection('category-product-image');
                }
            });
           return $this->jsonResponse(null,'Category Product Created Successfully',true,200);
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }
    public function update(Request $request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'name' => 'required',
            'description' => 'nullable',
            'file' => 'nullable|mimes:jpg,png',
        ]);
        if ($validation->fails()) {
            return $this->jsonResponse(null,$validation->errors(),false,400);
        }
        try {
            DB::transaction(function () use ($data) {
                $category=CategoryProduct::findOrFail($data['id']);
                $category->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status'=>$data['status']=="true"? 1:0,
                    'creator_id' => auth()->id(),
                ]);
                if(array_key_exists('removedFiles',$data)){
                    $category->clearMediaCollection('category-product-image');
                }
                if (array_key_exists('file', $data)) {
                    if($category->hasMedia('category-product-image')){
                        $category->clearMediaCollection('category-product-image');
                    }
                    $category->addMedia($data['file'])->toMediaCollection('category-product-image');
                }

            });
            return $this->jsonResponse(null,'Product updated Successfully',true,200);
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }
    public function delete($id)
    {
        try {
            CategoryProduct::findOrFail($id)->delete();
            return $this->jsonResponse(null,'Category Product deleted Successfully',true,200);
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }

}
