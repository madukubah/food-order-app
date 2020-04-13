<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use App\User;
use App\Model\Customer;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:users',
            'password'  => 'required|min:3|confirmed',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->phone    = $request->phone;
        $user->address  = $request->address;
        $user->password = Hash::make($request->password);
        $user->save();

        $customer = new Customer();
        $customer->code = "CUSTOMER_".time();
        $customer->save();
        
        $customer->user()->save( $user );

        return response()->json(['status' => 'success' ], 200);
    }
    /**
     * Login user and return a token
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json(['status' => 'success', 'token' => $token ], 200)->header('Authorization', $token);
        }
        return response()->json(['error' => 'login_error'], 401);
    }
    /**
     * Logout User
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }
    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $user = Auth::user();//User::find(Auth::user()->id);
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

     /**
     * Get authenticated user
     */
    public function update(Request $request)
    {
        $user = Auth::user();//User::find(Auth::user()->id);
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
            'data' => $user
        ]);
    }

     /**
        *  uploadProfilPict
     **/
    public function uploadProfilPict( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|max:1024',
        ]  );

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        $fileName = "PROFILE_".time().".".$request->photo->getClientOriginalExtension();

        if( $request->photo->move( User::PHOTO_PATH, $fileName ) )
        {
            $user = Auth::user();
            $oldPhoto   = $user->profile_pict;
            if( $oldPhoto != 'default.jpg' ){
                try {
                    unlink( User::PHOTO_PATH."/".$oldPhoto );
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            $user->profile_pict = $fileName;
            $user->save();
        }
        return response()->json(['status' => 'success', 'url' => ''.$fileName ], 200);
    }
    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 401);
    }
    /**
     * Return auth guard
     */
    private function guard()
    {
        return Auth::guard();
    }
}