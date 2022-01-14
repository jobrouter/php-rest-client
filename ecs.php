<?php

declare (strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $header = <<<HEADER
This file is part of the JobRouter Client.
https://github.com/brotkrueml/jobrouter-client

Copyright (c) 2019-2022 Chris Müller

For the full copyright and license information, please view the
LICENSE.txt file that was distributed with this source code.
HEADER;

    $containerConfigurator->import(__DIR__ . '/vendor/brotkrueml/coding-standards/config/common.php');

    $services = $containerConfigurator->services();
    $services->set(\PhpCsFixer\Fixer\Comment\HeaderCommentFixer::class)
        ->call('configure', [[
            'comment_type' => 'comment',
            'header' => $header,
            'separate' => 'both',
        ]]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    );

    $parameters->set(Option::SKIP, [
        \PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class . '.FoundInWhileCondition' => [
            __DIR__ . '/tests/Unit/Mapper/RouteContentTypeMapperTest.php',
        ]
    ]);
};
