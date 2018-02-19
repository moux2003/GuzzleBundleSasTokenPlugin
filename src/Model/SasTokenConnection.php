<?php

namespace Moux2003\GuzzleBundleSasTokenPlugin\Model;

class SasTokenConnection
{
    /** @var @var string */
    protected $sasKey;
    /** @var @var string */
    protected $sasKeyName;
    /** @var @var string */
    protected $endpoint;

    public function __construct($sasKey = null, $sasKeyName = null, $endpoint = null)
    {
        $this->sasKey = $sasKey;
        $this->sasKeyName = $sasKeyName;
        $this->endpoint = $endpoint;
    }

    public function isValid()
    {
        return (null !== $this->getSasKey()) && (null !== $this->getSasKeyName()) && (null !== $this->getEndpoint());
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
     * @param string $sasKey
     *
     * @return self
     */
    public function setSasKey($sasKey)
    {
        $this->sasKey = $sasKey;

        return $this;
    }

    /**
     * @param string $sasKeyName
     *
     * @return self
     */
    public function setSasKeyName($sasKeyName)
    {
        $this->sasKeyName = $sasKeyName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param mixed $endpoint
     *
     * @return self
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
