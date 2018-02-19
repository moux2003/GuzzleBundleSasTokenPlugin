<?php

namespace Moux2003\GuzzleBundleSasTokenPlugin\Middleware;

use Moux2003\GuzzleBundleSasTokenPlugin\Helper\ConnectionStringHelper;
use Moux2003\GuzzleBundleSasTokenPlugin\Model\SasTokenConnection;
use Psr\Http\Message\RequestInterface;

/**
 * Adds SAS Token auth header to request.
 */
class SasTokenAuthMiddleware
{
    /** @var SasTokenConnection|null */
    protected $connection;

    /** @var string */
    protected $expiresInMinutes;

    /**
     * @param string $sasKey
     * @param string $sasKeyName
     * @param string $uri
     */
    public function __construct($connectionString, $expiresInMinutes = 60)
    {
        $this->connection = ConnectionStringHelper::parseConnectionInformations($connectionString);
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * Add WSSE auth headers to Request.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Closure
     */
    public function attach(): \Closure
    {
        return function (callable $handler): \Closure {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (null !== $this->getConnection()) {
                    $targetUri = strtolower(rawurlencode(strtolower($request->getUri())));
                    $expires = time();
                    $expires = $expires + $this->getExpiresInMinutes() * 60;
                    $toSign = $targetUri."\n".$expires;
                    $signature = $this->generateSignature($toSign);

                    $token = [
                        sprintf('SharedAccessSignature sr=%s', $targetUri),
                        sprintf('sig=%s', $signature),
                        sprintf('se=%s', $expires),
                        sprintf('skn=%s', $this->getConnection()->getSasKeyName()),
                    ];

                    $request = $request->withHeader('Authorization', implode('&', $token));
                }

                return $handler($request, $options);
            };
        };
    }

    /**
     * @param string $toSign
     *
     * @return string signature
     */
    protected function generateSignature($toSign): string
    {
        return rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $this->getConnection()->getSasKey(), true)));
    }

    /**
     * @return int
     */
    public function getExpiresInMinutes()
    {
        return $this->expiresInMinutes;
    }

    /**
     * @return SasTokenConnection|null
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
