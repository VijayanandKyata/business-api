<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Response;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            // return $this->sendError('Validation Error.', $validator->errors());   
            return response()->json([
                'success' => true,
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ]);    
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
   
        // return $this->sendResponse($success, 'User register successfully.');
        return response()->json([
            'success' => true,
            'data' => $success,
            'message' => 'User registered successfully.',
        ]);
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            // return $this->sendResponse($success, 'User login successfully.');

            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'User login successfully.',
            ]);
        } 
        else{ 
            // return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            return response()->json([
                'success' => true,
                'data' => 'Unauthorised',
                'message' => 'User login Failed.',
            ]);
        } 
    }
}