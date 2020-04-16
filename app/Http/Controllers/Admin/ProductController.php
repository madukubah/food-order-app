<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Product;
use App\Model\Image;
use Illuminate\Support\Facades\Validator;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::with('images', 'menu')->paginate(10);//->links();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'menu_id' => 'required',
            'name' => 'required|min:3',
            'description' => 'required|min:3',
            'price' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $entity = Product::createProduct([
            "menu_id" => $request->menu_id,
            "name" => $request->name,
            "description" => $request->description,
            "price" => $request->price,
        ]);
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entity = Product::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $entity->images;
        $entity->menu;
        
        return response()->json( $entity , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'menu_id' => 'required',
            'name' => 'required|min:3',
            'description' => 'required|min:3',
            'price' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $entity = Product::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $entity->menu_id        = $request->menu_id;
        $entity->name           = $request->name;
        $entity->description    = $request->description;
        $entity->price          = $request->price;

        $entity->save();
        return response()->json(['status' => 'success'], 200);
    }

     /**
        *  uploadProfilPict
     **/
    public function addImage( $id, Request $request )
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|max:1024',
            // 'product_id' => 'required',
        ]  );

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::find( $id );
        if( $product == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $fileName = "PRODUCT_".time().".".$request->image->getClientOriginalExtension();
        // return response()->json(['status' => $product ], 200);
        
        if( $request->image->move( Product::PHOTO_PATH, $fileName ) )
        {
            $image = new Image();
            $image->url = $fileName;
            
            $image->save( );

            $product->images()->save($image);
        }
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyImage($id)
    {
        $entity = Image::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);
        try {
            unlink( Product::PHOTO_PATH."/".$entity->url );
        } catch (\Throwable $th) {
            //throw $th;
        }
        $entity->delete();    
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entity = Product::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);
        foreach( $entity->images as $image )
        {
            $this->_destroyImage($image->id);
        }
        $entity->delete();    
        return response()->json(['status' => 'success'], 200);
    }

    private function _destroyImage($id)
    {
        $entity = Image::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);
        try {
            unlink( Product::PHOTO_PATH."/".$entity->url );
        } catch (\Throwable $th) {
            //throw $th;
        }
        $entity->delete();    
    }
}
