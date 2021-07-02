# How get marked rule entity

### General. Get all classes

```php
<?php

use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEngineFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;

class Service {
 
    protected $classes;
 
    public function __construct(
        RuleEngineFactoryInterface $resourceFactory
    )
    {
        /** @var ResourceRuleEntity $iterable */
        foreach ($resourceFactory->create() as $iterable) {
            $this->classes[] = [
                'class' => $iterable->getClassName(),
                'properties' => array_map(function (PropertyRuleEntity $ruleEntity) {
                    return [
                        'conditionType' => $ruleEntity->getClassNameAttributeConditionType(),
                        'actionType' => $ruleEntity->getClassNameActionFieldType()
                    ];
                }, $iterable->getProperties())
            ];
        }
    }

}
```

### By class

```php
<?php

use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactory;use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;

class Service {
    
    protected $classInfo;
    
    public function __construct(RuleEntityResourceFactory $factory) {
        $classInfo = $factory->create('ModelClassName');
        if ($classInfo instanceof ResourceRuleEntity) {
            // it's ok
        }
    }
}

```
