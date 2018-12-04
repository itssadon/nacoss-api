<?php
namespace NACOSS\Helpers;

use NACOSS\Helpers\CasingHelper;

class ResponsePayload {
    protected static $code;
    protected static $message;
    protected static $apiEndpoint;
    protected static $developerMessage;
    protected static $data;

    public static function getPayload($code, $message, $apiEndpoint, $developerMessage) {
        static::$code = $code;
        static::$message = $message;
        static::$apiEndpoint = $apiEndpoint;
        static::$developerMessage = $developerMessage;

        return static::getPayloadArray();
    }

    public static function getDataPayload($data, $index='') {
        $camelCased = [];

        if(is_array($data)){
            foreach ($data as $key => $value) {
                $camelKey = CasingHelper::getCamelCase($key);
                $camelCased[$camelKey] = $value;
            }

            $data = $camelCased;
        }

        static::$data = $data;
        $camelIndex = CasingHelper::getCamelCase($index);

        return static::getPayloadArray($camelIndex);
    }

    private static function isErrorResponse() {
        return static::isClientErrorResponse() || static::isServerErrorResponse();
    }

    private static function isClientErrorResponse() {
        return static::$code >= 400 && static::$code <= 451;
    }

    private static function isServerErrorResponse() {
        return static::$code >= 500 && static::$code <= 511;
    }

    private static function getPayloadArray($index='') {
        if (!is_null(static::$data)) {
            if (empty($index)) {
                $payload = static::$data;
            } else {
                $payload[$index] = static::$data;
            }

        } else {
            $payload = [
                'code' => static::$code,
                'message' => static::$message,
                'endpoint' => static::$apiEndpoint,
            ];

            if (static::isErrorResponse()) {
                $payload['developerMessage'] = static::$developerMessage;
            }
        }

        return $payload;
    }
}
