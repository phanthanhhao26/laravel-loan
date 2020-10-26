<?php

namespace App\Http;

use Response;
use Illuminate\Http\JsonResponse;

/**
 * Class: ApiResponse
 *
 * This extends the default laravel JsonResponse with our own custom data
 * structure.
 *
 * All responses from our API are returned in this standard format.
 *
 * @see JsonResponse
 */
class ApiResponse extends JsonResponse
{
    /**
     * Constructor.
     *
     * @param str   $message    Human readable message
     * @param bool  $apiSuccess Was the API request successful
     * @param array $data       Response data
     * @param array $data       Response metadata (pagination etc)
     * @param int   $status     HTTP status code
     * @param array $headers    HTTP headers
     * @param int   $options    Options
     *
     * @return void
     */
    public function __construct($message, $apiSuccess, $data = [], $metadata = [], $status = 200, $headers = [], $options = 0)
    {
        $data = [
            'success' => $apiSuccess,
            'message' => $message,
            'data' => $data,
        ];

        if ($metadata !== []) {
            $data['metadata'] = $metadata;
        }

        parent::__construct($data, $status, $headers, $options);
    }
}
