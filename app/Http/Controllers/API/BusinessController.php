<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Business;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BusinessResource;
use Illuminate\Http\JsonResponse;

class BusinessController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        
        //$items = Bussiness::paginate(1); 
        
        $items = Business::paginate(2)->appends(['sort' => 'name']);

        $data = [
            'data' => BusinessResource::collection($items->items()), // Format the items
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'next_page_url' => $items->nextPageUrl(),
                'prev_page_url' => $items->previousPageUrl()
            ]
        ];

        // Return the response with formatted data and pagination info
        return $this->sendResponse($data, 'Business retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'business_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'website' => 'required',
            'description' => 'required',
            'keywords' => 'required',
            'is_approved' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $business = Business::create($input);

        return $this->sendResponse(new BusinessResource($business), 'Business created successfully.');
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $business = Business::find($id);

        if (is_null($business)) {
            return $this->sendError('Business not found.');
        }

        return $this->sendResponse(new BusinessResource($business), 'Business retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'business_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'website' => 'required',
            'description' => 'required',
            'keywords' => 'required',
            'is_approved' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $business->business_name = $input['business_name'];
        $business->address = $input['address'];
        $business->city = $input['city'];
        $business->postal_code = $input['postal_code'];
        $business->phone_number = $input['phone_number'];
        $business->website = $input['website'];
        $business->description = $input['description'];
        $business->keywords = $input['keywords'];
        $business->is_approved = $input['is_approved'];
        $business->save();

        return $this->sendResponse(new BusinessResource($business), 'Business updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Business $business): JsonResponse
    // {
    //     $business->delete();

    //     return $this->sendResponse([], 'Business deleted successfully.');
    // }

    public function edit($id): JsonResponse
    {
        $business = Business::find($id);

        if (is_null($business)) {
            return $this->sendError('Business not found.');
        }

        return $this->sendResponse(new BusinessResource($business), 'Business data retrieved successfully.');
    }






    // public function destroy($id)
    // {
    //     $business = Business::find($id);

    //     if ($business) {
    //         $business->delete(); // Soft deletes the record
    //         return $this->sendResponse(new BusinessResource($business), 'Business deleted successfully.');
    //     }

    //     return $this->sendError('Business not found.');
    // }

    // public function restore($id)
    // {
    //     $business = Business::withTrashed()->find($id);

    //     if ($business) {
    //         $business->restore(); // Restores the soft-deleted record
    //         return $this->sendResponse(new BusinessResource($business), 'Business restored successfully.');
    //     }

    //     return $this->sendError('Business not found.');
    // }

    // public function forceDelete($id)
    // {
    //     $business = Business::withTrashed()->find($id);

    //     if ($business) {
    //         $business->forceDelete(); // Permanently deletes the record
    //         return $this->sendResponse(new BusinessResource($business), 'Business permanently deleted.');
    //     }

    //     return $this->sendError('Business not found.');
    // }

    // public function trashed()
    // {
    //     $trashedBusinesses = Business::onlyTrashed()->get(); // Retrieves only soft-deleted records

    //     return $this->sendResponse($trashedBusinesses, 'Trashed businesses retrieved successfully.');
    // }
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
            
        }
    
        $count = Business::whereIn('id', $ids)->delete(); // Soft deletes the records

        return $this->sendResponse(null, "{$count} businesses deleted successfully.");
    }


    public function restoreMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Business::withTrashed()->whereIn('id', $ids)->restore(); // Restores the soft-deleted records

        return $this->sendResponse(null, "{$count} businesses restored successfully.");
    }
    public function forceDeleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Business::withTrashed()->whereIn('id', $ids)->forceDelete(); // Permanently deletes the records

        return $this->sendResponse(null, "{$count} businesses permanently deleted.");
    }
    public function trashedMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $trashedBusinesses = Business::onlyTrashed()->whereIn('id', $ids)->get(); // Retrieves only specified soft-deleted records

        return $this->sendResponse($trashedBusinesses, 'Trashed businesses retrieved successfully.');
    }


}
