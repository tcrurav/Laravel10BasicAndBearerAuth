<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use \Illuminate\Support\Str;
     
class RegisterController extends BaseController
{
    
    private function extractBasicAuthData(Request $request)
    {
        $header = $request->header('Authorization');
        if(!Str::startsWith($header, 'Basic ')){
            return null;
        }

        $encoded = explode('Basic ', $header);
        $decoded = base64_decode($encoded[1]);
        list($email, $password) = explode(":", $decoded);

        $dataDecoded = $request->all(); //only name should be there
        $dataDecoded['email'] = $email;
        $dataDecoded['password'] = $password;

        return $dataDecoded;
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $dataDecoded = $this->extractBasicAuthData($request);

        if($dataDecoded == null) return $this->sendError('Not valid Basic Auth');

        $validator = Validator::make($dataDecoded, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $input = $dataDecoded;
        $input['password'] = bcrypt($input['password']);

        try {
            $user = User::create($input);
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'User register successfully.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return $this->sendError('User Register Error.', $exception->errorInfo);
        }
    }
     
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        $dataDecoded = $this->extractBasicAuthData($request);

        if($dataDecoded == null) return $this->sendError('Not valid Basic Auth');

        if(Auth::attempt(['email' => $dataDecoded['email'], 'password' => $dataDecoded['password']])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}