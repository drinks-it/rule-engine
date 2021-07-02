## Install

```shell
composer requrie drinks-it/rule-engine
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

[Event](docs/Event.md)
