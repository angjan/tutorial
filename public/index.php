<?php

use Phalcon\Loader;
use Phalcon\Tag;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt;

try {

    // Register an autoloader
    $loader = new Loader();
    $loader->registerDirs(
        array(
            '../app/controllers/',
            '../app/models/'
        )
    )->register();

    // Create a DI
    $di = new FactoryDefault();

    // Set the database service
    $di['db'] = function() {
        return new DbAdapter(array(
            "host"     => "localhost",
            "username" => "root",
            "password" => "secret",
            "dbname"   => "tutorial"
        ));
    };


    $di->set(
        'voltService',
        function ($view, $di) {
            $volt = new Volt($view, $di);

            $volt->setOptions(
                [
                    'compiledPath'      => '../app/compiled-templates/',
                    'compiledExtension' => '.compiled',
                ]
            );

            return $volt;
        }
    );

    // Register Volt as template engine
    $di->set(
        'view',
        function () {
            $view = new View();

            $view->setViewsDir('../app/views/');

            $view->registerEngines(
                [
                    '.volt' => 'voltService',
                ]
            );

            return $view;
        }
    );

    // Setup a base URI so that all generated URIs include the "tutorial" folder
    $di['url'] = function() {
        $url = new Url();
        $url->setBaseUri('/tutorial/');
        return $url;
    };

    // Setup the tag helpers
    $di['tag'] = function() {
        return new Tag();
    };

    // Handle the request
    $application = new Application($di);

    echo $application->handle()->getContent();

} catch (Exception $e) {
     echo "Exception: ", $e->getMessage();
}
