## Compatibility and upgrading

This version targets PHP 8.2 or later, Symfony 7.4 LTS, Doctrine ORM 3 and DBAL 4.
The optional API Platform integration supports API Platform 4.4:

```shell
composer require drinks-it/rule-engine api-platform/symfony:^4.4 --with-all-dependencies
```

This is a breaking upgrade for applications using Symfony 6, API Platform 3,
Doctrine ORM 2 or DBAL 3. Upgrade those dependencies together. Existing rule
payloads and custom Doctrine type names remain unchanged. Review generated
Doctrine migrations after the DBAL upgrade, especially legacy SQL comment hints.

Use PHP attributes for rule resource and property mappings on Symfony 7.4.
Legacy docblock mappings are no longer supported. Convert them to PHP attributes
(see [mapping examples](docs/mark_entity_resources.md)).

For development, run `composer install`, `composer test-unit` and `composer php-cs`.
The test suite includes a Symfony kernel with API Platform, schema generation,
serializer dispatch and SQLite round trips for the custom Doctrine types.

## Install

```shell
composer require drinks-it/rule-engine
```

#### depends
```shell
composer require drinks-it/rule-engine --with-dependencies 
```

## Bundle add

`config/bundles.php`

```php
<?php

return [
    // ... another bundles
    DrinksIt\RuleEngineBundle\RuleEngineBundle::class => ['all' => true]
];
```

# rule-engine configuration

```yaml
doctrine:
    dbal:
        types:
            rule-engine-conditions: DrinksIt\RuleEngineBundle\Doctrine\Types\ConditionsType
            rule-engine-action: DrinksIt\RuleEngineBundle\Doctrine\Types\ActionType
            rule-engine-event: DrinksIt\RuleEngineBundle\Doctrine\Types\TriggerEventType
```

### make entity

```shell
php bin/console make:rule-engine [Optional Name]
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```


* [Mark Entity Resource](docs/mark_entity_resources.md)
* [How get marked rules entity?](docs/how_get_marked_rule_entity.md)
* [How create new rule?](docs/how_create_new_rule.md)
* [How call event?](docs/Event.md)
