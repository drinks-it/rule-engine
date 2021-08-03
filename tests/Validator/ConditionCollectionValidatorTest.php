<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Validator;

use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEngineFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityCollection;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\NumberActionType;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\StringActionType;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\NumberConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\NumberAttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\StringAttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Validator\Asserts\ConditionCollection;
use DrinksIt\RuleEngineBundle\Validator\ConditionCollectionValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\UnsupportedMetadataException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ConditionCollectionValidatorTest extends TestCase
{
    private const EVENT_TRIGGER_COLUMN = 'EntityClassNameEvent';
    private const RULE_ENTITY_NAME = 'RuleEntityName';
    /**
     * @dataProvider validateValuesExceptions
     * @dataProvider validateValues
     *
     * @param mixed $value
     * @param mixed $expectException
     * @param mixed $constraint
     * @param mixed|MockObject $ruleEntityInterface
     */
    public function testValidate($value, $expectException, $constraint, $ruleEntityInterface): void
    {
        $engineFactory = $this->createMock(RuleEngineFactoryInterface::class);
        $executionContextInterface = $this->createMock(ExecutionContextInterface::class);

        if ($ruleEntityInterface) {
            $executionContextInterface->method('getRoot')->willReturn($ruleEntityInterface);

            if (!$expectException) {
                $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
                $executionContextInterface
                    ->expects($this->once())
                    ->method('buildViolation')->with(
                        $this->isType('string'),
                        $this->isType('array')
                    )
                    ->willReturn($builder);
                $builder->method('atPath')->with(
                    $this->isType('string')
                )->willReturnSelf();

                $builder->expects($this->once())->method('addViolation');
            }
        }
        $resourceRuleEntityCollection = new ResourceRuleEntityCollection();
        $resourceRuleEntityCollection->push(new ResourceRuleEntity(self::RULE_ENTITY_NAME, [
            new PropertyRuleEntity(
                'field',
                NumberConditionAttribute::class,
            ),
            new PropertyRuleEntity(
                'type_string',
                StringConditionAttribute::class,
            ),
        ], [
            self::EVENT_TRIGGER_COLUMN,
        ]));
        $engineFactory->method('create')->willReturn($resourceRuleEntityCollection);

        $actionCollectionValidator = new ConditionCollectionValidator($engineFactory);
        $actionCollectionValidator->initialize($executionContextInterface);

        if ($expectException) {
            $this->expectException($expectException);
        }

        $actionCollectionValidator->validate($value, $constraint);
    }

    public function validateValuesExceptions(): iterable
    {
        yield 'UnexpectedTypeException | Constraint' => [
            null,
            UnexpectedTypeException::class,
            $this->createMock(Constraint::class),
            null,
        ];

        yield 'UnexpectedValueException | Value' => [
            null,
            UnexpectedValueException::class,
            $this->createMock(ConditionCollection::class),
            null,
        ];

        yield 'UnexpectedValueException | RuleEntity' => [
            $this->createMock(CollectionConditionInterface::class),
            UnsupportedMetadataException::class,
            $this->createMock(ConditionCollection::class),
            null,
        ];
    }

    public function validateValues(): iterable
    {
        $collection = $this->createMock(CollectionConditionInterface::class);
        $subCollection = $this->createMock(CollectionConditionInterface::class);
        $subCollection->method('isEmpty')->willReturn(true);

        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(Condition::TYPE_ALL, 1, null, $subCollection, true),
        ]));

        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
        $ruleEntityInterface->method('getTriggerEvent')->willReturn(
            new TriggerEventColumn(self::EVENT_TRIGGER_COLUMN)
        );

        yield 'Empty collection' => [
            $collection,
            null,
            $this->createMock(ConditionCollection::class),
            $ruleEntityInterface,
        ];

        $collection = $this->createMock(CollectionConditionInterface::class);
        $subCollection = $this->createMock(CollectionConditionInterface::class);

        $subCollectionInSub = $this->createMock(CollectionConditionInterface::class);
        $subCollectionInSub->method('isEmpty')->willReturn(true);

        $subCollection->method('isEmpty')->willReturn(false);
        $subCollection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(Condition::TYPE_ALL, 1, null, $subCollectionInSub, true),
        ]));

        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(Condition::TYPE_ALL, 1, null, $subCollection, true),
        ]));

        yield 'Empty collection in sub conditions' => [
            $collection,
            null,
            $this->createMock(ConditionCollection::class),
            $ruleEntityInterface,
        ];

        $collection = $this->createMock(CollectionConditionInterface::class);
        $subCollection = $this->createMock(CollectionConditionInterface::class);
        $subCollection->method('isEmpty')->willReturn(false);
        $subCollection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(
                Condition::TYPE_ATTRIBUTE,
                1,
                new NumberConditionAttribute(
                    'Test',
                    'field',
                    NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                    1
                )
            ),
        ]));

        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(Condition::TYPE_ALL, 1, null, $subCollection, true),
        ]));

        yield 'Resource not found' => [
            $collection,
            null,
            $this->createMock(ConditionCollection::class),
            $ruleEntityInterface,
        ];

        $collection = $this->createMock(CollectionConditionInterface::class);
        $subCollection = $this->createMock(CollectionConditionInterface::class);
        $subCollection->method('isEmpty')->willReturn(false);
        $subCollection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(
                Condition::TYPE_ATTRIBUTE,
                1,
                new NumberConditionAttribute(
                    self::RULE_ENTITY_NAME,
                    'field_not_found',
                    NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                    1
                )
            ),
        ]));

        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(Condition::TYPE_ALL, 1, null, $subCollection, true),
        ]));

        yield 'Property not found' => [
            $collection,
            null,
            $this->createMock(ConditionCollection::class),
            $ruleEntityInterface,
        ];

        $collection = $this->createMock(CollectionConditionInterface::class);
        $subCollection = $this->createMock(CollectionConditionInterface::class);
        $subCollection->method('isEmpty')->willReturn(false);
        $subCollection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(
                Condition::TYPE_ATTRIBUTE,
                1,
                new StringConditionAttribute(
                    self::RULE_ENTITY_NAME,
                    'field',
                    StringAttributeConditionTypeInterface::OPERATOR_EQ,
                    1
                )
            ),
        ]));

        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(Condition::TYPE_ALL, 1, null, $subCollection, true),
        ]));

        yield 'Property not match by class' => [
            $collection,
            null,
            $this->createMock(ConditionCollection::class),
            $ruleEntityInterface,
        ];

        $collection = $this->createMock(CollectionConditionInterface::class);
        $subCollection = $this->createMock(CollectionConditionInterface::class);
        $subCollection->method('isEmpty')->willReturn(false);
        $subCollection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(
                Condition::TYPE_ATTRIBUTE,
                1,
                new NumberConditionAttribute(
                    self::RULE_ENTITY_NAME,
                    'field',
                    NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                    1
                )
            ),
        ]));

        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new Condition(Condition::TYPE_ALL, 1, null, $subCollection, true),
        ]));

        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
        $ruleEntityInterface->method('getTriggerEvent')->willReturn(
            new TriggerEventColumn('AnotherTriggerEvent')
        );

        yield 'Unsupported event' => [
            $collection,
            null,
            $this->createMock(ConditionCollection::class),
            $ruleEntityInterface,
        ];
//
//        $collection = $this->createMock(CollectionConditionInterface::class);
//        $collection->method('getIterator')->willReturn(new \ArrayIterator([
//            new NumberActionType('fieldType', self::RULE_ENTITY_NAME, '1 + 1'),
//        ]));
//
//        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
//
//        yield 'Property not found' => [
//            $collection,
//            null,
//            $this->createMock(ConditionCollection::class),
//            $ruleEntityInterface,
//        ];
//
//        $collection = $this->createMock(CollectionActionsInterface::class);
//        $collection->method('getIterator')->willReturn(new \ArrayIterator([
//            new StringActionType('field', self::RULE_ENTITY_NAME, '1 + 1'),
//        ]));
//
//        yield 'Not match actions field types' => [
//            $collection,
//            null,
//            $this->createMock(ConditionCollection::class),
//            $ruleEntityInterface,
//        ];
    }
}
