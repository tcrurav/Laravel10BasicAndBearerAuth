<?php
       
namespace App\Http\Controllers\API;
       
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Bicycle;
use Validator;
use App\Http\Resources\BicycleResource;
use Illuminate\Http\JsonResponse;
       
class BicycleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $bicycles = Bicycle::all();
        
        return $this->sendResponse(BicycleResource::collection($bicycles), 'Bicycles retrieved successfully.');
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
            'brand' => 'required',
            'model' => 'required'
        ]);
       
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
       
        $bicycle = Bicycle::create($input);
       
        return $this->sendResponse(new BicycleResource($bicycle), 'Bicycle created successfully.');
    } 
     
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $bicycle = Bicycle::find($id);
      
        if (is_null($bicycle)) {
            return $this->sendError('Bicycle not found.');
        }
       
        return $this->sendResponse(new BicycleResource($bicycle), 'Bicycle retrieved successfully.');
    }
      
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bicycle $bicycle): JsonResponse
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'brand' => 'required',
            'model' => 'required'
        ]);
       
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
       
        $bicycle->brand = $input['brand'];
        $bicycle->model = $input['model'];
        $bicycle->save();
       
        return $this->sendResponse(new BicycleResource($bicycle), 'Bicycle updated successfully.');
    }
     
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bicycle $bicycle): JsonResponse
    {
        $bicycle->delete();
       
        return $this->sendResponse([], 'Bicycle deleted successfully.');
    }
}