<?php

namespace Moux2003\GuzzleBundleSasTokenPlugin\Helper;

use Moux2003\GuzzleBundleSasTokenPlugin\Model\SasTokenConnection;

class ConnectionStringHelper
{
    /**
     * Parse connection string and extracts parameters.
     *
     * @param string $connectionString string containing all informations
     *
     * @return SasTokenConnection|null
     */
    public static function parseConnectionInformations($connectionString)
    {
        $parts = explode(';', $connectionString);

        if (3 != sizeof($parts)) {
            return null;
        }

        $connection = new SasTokenConnection();

        foreach ($parts as $part) {
            if (0 === strpos($part, 'Endpoint')) {
                $connection->setEndpoint('https'.substr($part, 11));
            } elseif (0 === strpos($part, 'SharedAccessKeyName')) {
                $connection->setSasKeyName(substr($part, 20));
            } elseif (0 === strpos($part, 'SharedAccessKey')) {
                $connection->setSasKey(substr($part, 16));
            }
        }

        return $connection->isValid() ? $connection : null;
    }
}
