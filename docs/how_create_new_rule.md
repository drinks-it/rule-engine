# How create new Rule?

```http request
POST http://example.com/api/create_new_rule
Content-Type: application/json; charset=utf-8

{
  "name": "Rule name",
  "description": "Description rule. My first rule",
  "active": true,
  "stopRuleProcessing": true,
  "priority": 1,
  "conditions": [
    {
      "type": "ANY",
      "result": true,
      "subConditions": [
        {
          "entityShortName": "Product",
          "fieldName": "field",
          "operator": "=",
          "value": 34
        },
        {
          "entityShortName": "Product",
          "fieldName": "fieldAnother",
          "operator": "=",
          "value": 56
        }
      ]
    }
  ],
  "actions": [
    {
      "execute": "%field% + %fieldAnother%"
    }
  ],
  "triggerEvent": "App\\Event\\Rule\\EventNameRule"
}

###
```

```php
<?php

use Doctrine\ORM\EntityManagerInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\Factory\RuleEntityPropertyFactoryInterface;use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use Symfony\Component\HttpFoundation\Request;

class Service 
{
    private EntityManagerInterface $entityManager;
    
    private RuleEntityPropertyFactoryInterface $propertyFactory;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        RuleEntityPropertyFactoryInterface $propertyFactory
    ) {
        $this->entityManager = $entityManager;
        $this->propertyFactory = $propertyFactory;
    }
    
    public function createNewRule(Request $request, RuleEntityInterface $ruleEntity) 
    {      
        $data = $request->toArray();
        
        $ruleEntity->setName($data['name']);
        $ruleEntity->setActive($data['active']);
        $ruleEntity->setDescription($data['description']);
        $ruleEntity->setPriority($data['priority']);
        $ruleEntity->setTriggerEvent(new TriggerEventColumn($data['triggerEvent']));
        $ruleEntity->setConditions($this->decodeConditions($data['conditions'] ?? []));
        
        // todo action
        
        $this->entityManager->persist($ruleEntity);
        $this->entityManager->flush();
        
    }
    
    private function decodeConditions(array $conditions = []): CollectionConditionInterface {
        $collectionReturn = new CollectionCondition();
        foreach ($conditions as $idx => $condition) {
            $conditionObject = new Condition($condition['type'], $idx);
            $conditionObject->setResult((bool) $condition['result']);
            if ($conditionObject->getType() !== Condition::TYPE_ATTRIBUTE) {
                $conditionObject->setSubConditions(
                    $this->decodeConditions($conditions['subConditions'] ?? [])
                );
                $collectionReturn->add($conditionObject);
                continue;
            } 
            
            // decode short name, to real class name path
            $classProperties = $this->propertyFactory->create($condition['entityShortName']);          
            if (isset($classProperties[$condition['fieldName']])) {
                throw new RuntimeException('Check your property');
            }
            
            $propertyMetadata = $classProperties[$condition['fieldName']];
            $classNameCondition = $propertyMetadata->getClassNameAttributeConditionType();
            
            /** @var AttributeConditionTypeInterface $objectAttributeCondition */
            $objectAttributeCondition = new $classNameCondition(
                $condition['entityShortName'], 
                $propertyMetadata->getName()
            );
            
            $objectAttributeCondition->setOperator($condition['operator']);
            $objectAttributeCondition->setValue($condition['value']);
            
       
            $conditionObject->setAttributeCondition($objectAttributeCondition);
            $collectionReturn->add($conditionObject);
        }
        
        return $collectionReturn;
    }
    
    public function decodeActions() : CollectionActionsInterface
    {
        // todo
    }
    
}

```
