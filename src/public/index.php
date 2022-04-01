<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;

use Phalcon\Config\ConfigFactory;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream as ls;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
// use Phalcon\Logger\Adapter\Stream;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
        APP_PATH . "/component/",
    ]
);



$loader->registerNamespaces(
    [

        'app\component' => APP_PATH . '/component',
        'App\Listeners' => APP_PATH . '/listeners'
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);



$eventsManager = new EventsManager();

$container->set(
    'db',
    function () use ($eventsManager) {
        $connection = new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'event',
            ]
        );

        $connection->setEventsManager($eventsManager);
        return $connection;
    }
);

// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );

$container->set(

    'session',
    function () {

        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );
        $session->setAdapter($files);
        $session->start();

        return $session;
    }
);


$container->set(
    'myescaper',
    function () {

        $fileName = '../app/etc/myescaper.php';
        $factory  = new ConfigFactory();

        $myescaper = $factory->newInstance('php', $fileName);
        return $myescaper;
    }
);




$adapter = new ls(APP_PATH . '/log/db.log');

$logger  = new Logger(
    'messages',
    [
        'main' => $adapter,
    ]
);

$container->set(
    'logger',
    $logger
);
$eventsManager->attach(
    'db:afterQuery',
    function (Event $event, $connection) use ($logger) {


        // $logger = $this->logger;
        $logger->error($connection->getSQLStatement());
    }
);

$eventsManager->attach(
    'notification',
    new \App\Listeners\NotificationListeners()
);

$eventsManager->attach(
    'application:beforeHandleRequest',
    new \App\Listeners\NotificationListeners()
);

$container->set(
    'EventsManager',
    $eventsManager
);


$application = new Application($container);

$application->setEventsManager($eventsManager);


try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
