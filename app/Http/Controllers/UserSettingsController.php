<?php

namespace App\Http\Controllers;

use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserSettingsController extends Controller
{
    public function sendResponse($data,$message,$status=200)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response,$status);
    }
    public function sendError($errorData,$message,$status=200)
    {
        $response = [];
        $response['status'] = false;
        $response['message'] = $message;
        if (!empty($errorData)) {
            $response['data'] = $errorData;
        }
        return response()->json($response,$status);
    }

    private function firstOrCreate($type,$data)
    {
        $response = [false,'empty data',0];
        if($data)
        {
            switch($type)
            {
                case 'source':
                    $source_id = '';
                    $source_name = '';
                    if(isset($data['source_id']) && trim($data['source_id']) != ''){
                        $source_id  = $data['source_id'];
                    }
                    if(isset($data['source_name']) && trim($data['source_name']) != ''){
                        $source_name = $data['source_name'];
                    }
                    if($source_id && $source_name){
                        $sid = DB::table('sources')->whereIN('source_id',[$source_id])->whereIN('source_name',[$source_name])->value('id');
                        if($sid == null){
                            // save source
                            $sid = DB::table('sources')->insertGetId(
                                ['source_id' => $source_id, 'source_name' => $source_name]
                            );
                        }
                        if($sid){
                            $response = [true,'success',$sid];
                        }
                    }
                    break;
                case 'category':
                    $cat_key = '';
                    $cat_name = '';
                    if(isset($data['category_key']) && trim($data['category_key']) != ''){
                        $cat_key  = $data['category_key'];
                    }
                    if(isset($data['category_name']) && trim($data['category_name']) != ''){
                        $cat_name = $data['category_name'];
                    }
                    if($cat_key && $cat_name){
                        $catid = DB::table('categories')->whereIN('category_key',[$cat_key])->whereIN('category_name',[$cat_name])->value('id');
                        if($catid == null){
                            // save category
                            $catid = DB::table('categories')->insertGetId(
                                ['category_key' => $cat_key,'category_name' => $cat_name]
                            );
                        }
                        if($catid){
                            $response = [true,'success',$catid];
                        }
                    }
                    break;
                case 'author':
                    $author_name = '';
                    if(isset($data['author']) && trim($data['author']) != ''){
                        $author_name  = $data['author'];
                    }
                    if($author_name){
                        $id = DB::table('authors')->whereIN('author_name',[$author_name])->value('id');
                        if($id == null){
                            // save author
                            $id = DB::table('authors')->insertGetId(
                                ['author_name' => $author_name]
                            );
                        }
                        if($id){
                            $response = [true,'success',$id];
                        }
                    }
                    break;
            }
        }
        return $response;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->only('itemType','itemKey','itemValue');
        $validator = Validator::make($input,[
            'itemType' => 'required|string|max:20',
            'itemKey' => 'required|string|max:100',
            'itemValue' => 'required|string|max:100'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors(), 'Validation Error');
        }

        $mess = 'user not found';
        $userId = auth()->user()->id;
        if($userId)
        {
            $type = $request->itemType;
            $key = $request->itemKey;
            $val = $request->itemValue;
            $data = [];
            $col = '';
            $mess = "$type could not be created";
            if($type == "source"){
                $col = "source_id";
                $data = ['source_id' => $key,'source_name' => $val];
            }
            if($type == "category"){
                $col = "category_id";
                $data = ['category_key' => $key,'category_name' => $val];
            }
            if($type == "author"){
                $col = 'author_id';
                $data = ['author' => $val];
            }
            // get item id
            $resp = $this->firstOrCreate($type,$data);
            if($resp[0] && $resp[1] == "success")
            {
                $itmId = $resp[2];
                $mess = "You have a similar $type";
                $exists = DB::table('user_settings')->whereIN('user_id',[$userId])->whereIn($col,[$itmId])->exists();
                // check item exists
                if(!$exists)
                {
                    $mess = "add $type error";
                    $tnow = Carbon::now();
                    $item = UserSettings::create(['user_id' => $userId,$col => $itmId,'created_at' => $tnow,'updated_at' => $tnow]);
                    if($item){
                        $mess = 'success';
                        $item_id = $item->id;
                        return $this->sendResponse(['item_id' => $item_id], "$type saved successfully");
                    }
                }
            }
        }
        return $this->sendError([], $mess);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserSettings $userSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserSettings $userSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserSettings $userSettings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = UserSettings::find($id);
        if($item)
        {
            if($item->user_id != auth()->user()->id){
                return $this->sendError(null, 'action denied!');
            }
            $item->delete();
            return $this->sendResponse([], 'Success');
        }
        return $this->sendError(null, 'Resource could not be found!');
    }
}
