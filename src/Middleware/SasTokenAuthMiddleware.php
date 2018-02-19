<?php

namespace Moux2003\GuzzleBundleSasTokenPlugin\Middleware;

use Psr\Http\Message\RequestInterface;

/**
 * Adds SAS Token auth header to request.
 */
class SasTokenAuthMiddleware
{
    /** @var string */
    protected $sasKey;

    /** @var string */
    protected $sasKeyName;

    /** @var string */
    protected $uri;

    /** @var string */
    protected $expiresInMinutes;

    /**
     * @param string $sasKey
     * @param string $sasKeyName
     * @param string $uri
     */
    public function __construct($sasKey, $sasKeyName, $uri, $expiresInMinutes = 60)
    {
        $this->sasKey = $sasKey;
        $this->sasKeyName = $sasKeyName;
        $this->uri = $uri;
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
                $targetUri = strtolower(rawurlencode(strtolower($this->getUri())));
                $expires = time();
                $expires = $expires + $this->getExpiresInMinutes() * 60;
                $toSign = $targetUri."\n".$expires;
                $signature = $this->generateSignature($toSign);

                $token = [
                    sprintf('SharedAccessSignature sr=%s', $targetUri),
                    sprintf('sig=%s', $signature),
                    sprintf('se=%s', $expires),
                    sprintf('skn=%s', $this->getSasKeyName()),
                ];

                $request = $request->withHeader('Authorization', implode('&', $token));

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
        return rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $this->getSasKey(), true)));
    }

    /**
     * @return string
     */
    public function getSasKey()
    {
        return $this->sasKey;
    }

    /**
     * @return string
     */
    public function getSasKeyName()
    {
        return $this->sasKeyName;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return mixed
     */
    public function getExpiresInMinutes()
    {
        return $this->expiresInMinutes;
    }
}
