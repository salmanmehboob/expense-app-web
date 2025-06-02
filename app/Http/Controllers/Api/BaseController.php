<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseNotificationService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param $result
     * @param $message
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = Response::HTTP_NOT_FOUND)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Handle validation errors.
     *
     * @param Request $request
     * @param array $rules
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateRequest($request, $rules, $messages = [])
    {
        return Validator::make($request->all(), $rules, $messages);
    }


//    protected function sendPushNotification(FirebaseService $firebase)
//    {
//        $token = 'DEVICE_TOKEN'; // You should get this from user/device
//        $title = 'Hello!';
//        $body = 'You have a new message.';
//        $data = [
//            'custom_key' => 'custom_value'
//        ];
//
//        $response = $firebase->sendNotification($token, $title, $body, $data);
//
//        return response()->json($response);
//    }


}
