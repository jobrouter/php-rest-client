<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Client.
 *
 * Copyright (c) 2019-2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 *
 * @see https://github.com/brotkrueml/jobrouter-client
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
     */
    private $routes = [
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
        'PATCH' => [
            'application/sessions' => '',
            // @deprecated, only available in JobRouter 4.2
            'application/sessions/.+?' => '',
        ],
        'POST' => [
            'application/dialogelement/steps/.+?/sqltables/.+?/rows' => '',
            'application/dialogelement/steps/.+?/userslists/.+?/options' => '',
            'application/documenthub' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/fileuploads' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/incidents/.+?' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/jobarchive/archives/.+?/documents' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/jobarchive/archives/.+?/documents/.+?' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
            'application/jobarchive/archives/.+?/documents/.+?/clippedfiles' => self::CONTENT_TYPE_MULTIPART_FORMDATA,
        ],
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
