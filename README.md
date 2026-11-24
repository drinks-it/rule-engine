# Rule Engine Bundle

A Symfony bundle for evaluating conditions and running actions on Doctrine entities
when an application event occurs. It provides rule metadata, condition and action
collections, Symfony Serializer and Validator integration, custom Doctrine types,
and optional API Platform schema integration.

## Requirements

The current `composer.json` declares:

| Dependency | Constraint |
| --- | --- |
| PHP | `^8.2` |
| Symfony components | `7.4.*` |
| Doctrine ORM | `^3.3` |
| Doctrine DBAL | `^4.3` |
| DoctrineBundle | `^2.18` |
| Doctrine Collections | `^2.3` |

The development dependencies target API Platform `^5.0` (`api-platform/symfony`
and `api-platform/doctrine-orm`). API Platform is optional for applications using
the bundle. Rule mappings use native PHP attributes; `doctrine/annotations` is
not required and docblock mappings are no longer read.

## Installation

```shell
composer require drinks-it/rule-engine
```

Register the bundle in `config/bundles.php` alongside FrameworkBundle and
DoctrineBundle:

```php
return [
    // Other application bundles...
    DrinksIt\RuleEngineBundle\RuleEngineBundle::class => ['all' => true],
];
```

Enable Symfony Serializer and Validator in the host application:

```yaml
# config/packages/framework.yaml
framework:
    serializer: { enabled: true }
    validation: { enabled: true }
```

## Doctrine configuration

Register the types used to store rule conditions, actions, and trigger events:

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            rule-engine-conditions: DrinksIt\RuleEngineBundle\Doctrine\Types\ConditionsType
            rule-engine-action: DrinksIt\RuleEngineBundle\Doctrine\Types\ActionType
            rule-engine-event: DrinksIt\RuleEngineBundle\Doctrine\Types\TriggerEventType
```

Use these same names in your entity column mappings. The application supplies its
rule entity implementing `DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface`,
its Doctrine mappings, and database migrations. This bundle does not provide a
`make:rule-engine` command.

## Entity mappings

Mark Doctrine entities with `RuleEntityResource` and expose their properties to
conditions and actions with `RuleEntityProperty`. For example, add these
attributes to an existing mapped entity and property:

```php
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\StringActionType;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;

#[RuleEntityResource]
class Product
{
    #[RuleEntityProperty(
        condition: StringConditionAttribute::class,
        action: StringActionType::class,
    )]
    private string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

The example shows the rule attributes only; retain your Doctrine entity, ID, and
column attributes. `RuleEntityResource` accepts an `events` array to restrict
applicable events. `RuleEntityProperty` accepts `condition`, `action`,
`onlyEvents`, and `relationObject` arguments.

See [entity mapping examples](docs/mark_entity_resources.md) and
[accessing rule metadata](docs/how_get_marked_rule_entity.md).

## Events and rules

Implement `DrinksIt\RuleEngineBundle\Event\RuleEventInterface` for an application
rule event. With Symfony service autoconfiguration enabled, these services receive
the `rule_engine.tag.event` tag automatically.

Dispatch an `ObserverEntityEventInterface` implementation through Symfony's event
dispatcher using `RuleEvent::EVENT`. The observer supplies the entities to process
and the rule event class. See the [event workflow](docs/Event.md) and
[rule creation examples](docs/how_create_new_rule.md). The application provides
its own endpoints and persistence logic.

## Optional API Platform integration

Install the packages targeted by the current development configuration:

```shell
composer require api-platform/symfony:^5.0 api-platform/doctrine-orm:^5.0
```

Register `ApiPlatform\Symfony\Bundle\ApiPlatformBundle` in the host application.
The rule engine loads its API Platform services when that bundle class is
available, adding property normalization and JSON schema decoration for rules.
See the [condition schema](docs/ConditionsJsonSchema.md) and
[action schema](docs/ActionJsonSchema.md) documentation.

## Upgrading

Applications upgrading from Symfony 6, Doctrine ORM 2, or DBAL 3 must update their
dependencies to the constraints above. Review generated Doctrine migrations,
including changes to legacy SQL comment hints.

Convert `@RuleEntityResource` and `@RuleEntityProperty` docblocks to PHP attributes.
The legacy `RuleEntityAnnotationExtractor` and annotation reader integration have
been removed. Custom extractor integrations should use
`RuleEntityExtractorInterface`; the default implementation is
`RuleEntityAttributeExtractor`.

## Development

```shell
composer install
vendor/bin/simple-phpunit install
composer test-unit
composer php-cs
```

Use `composer php-cs-fix` to apply code style fixes. The integration tests require
`pdo_sqlite`; the coverage command requires a coverage driver such as Xdebug.
The suite covers mappings, conditions, actions, serialization, validation, and
bundle boot with API Platform, including schema generation and SQLite round trips
for custom Doctrine types.

## License

MIT.
