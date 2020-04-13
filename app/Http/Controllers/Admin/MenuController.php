<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Menu;
use Illuminate\Support\Facades\Validator;


class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus =  Menu::where("menu_id", NULL )->with('menus')->get();
        // $menus =  Menu::where("menu_id", NULL )->with('products', 'menus')->get();
        foreach( $menus as $menu )
        {
            foreach( $menu->menus as $m )
            {
                $m->menus;
            }   
        }
        return $menus;
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
        $validationConfig = [
            'name' => 'required|min:3',
        ];

        if( $request->input('menu_id') != NULL )
        {
            $validationConfig[ 'menu_id' ] = 'required|numeric';
        }

        $v = Validator::make($request->all(), $validationConfig);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $entity = Menu::createMenu([
            "name"   => $request->name,
        ]);
        $entity->menu_id = $request->menu_id;
        $entity->save();
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
        //
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
            'name' => 'required|min:3',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $entity = Menu::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $entity->update([
            "name" => $request->name
        ]);
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
        $entity = Menu::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);
        $this->deleteBranch( $entity->menus );
        $entity->delete();    
        return response()->json(['status' => 'success'], 200);
    }

    private function deleteBranch( $menus ){

        foreach( $menus as $menu )
        {
            if(  count( $menu->menus ) > 0 )
            {
                $this->deleteBranch( $menu->menus );
            }  
            else
                $menu->delete();
        }
    }
}
