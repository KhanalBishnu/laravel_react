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
        $limit=$data['paginatedValue'] ?? 8;
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
            return $this->jsonResponse(null,$validation->errors()->first(),false,400);
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
            return $this->jsonResponse(null,$validation->errors()->first(),false,400);
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


    // public function isLeadAssignedForFormType($evaluationFormTypeId)
    // {
    //     return $this->evaluatorsAsLeadForFormType($evaluationFormTypeId)->exists();
    // }

    // // Existing relationship to get evaluators for a specific lead and evaluation form type
    // public function evaluatorsAsLeadForFormType($evaluationFormTypeId)
    // {
    //     return $this->hasMany(Evaluator::class, 'lead_id')
    //                 ->where('evaluation_form_type_id', $evaluationFormTypeId);
    // }


// try {
//     return DB::transaction(function () use ($data) {
//         $data['staff_ids'] = $data['staff_ids'] ?? [];
//         $isUpdate = $data['update'] ?? false;

//         // Check if the lead is assigned to themselves
//         if (in_array($data['lead_id'], $data['staff_ids'])) {
//             return [
//                 'response' => false,
//                 'message' => 'Lead cannot be assigned to oneself. Please select a different staff to assign this lead.'
//             ];
//         }

//         $lead = DepartmentStaff::findOrFail($data['lead_id']);
//         $evaluationFormTypeId = $data['evaluation_form_type_id'];
//         $message = $isUpdate ? 'updated' : 'added';

//         // If 'all' is selected, assign all enabled staff
//         if (count($data['staff_ids']) == 1 && $data['staff_ids'][0] == 'all') {
//             $staffIds = DepartmentStaff::whereHas('user', function ($query) {
//                 $query->where('is_enabled', 1);
//             })->pluck('id')->toArray();

//             // Delete existing evaluators for the given form type and lead
//             Evaluator::where('evaluation_form_type_id', $evaluationFormTypeId)
//                 ->where('lead_id', $lead->id)
//                 ->delete();

//             // Bulk insert evaluators
//             $evaluators = array_map(function ($staff_id) use ($lead, $evaluationFormTypeId) {
//                 return [
//                     'staff_id' => $staff_id,
//                     'lead_id' => $lead->id,
//                     'evaluation_form_type_id' => $evaluationFormTypeId,
//                 ];
//             }, $staffIds);

//             Evaluator::insert($evaluators);

//             return [
//                 'response' => true,
//                 'message' => "Evaluation lead setting $message successfully"
//             ];
//         }

//         // Handle the update or create scenario
//         if ($isUpdate) {
//             // Delete existing evaluators for the given form type and lead
//             Evaluator::where('evaluation_form_type_id', $evaluationFormTypeId)
//                 ->where('lead_id', $lead->id)
//                 ->delete();
//         } else {
//             $existingStaffIds = $lead->associatedStaffs($evaluationFormTypeId)->pluck('staff_id')->toArray();
//         }

//         $newEvaluators = [];

//         foreach ($data['staff_ids'] as $staff_id) {
//             if (!isset($existingStaffIds) || !in_array($staff_id, $existingStaffIds)) {
//                 $newEvaluators[] = [
//                     'staff_id' => $staff_id,
//                     'lead_id' => $lead->id,
//                     'evaluation_form_type_id' => $evaluationFormTypeId,
//                 ];
//             }
//         }

//         if (!empty($newEvaluators)) {
//             Evaluator::insert($newEvaluators);
//         }

//         return [
//             'response' => true,
//             'message' => "Evaluation lead setting $message successfully"
//         ];
//     });
// } catch (\Throwable $th) {
//     return [
//         'response' => false,
//         'message' => $th->getMessage()
//     ];
// }


}
