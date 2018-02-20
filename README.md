# GuzzleBundleSasTokenPlugin

[![Latest Version](https://img.shields.io/github/release/moux2003/GuzzleBundleSasTokenPlugin.svg?style=flat-square)](https://github.com/moux2003/GuzzleBundleSasTokenPlugin/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

This plugin integrates [SAS Token][1] (Azure) functionality into Guzzle Bundle, a bundle for building RESTful web service clients.

## Requirements
 - [Guzzle Bundle][2]

 
### Installation
Using [composer][3]:

##### composer.json
``` json
{
    "require": {
        "moux2003/guzzle-bundle-sastoken-plugin": "~1.0"
    }
}
```

##### command line
``` bash
$ composer require moux2003/guzzle-bundle-sastoken-plugin
```

## Usage
### Enable bundle
``` php
# app/AppKernel.php

new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Moux2003\GuzzleBundleSasTokenPlugin\GuzzleBundleSasTokenPlugin(),
])
```

### Basic configuration
``` yaml
# app/config/config.yml

eight_points_guzzle:
    clients:
        api_azure:
            base_url: "https://namespace.windows.net"

            # define headers, options

            # plugin settings
            plugin:
                sasToken:
                    connectionString: "Endpoint=scheme://namespace.windows.net/;SharedAccessKeyName=DefaultFullSharedAccessSignature;SharedAccessKey=HaShEdKey"
                    expires: 60 # optional
```

## Usage with guzzle
``` php
<?php 

$sasToken = new \Moux2003\GuzzleBundleSasTokenPlugin\Middleware\SasTokenAuthMiddleware($connectionString);

$stack = \GuzzleHttp\HandlerStack::create();

// Add the sasToken middleware to the handler stack.
$stack->push($sasToken->attach());

$client   = new \GuzzleHttp\Client(['handler' => $stack]);
$response = $client->post('https://namespace.windows.net/hubname/messages?api-version=2013-10');
```

## License
This middleware is licensed under the MIT License - see the LICENSE file for details

[1]: https://docs.microsoft.com/en-us/azure/storage/common/storage-dotnet-shared-access-signature-part-1
[2]: https://github.com/8p/EightPointsGuzzleBundle
[3]: https://getcomposer.org/
