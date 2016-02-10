<?php
namespace FriendsApi\controllers;

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

class RestController extends Controller
{
    CONST SUCCESS_RESPONSE = 'ok';
    CONST FAILED_RESPONSE = 'failed';

    public function setParam($name, $value)
    {
        $this->dispatcher->setParam($name, $value);
    }

    public function returnResult($result, $errorMessage = null)
    {
        $response = new Response();

        $result = [
            'result'        => $result,
            'status'        => $errorMessage === null ? self::SUCCESS_RESPONSE : self::FAILED_RESPONSE,
            'error_message' => (string)$errorMessage
        ];

        $response->setJsonContent($result);

        return $response;
    }
}
