<?php
namespace App\Http\Middleware;
use Closure;
use App\Model\Cuenta;
class AuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next, $guard = null)
    {
        $token=$request->header('Authorization');
        $user=Cuenta::where('api_token',$token)->first();
        if ($user==null) {
            return response()->json(["status"=>"ERROR","data"=>"Token no existe"]);
        }
        $request->merge(['user_id'=>$user->id]);
        session(['cuenta_id' => $user->id]);
        return $next($request);
    }
}