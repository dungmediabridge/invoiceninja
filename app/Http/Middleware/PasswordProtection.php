<?php
/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2019. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://opensource.org/licenses/AAL
 */

namespace App\Http\Middleware;

use App\Libraries\MultiDB;
use App\Models\CompanyToken;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class PasswordProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    public function handle($request, Closure $next)
    {

        $error = [
            'message' => 'Invalid Password',
            'errors' => []
        ];

        if( $request->header('X-API-PASSWORD') ) 
        {

            if(!Hash::check($request->header('X-API-PASSWORD'), auth()->user()->password))
                return response()->json($error, 403);

        }
        elseif (Cache::get(auth()->user()->email."_logged_in")) {
            return $next($request);
        }
        else {

            $error = [
                'message' => 'Access denied',
                'errors' => []
            ];
                return response()->json($error, 412);
            
        }

        Cache::add(auth()->user()->email."_logged_in", 'logged_in', now()->addMinutes(5));

        return $next($request);
    }


}
