<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use App\Http\ApiResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Send a generic CRUD response
     *
     * @param string $type
     * @param mixed $resource
     * @param string $primaryKey
     */
    public function sendCrudResponse(string $type, $resource, string $primaryKey = 'id')
    {
        $types = [
            'store' => 'created',
            'show' => 'retrieved',
            'update' => 'updated',
            'destroy' => 'deleted',
        ];

        if ($resource instanceof \App\Http\Resources\ApiResource) {
            $resourceName = Str::humanClassName($resource->resource);
        } else {
            $resourceName = Str::humanClassName($resource);
        }

        $message = sprintf(
            '%s %s successfully %s',
            $resourceName,
            $resource->$primaryKey,
            $types[$type]
        );

        return $this->sendResponse($message, $resource);
    }

    public function sendResponse($message, $data = [], $metadata = [], $code = 200)
    {
        return new ApiResponse($message, true, $data, $metadata, $code);
    }

    public function sendError($error, $data = [], $metadata = [], $code = 404)
    {
        return new ApiResponse($error, false, $data, $metadata, $code);
    }
}
