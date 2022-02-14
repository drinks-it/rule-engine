<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Doctrine\Types\ConditionsType;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ConditionsTypeTest extends TestCase
{
    private $platform;

    private $type;

    public function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->type = new ConditionsType();
    }

    public function testGetName(): void
    {
        $this->assertEquals(ConditionsType::TYPE, $this->type->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        $this->assertIsBool($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testConvertToDatabaseValue(): string
    {
        $actionInterface = new CollectionCondition([
            new Condition(Condition::TYPE_ALL, 1, null, new CollectionCondition([
                new Condition(
                    Condition::TYPE_ATTRIBUTE,
                    1,
                    new StringConditionAttribute(
                        stdClass::class,
                        'test',
                        StringConditionAttribute::OPERATOR_EQ,
                        'false'
                    )
                ),
            ])),
        ]);
        $jsonSerialized = $this->type->convertToDatabaseValue($actionInterface, $this->platform);
        $this->assertJson($jsonSerialized);

        return $jsonSerialized;
    }

    public function testConvertToDatabaseValueWithException(): void
    {
        $this->expectException(ConversionException::class);
        $this->type->convertToDatabaseValue(['test' => ['olo'], 'can_json' => 'yes'], $this->platform);
    }

    public function testGetSQLDeclaration(): void
    {
        $this->platform->expects($this->once())->method('getJsonTypeDeclarationSQL')
            ->with($this->equalTo(['foo', 'test']))
            ->willReturn('AnyTypeDoctrine');

        $this->assertIsString(
            $this->type->getSQLDeclaration(['foo', 'test'], $this->platform)
        );
    }

    /**
     * @depends testConvertToDatabaseValue
     * @param mixed $jsonSerialized
     */
    public function testConvertToPHPValue($jsonSerialized): void
    {
        $object = $this->type->convertToPHPValue($jsonSerialized, $this->platform);
        $this->assertInstanceOf(CollectionConditionInterface::class, $object);
        foreach ($object as $itemCondition) {
            $this->assertInstanceOf(Condition::class, $itemCondition);
        }

        $this->assertInstanceOf(CollectionConditionInterface::class, $this->type->convertToPHPValue('{}', $this->platform));

        $this->expectException(NotEncodableValueException::class);
        $this->type->convertToPHPValue('', $this->platform);
    }
}
