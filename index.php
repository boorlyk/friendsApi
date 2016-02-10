<?php

error_reporting(E_ALL);

define('APP_PATH', realpath('./'));

try {

    /**
     * Read the configuration
     */
    $config = include APP_PATH . "/app/config/config.php";

    /**
     * Read auto-loader
     */
    include APP_PATH . "/app/config/loader.php";

    /**
     * Read services
     */
    include APP_PATH . "/app/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
    $result = [
        'status'        => \FriendsApi\controllers\RestController::FAILED_RESPONSE,
        'result'        => [],
        'error_message' => $e->getMessage()
    ];
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    echo json_encode($result);
}
