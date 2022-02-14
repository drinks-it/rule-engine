<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Validator;

use DrinksIt\RuleEngineBundle\Event\Factory\RuleEventListenerFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Validator\Asserts\TriggerEvent;
use DrinksIt\RuleEngineBundle\Validator\TriggerEventValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\UnsupportedMetadataException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class TriggerEventValidatorTest extends TestCase
{
    /**
     * @dataProvider validateValuesExceptions
     * @dataProvider validateValues
     *
     * @param mixed $value
     * @param mixed $expectException
     * @param mixed $constraint
     * @param mixed $ruleEntityInterface
     */
    public function testValidate($value, $expectException, $constraint, $ruleEntityInterface): void
    {
        $ruleEventListenerFactoryInterface = $this->createMock(RuleEventListenerFactoryInterface::class);
        $executionContextInterface = $this->createMock(ExecutionContextInterface::class);

        if ($ruleEntityInterface) {
            $executionContextInterface->method('getRoot')->willReturn($ruleEntityInterface);

            $ruleEventListenerFactoryInterface->expects($this->once())->method('create')
                ->willReturn([
                    ['resource' => 'Event1'],
                    ['resource' => 'Event2'],
                    ['resource' => 'Event3'],
                ]);

            if (!$expectException && null === $value->getEntityClassName()) {
                $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
                $executionContextInterface
                    ->expects($this->once())
                    ->method('buildViolation')->with(
                        $this->isType('string'),
                        $this->isType('array')
                    )
                    ->willReturn($builder);

                $builder->method('setCode')->with(
                    $this->equalTo(TriggerEventValidator::CODE)
                )->willReturnSelf();

                $builder->expects($this->once())->method('addViolation');
            }
        }

        $eventValidator = new TriggerEventValidator($ruleEventListenerFactoryInterface);
        $eventValidator->initialize($executionContextInterface);

        if ($expectException) {
            $this->expectException($expectException);
        }
        $eventValidator->validate($value, $constraint);
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
            $this->createMock(TriggerEvent::class),
            null,
        ];

        yield 'UnexpectedValueException | TriggerEvent' => [
            $this->createMock(TriggerEventColumn::class),
            UnsupportedMetadataException::class,
            $this->createMock(TriggerEvent::class),
            null,
        ];
    }

    public function validateValues(): iterable
    {
        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);

        return [
            'Event not found' => [
                new TriggerEventColumn(),
                null,
                $this->createMock(TriggerEvent::class),
                $ruleEntityInterface,
            ],
            'Event found' => [
                new TriggerEventColumn('Event3'),
                null,
                $this->createMock(TriggerEvent::class),
                $ruleEntityInterface,
            ],
        ];
    }
}
