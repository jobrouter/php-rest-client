<?php

declare (strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $header = <<<HEADER
This file is part of the JobRouter Client.
https://github.com/brotkrueml/jobrouter-client

Copyright (c) 2019-2023 Chris Müller

For the full copyright and license information, please view the
LICENSE.txt file that was distributed with this source code.
HEADER;

    $config->import(__DIR__ . '/vendor/brotkrueml/coding-standards/config/common.php');

    $config->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $config->ruleWithConfiguration(\PhpCsFixer\Fixer\Comment\HeaderCommentFixer::class, [
        'comment_type' => 'comment',
        'header' => $header,
        'separate' => 'both',
    ]);
};
