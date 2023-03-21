<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Client;

use Illuminate\support\Facades\Auth;
class UserController extends Controller
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['user' => $user], 200);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (!Auth::attempt($data)) {
            return response(['message' => 'invalid Login information']);
        }
        if (Auth::attempt($data)) {
            $token = auth()
                ->user()
                ->createToken('BlogApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
    public function getTokenAndRefreshToken(OClient $oClient, $email, $password)
    {
        $oClient = OClient::where('password_client', 1)->first();
        dd($oClient);
        $http = new Client();
        $response = $http->request('POST', 'http://mylemp-nginx/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $email,
                'password' => $password,
                'scope' => '*',
            ],
        ]);
        $result = json_decode((string) $response->getBody(), true);
        return response()->json($result, $this->successStatus);
    }
}
