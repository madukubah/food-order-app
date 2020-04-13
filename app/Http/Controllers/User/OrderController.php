<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Model\Order;
use App\Model\Product;
use App\Model\OrderDetail;
use App\Model\Payment;
use Auth;
use App\Model\Image;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = Auth::user()->userable;
        $payment = Payment::where('customer_id', $customer->id )->with('trf_img', 'order' )->paginate(10);//->links();
        foreach( $payment as $p )
        {
            $p->order->orderDetails;
        }
        return $payment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrders( $status = "ALL" )
    {
        if( $status != "ALL" )
            $payment = Payment::where('status', $status )->with('trf_img', 'order', 'customer' )->paginate(10);//->links();
        else
            $payment = Payment::with('trf_img', 'order', 'customer' )->paginate(10);//->links();

        foreach( $payment as $p )
        {
            $p->customer->user;
            $p->order->orderDetails;
        }
        return $payment;
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
        $validatorConfig = [
            // order
            'customer_id'   => 'required',
            'phone_ref'     => 'required',
            'address'       => 'required',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            // payment
            'payment_method'   => 'required|numeric',
            // item
            'product_id'    => 'required|array',
            'product_id.*'  => 'required|numeric',
        ];
        if(  $request->get('product_id') != NULL )
        {
            $validatorConfig["quantity"]= 'required|array|min:'.count( $request->get('product_id') );
            $validatorConfig["quantity.*"]= 'required|numeric';

            $validatorConfig["note"]= 'required|array|min:'.count( $request->get('product_id') );
            $validatorConfig["note.*"]= 'required|string';
        }
        
        $v = Validator::make($request->all(), $validatorConfig );
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        // create order
        $order = Order::createOrder( [
            'customer_id'   => $request->customer_id,
            'phone_ref'     => $request->phone_ref,
            'address'       => $request->address,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
        ] );

        $_orderDetails = [];
        $total =0;
        foreach( $request->get('product_id') as $ind => $p )
        {
            $product = Product::find( $p );
            if( $product == NULL )
                return response()->json([
                    'status' => 'Record not found'
                ], 404);
            
            $detail = new OrderDetail();
            $detail->product_id     = $product->id;
            $detail->product_name   = $product->name;
            $detail->product_price  = $product->price;
            $detail->quantity       = $request->quantity[ $ind ];
            $detail->note           = $request->note[ $ind ] ;

            $detail->order()->associate($order->id);
            $detail->save();

            $total += ( $product->price * $request->quantity[ $ind ] );
        }
        // create payment
        $payment = Payment::createPayment([
            'payment_method' => $request->payment_method,
            'order_id' => $order->id,
            'customer_id' => $request->customer_id,
            'total' => $total,
            'status' => "SUSPEND",
        ]);
        
        return response()->json(['status' => "success" ], 200);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmOrder($id, Request $request)
    {
        $payment = Payment::find( $id );
        if( $payment == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);
        $payment->status = "FUNDED";
        $payment->save();
        return response()->json(['status' => "success" ], 200);
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelOrder($id, Request $request)
    {
        $payment = Payment::find( $id );
        if( $payment == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);
        $payment->status = "CANCELLED";
        $payment->save();
        return response()->json(['status' => "success" ], 200);
    }

    /**
        *  uploadProfilPict
     **/
    public function addTrfImage( $id, Request $request )
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|max:1024',
        ]  );

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = Payment::find( $id );
        if( $payment == NULL )
            return response()->json([
                'status' => 'Record not found'
            ], 404);

        $fileName = "PAYMENT_".time().".".$request->image->getClientOriginalExtension();
        
        if( $request->image->move( Payment::PHOTO_PATH, $fileName ) )
        {
            $image = new Image();
            $image->url = $fileName;
            
            $image->save( );

            $payment->trf_img()->save($image);
        }
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
        //
    }
}
