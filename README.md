# MicroserviceBundle

This bundle contains the base for the services at Vocento and how the versioning
and other resources of the service should work.

## Installation

To install the bundle, include the package as a requirement in your `composer.json` file.

```
composer require "vocento/microservice-bundle"
```

Once the bundle is installed, you have follow this steps:

### Activate the Bundle

Add the bundle to the `AppKernel`

```php
// app/AppKernel.php
<?php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Vocento\MicroserviceBundle\MicroserviceBundle(),
        ];

        return $bundles;
    }
    
    // ...
}
```

### Configure the Bundle

Add the bundle configuration to your `config.yml` file

```yaml
# app/config/config.yml

microservice:
    name: 'your-microservice-name'
    versions:
        list:
            - 'v1'
            - 'v2'
            - 'v3.1'
            - 'v3.1.4'
        curent: 'v1' # default is latests
```

### Configure the Bundle routes

Add the bundle routing configuration to your `routing` file
 
```yaml
# app/config/routing.yml

microservice:
    resource: "@MicroserviceBundle/Resources/config/routing/base.yml"

# ...
``` 

This configuration will expose three endpoints related with the service in order
to enable a way to auto-discover the service, the available versions and the current
version.

#### Service name endpoint

Request
`GET /service/name.json`

Response 
```json
{
    "name": "service-name"
}
```

#### Service versions endpoint

Request
`GET /service/versions.json`

Response 
```json
{
    "versions": [
        "v1",
        "v2"
    ],
    "current": "v2"
}
```

#### Service current version endpoint

Request
`GET /service/versions/current.json`

Response 
```json
{
    "version": "v2"
}
```

### Convert a Bundle as a MicroserviceBundle 

To declare a new Bundle as a Microservice Bundle, you have to extend the class
`Vocento\Vocento\MicroserviceBundle\AbstractMicroserviceBundle`.

```php
<?php

// src/Vocento/AppBundle/AppBundle.php

namespace AppBundle;

use Vocento\MicroserviceBundle\AbstractMicroserviceBundle;

class AppBundle extends AbstractMicroserviceBundle 
{
    /**
     * @inheritDoc
     */
    public function getName() {
        return 'AppBundle';
    }
    
    // ...
}
```

If you want to add an extension to your bundle, you have to add the method `getContainerExtension`
and return an instance of your extension.

```php
<?php

// src/Vocento/AppBundle/AppBundle.php

namespace AppBundle;

use AppBundle\DpendencyInjection\MyBundleExtension;
use Vocento\MicroserviceBundle\AbstractMicroserviceBundle;

class AppBundle extends AbstractMicroserviceBundle 
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension() {
        return new MyBundleExtension();
    }
    
    // ...
}
```

