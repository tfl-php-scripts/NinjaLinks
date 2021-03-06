<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/public/ninjalinks',
    ]);

    $parameters->set(Option::AUTOLOAD_PATHS, [
        __DIR__ . '/public/ninjalinks/inc/RobotessNet/Autoloader.php',
        __DIR__ . '/public/ninjalinks/inc/RobotessNet/Singleton.php',
        __DIR__ . '/public/ninjalinks/inc/RobotessNet/App.php',
        __DIR__ . '/public/ninjalinks/inc/RobotessNet/StringUtils.php',
        __DIR__ . '/public/ninjalinks/functions.php',
    ]);

    $parameters->set(Option::SKIP, [
        __DIR__ . '/public/ninjalinks/config.sample.php',
        __DIR__ . '/public/ninjalinks/config.php',
        __DIR__ . '/public/njicons',
    ]);

    $parameters->set(Option::SETS, [
        SetList::PHP_74,
    ]);
};