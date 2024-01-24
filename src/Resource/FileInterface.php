<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter REST Client.
 * https://github.com/jobrouter/php-rest-client
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\RestClient\Resource;

interface FileInterface
{
    /**
     * @return array{path:string, filename:string, contentType?:string}
     * @internal
     */
    public function toArray(): array;
}
