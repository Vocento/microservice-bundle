# MicroserviceBundle

This bundle contains the base for the services at Vocento and how the versioning
and other resources of the service should work.

## Installation

To install the bundle, include the package as a requirement in your `composer.json` file.

```bash
composer require vocento/microservice-bundle
```

Once the bundle is installed, you have to configure it.

## Configure the Bundle

Add the bundle to the `config/bundles.php` file.

```php
<?php

return [
    // ...
    Vocento\MicroserviceBundle\MicroserviceBundle::class => ['all' => true],
];

```

Add the bundle configuration to `config/packages/microservice.yaml` file

```yaml
microservice:
    name: 'microservice-name'
    debug: '%kernel.debug%'
    manage_exceptions: true
    versions:
        list:
            - 'v1'
            - 'v2'
            - 'v3.1'
            - 'v3.1.4'
        current: 'v2'
```

Add the bundle routing configuration to `config/routes/microservice.yaml` file
 
```yaml
microservice:
    resource: "@MicroserviceBundle/Resources/config/routing/base.yml"
```

This configuration will expose three endpoints related with the service in order
to enable a way to auto-discover the service, the available versions and the current
version.

## Service endpoints

Request `GET /service`

```json
{
    "name": "microservice-name",
    "current": "v2",
    "versions": [
        "v1",
        "v2",
        "v3.1",
        "v3.1.4"
    ]
}
```

Request `GET /service/name.json`

```json
{
    "name": "microservice-name"
}
```

Request
`GET /service/versions.json`

```json
{
    "current": "v2",
    "versions": [
        "v1",
        "v2",
        "v3.1",
        "v3.1.4"
    ]
}
```

Request `GET /service/versions/current.json`

```json
{
    "version": "v2"
}
```
