<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function sendResponseApi($params) {
        $params = array_merge([
            'code' => 200,
            'data' => null,
            'error' => null,
            'paginate' => null,
            'message' => null
        ], $params);

        $arrMessage = [
            '200' => 'Success',
            '400' => 'Invalid Parameters',
            '401' => 'Unauthorize',
            '403' => 'Forbidden',
            '404' => 'Page Not Found',
            '429' => 'Too Many Attempts',
            '500' => 'Internal Server Error'
        ];

        $return = [
            'status' => $params['code'],
            'statusMessage' => $arrMessage[$params['code']]
        ];

        if (!empty($params['message'])) {
            $return['message'] = $params['message'];
        }

        if (!empty($params['data'])) {
            $return['data'] = $params['data'];
        }

        if (!empty($params['error'])) {
            $return['error'] = $params['error'];
        }

        if (!empty($params['paginate'])) {
            $return['paginate'] = $params['paginate'];
        }

        return response()->json($return, $params['code']);
    }
}
