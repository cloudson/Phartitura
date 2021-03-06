<?php 
error_reporting(E_ALL|E_STRICT);

require __DIR__.'/../vendor/autoload.php';

use Respect\Rest\Router; 
use Respect\Config\Container;
use Cloudson\Phartitura\Controller\PackageController; 
use Cloudson\Phartitura\Routine\Twig as TwigRoutine;
use Cloudson\Phartitura\Routine\Json as JsonRoutine;
use Cloudson\Phartitura\Service\ProjectService;
use Cloudson\Phartitura\Project\Version\VersionDiff;
use Cloudson\Phartitura\Controller;

$c = new Container(__DIR__.'/../config/parameters.ini');

$app = new Router;
$contentNegotiation = [
    'application/json' => new JsonRoutine,
    'text/html' => (new TwigRoutine($c->twig))->addGlobalVar('current_url', $_SERVER['REQUEST_URI']),
];

$app->get('/', function () use ($c) {
    $service = new ProjectService($c->redisAdapter);
    return [
        'latestProjects' => $service->getLatestProjectsList(),
        '_view' => 'home.html',
    ];
})->accept($contentNegotiation);

$badgeCallback = function($user, $package, $version=null) use($c) {
    $caller = Controller\Package::get($c);
    $data = $caller($user, $package, $version);

    $versiondiff = new VersionDiff;
    $dependencies = $data['project']->getDependencies();
    $image = '/images/statusok.png';
    foreach ($dependencies as $dependency) {
        if ($versiondiff($dependency->getLatestVersion(), $dependency->getVersion())) {
            $image = '/images/statusnotok.png';
            break;
        }
    }
    header('Content-type: image/png');

    return readfile(__DIR__.$image);
};
$app->get('/*/*.png', $badgeCallback);
$app->get('/*/*/*.png', $badgeCallback);

$jsonCallback = function($user, $package, $version=null) use ($c){
    $caller =  Controller\Package::get($c);
    $data = $caller($user, $package, $version);
    header('Content-type: application/json');

    return (new JsonRoutine)->__invoke($data);
};

$app->get('/*/*.json', $jsonCallback);
$app->get('/*/*/*.json', $jsonCallback);

$app->get('/*/*/**', Controller\Package::get($c))->accept($contentNegotiation);

print $app->run();