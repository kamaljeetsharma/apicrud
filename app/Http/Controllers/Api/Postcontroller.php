<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class Postcontroller extends Controller
{
    public function index(){

$data['posts']=Post::all();
return response()->json([
'status'=>true,
'message'=>'all Posts data',
'data'=>$data,

],200);
    }

    public function store(Request $request){
        $validateuser=Validator::make($request->all(),
        [
            'title'=>'required',
            'description'=>'required',
            'image'=>'required|mimes:,jpeg,gif',
        ]);
        
        if($validateuser->fails()){
            return response()->json(
                [
                'status'=>false,
                'message'=>'validation error',
                'errors'=>$validateuser->errors()->all()
            ],401);
        }
    
        $img=$request->image;
        $ext=$img->getClientoriginalExtension();
        $imageName=time().'.'.$ext;
        $img->move(public_path().'/uploads',$imageName);

        $Post=Post::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$imageName,
        ]);
        return response()->json([
            'status'=>true,
            'message'=>'post created succesfully',
            'Post'=>$Post,
            ],200);
            
   
   
        }
        public function show(string $id){
            $data['post']=Post::select(
                'id',
                'title',
                'description',
                'image'

            )->where(['id'=>$id])->get();

            return response()->json([
                'status'=>true,
                'message'=>'your single post',
                'data'=>$data,
                ],200);
                
        }


public function update(Request $request,string $id){

    $validateuser=Validator::make($request->all(),
    [
        'title'=>'required',
        'description'=>'required',
        'image'=>'required|mimes:,jpeg,gif',
    ]);
    
    if($validateuser->fails()){
        return response()->json(
            [
            'status'=>false,
            'message'=>'validation error',
            'errors'=>$validateuser->errors()->all()
        ],401);
    }

$postImage=Post::select('id','image')
->where(['id'=>$id])->get();

if($request->image !='')

   {

    $path=public_path().'/uploads';
    if($postImage[0]->image !=''&&$postImage[0]->image !=null)
    {
        $old_file=$path.$postImage[0]->image;
        if(file_exists($old_file)){
            unlink($old_file);
        
    }


 }

    $img=$request->image;
    $ext=$img->getClientoriginalExtension();
    $imageName=time().'.'.$ext;
    $img->move(public_path().'/uploads/',$imageName);

}else{

    $imageName=$postImage->image;

     }


    $Post=Post::where(['id'=>$id])->update
    ([
        'title'=>$request->title,
        'description'=>$request->description,
        'image'=>$imageName,
    ]);


    return response()->json
       ([

             'status'=>true,
             'message'=>'post updated succesfully',
             'Post'=>$Post,

        ],200);
}




public function destroy(string $id)

{

$imagePath=Post::select('image')->where('id',$id)->get();
$filepath=public_path().'/uploads/'.$imagePath[0]['image'];

unlink($filepath);
$Post=Post::where('id',$id)->delete();
return response()->json
        ([  

               'status'=>true,
                'message'=>'your Post has been removed.',
                'Post'=>$Post,
            ],200);

        }


}
