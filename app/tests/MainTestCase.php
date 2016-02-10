<?php
namespace FriendsApi\tests;


use FriendsApi\controllers\RestController;
use Phalcon\Di;
use Phalcon\Exception;
use Phalcon\Http\Request;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url;
use Phalcon\Test\UnitTestCase;

abstract class MainTestCase extends UnitTestCase
{

    static $crossTestParams = [];

    protected function setCrossTestParam($paramName, $paramValue)
    {
        self::$crossTestParams[$paramName] = $paramValue;
    }

    protected function getCrossTestParam($paramName)
    {
        if (array_key_exists($paramName, self::$crossTestParams)) {
            return self::$crossTestParams[$paramName];
        }
        return null;
    }

    public function setUp()
    {
        $di = Di::getDefault();

        $this->setDI($di);
    }

    public function tearDown()
    {

    }

    protected function load($uri, $method = "GET", $params = [])
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;

        switch ($method) {
            case "GET":
                $_GET = $params;
                $_REQUEST = array_merge($_REQUEST, $_GET);
                break;
            case "POST":
                $_POST = $params;
                $_REQUEST = array_merge($_REQUEST, $_POST);
                break;
            case "PUT":
                $_PUT = $params;
                $_REQUEST = array_merge($_REQUEST, $_PUT);
                break;
            default:
                $_REQUEST = $params;
                break;
        }
        foreach ($params as $paramkey => $paramValue) {
            $this->getDi()->get('dispatcher')->setParam($paramkey, $paramValue);
        }

        $application = new Application($this->getDI());


        $_GET['_url'] = $uri;

        $response = $application->handle();

        return json_decode($response->getContent(), true);
    }

    protected function assertSuccessResponse($response)
    {
        $this->assertArrayHasKey('result', $response, 'Result is not found in response');
        $this->assertArrayHasKey('status', $response, 'Status not found in response');
        $this->assertArrayHasKey('error_message', $response, 'Error message not found in response');
        $this->assertEquals(RestController::SUCCESS_RESPONSE, $response['status'], 'Response is not ok');
        $this->assertEmpty($response['error_message'], 'Response contains error: ' . $response['error_message']);
    }
}