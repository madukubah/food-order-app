<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Customer;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Customer::with('user')->paginate(10);//->links();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Customer::with('user')->find($id);//->links();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $customer = Customer::find( $id );
        if( $customer == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $user = $customer->user;
        if( $user == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $validationConfig = [
            'name' => 'required|min:3',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
        ];

        if( $request->input('email') != $user->email )
            $validationConfig[ 'email' ] .= '|unique:users';

        if( $request->input('password') != NULL )
        {
            $validationConfig[ 'password' ] = ['required', 'string', 'min:4', 'confirmed'];
        }

        $v = Validator::make($request->all(), $validationConfig);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $user->name     = $request->input('name');
        $user->email    = $request->input('email');
        $user->phone    = $request->input('phone');
        $user->address  = $request->input('address');
        if( $request->input('password') != NULL )
        {
            $user->password  = Hash::make( $request->input('password') );
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            // 'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entity = Customer::find( $id );
        if( $entity == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $entity->delete();    
        return response()->json(['status' => 'success'], 200);
    }
}
