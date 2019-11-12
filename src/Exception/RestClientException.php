<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterClient\Exception;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

final class RestClientException extends \RuntimeException
{
    public function __construct(\Throwable $e)
    {
        if ($e instanceof ClientExceptionInterface) {
            // Use message, not the HTML document
            $message = $e->getMessage();
        } elseif ($e instanceof HttpExceptionInterface) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $message = $e->getResponse()->getContent(false);

            $messageArray = \json_decode($message, true);
            if (
                $messageArray !== null
                && isset($messageArray['errors']['-'])
                && \is_array($messageArray['errors']['-'])
            ) {
                $message = \implode(' / ', $messageArray['errors']['-']);
            }
        } else {
            $message = $e->getMessage();
        }

        parent::__construct($message, (int)$e->getCode(), $e);
    }
}
