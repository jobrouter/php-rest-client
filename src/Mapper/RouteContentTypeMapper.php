<?php

declare(strict_types=1);

/*
 * This file is part of the JobRouter Client.
 * https://github.com/brotkrueml/jobrouter-client
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterClient\Mapper;

/**
 * @internal
 */
final class RouteContentTypeMapper
{
    private const CONTENT_TYPE_APPLICATION_JSON = 'application/json';
    private const CONTENT_TYPE_MULTIPART_FORMDATA = 'multipart/form-data';

    /**
     * @var array<string, array<string, string>>
     * @phpstan-ignore-next-line
     */
    private $routes = [
        // @phpstan-ignore-next-line
        'DELETE' => [
            'application/dashboards/.+?' => '',
            'application/documenthub/.+?' => '',
            'application/fileuploads/.+?' => '',
            'application/jobarchive/archives/.+?/documents/.+?' => '',
            'application/sessions' => '',
            'application/sessions/.+?' => '',
            'application/steps/.+?/lock' => '',
            // @deprecated, only available in JobRouter 4.2
            'configuration/sessions/.+?' => '',
        ],
        // @phpstan-ignore-next-line
        'PATCH' => [
            'application/sessions' => '',
            // @deprecated, only available in JobRouter 4.2
            'application/sessions/.+?' => '',
        ],
        // @phpstan-ignore-next-line
        'POST' => [
            'application/documenthub' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/fileuploads' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/incidents/.+?' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/jobarchive/archives/.+?/documents' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/jobarchive/archives/.+?/documents/.+?' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/jobarchive/archives/.+?/documents/.+?/clippedfiles' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
        ],
        // @phpstan-ignore-next-line
        'PUT' => [
            'application/dashboards/.+?' => '',
            'application/incidents/.+?' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            // @deprecated, only available in JobRouter 4.2
            'application/sessions/.+?' => '',
        ],
    ];

    public function getRequestContentTypeForRoute(string $method, string $resource): string
    {
        if ($method === 'GET') {
            return '';
        }

        foreach ($this->routes[$method] ?? [] as $resourceToMatch => $contentType) {
            $pattern = \sprintf('#^%s$#', $resourceToMatch);

            if (\preg_match($pattern, $resource)) {
                return $contentType;
            }
        }

        return self::CONTENT_TYPE_APPLICATION_JSON;
    }
}
