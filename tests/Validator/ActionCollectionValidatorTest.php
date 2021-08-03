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
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Validator\ActionCollectionValidator;
use DrinksIt\RuleEngineBundle\Validator\Asserts\ActionCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\UnsupportedMetadataException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ActionCollectionValidatorTest extends TestCase
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

                $builder->expects($this->once())->method('addViolation');
            }
        }
        $resourceRuleEntityCollection = new ResourceRuleEntityCollection();
        $resourceRuleEntityCollection->push(new ResourceRuleEntity(self::RULE_ENTITY_NAME, [
            new PropertyRuleEntity(
                'field',
                null,
                NumberActionType::class
            ),
            new PropertyRuleEntity(
                'type_string',
                null,
                StringActionType::class
            ),
        ], [
            self::EVENT_TRIGGER_COLUMN,
        ]));
        $engineFactory->method('create')->willReturn($resourceRuleEntityCollection);

        $actionCollectionValidator = new ActionCollectionValidator($engineFactory);
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
            $this->createMock(ActionCollection::class),
            null,
        ];

        yield 'UnexpectedValueException | RuleEntity' => [
            $this->createMock(CollectionActionsInterface::class),
            UnsupportedMetadataException::class,
            $this->createMock(ActionCollection::class),
            null,
        ];
    }

    public function validateValues(): iterable
    {
        $collection = $this->createMock(CollectionActionsInterface::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new NumberActionType('field', \stdClass::class, '1 + 1'),
        ]));

        yield 'Unsupported resource' => [
            $collection,
            null,
            $this->createMock(ActionCollection::class),
            $this->createMock(RuleEntityInterface::class),
        ];

        $collection = $this->createMock(CollectionActionsInterface::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new NumberActionType('field', self::RULE_ENTITY_NAME, '1 + 1'),
        ]));

        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
        $ruleEntityInterface->method('getTriggerEvent')->willReturn(
            new TriggerEventColumn('AnotherTriggerEvent')
        );

        yield 'Unsupported event' => [
            $collection,
            null,
            $this->createMock(ActionCollection::class),
            $ruleEntityInterface,
        ];

        $collection = $this->createMock(CollectionActionsInterface::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new NumberActionType('fieldType', self::RULE_ENTITY_NAME, '1 + 1'),
        ]));

        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
        $ruleEntityInterface->method('getTriggerEvent')->willReturn(
            new TriggerEventColumn(self::EVENT_TRIGGER_COLUMN)
        );

        yield 'Property not found' => [
            $collection,
            null,
            $this->createMock(ActionCollection::class),
            $ruleEntityInterface,
        ];

        $collection = $this->createMock(CollectionActionsInterface::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([
            new StringActionType('field', self::RULE_ENTITY_NAME, '1 + 1'),
        ]));

        yield 'Not match actions field types' => [
            $collection,
            null,
            $this->createMock(ActionCollection::class),
            $ruleEntityInterface,
        ];
    }
}
