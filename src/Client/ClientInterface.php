<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterClient\Client;

use Brotkrueml\JobRouterClient\Model\Incident;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    public function authenticate(): void;

    /**
     * @param string $method
     * @param string $resource
     * @param array|Incident $data
     * @return ResponseInterface
     */
    public function request(string $method, string $resource, $data = []): ResponseInterface;
}
