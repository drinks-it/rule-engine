<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Doctrine\Types\TriggerEventType;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use PHPUnit\Framework\TestCase;

class TriggerEventTypeTest extends TestCase
{
    private $platform;

    private $type;

    public function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->type = new TriggerEventType();
    }

    public function testGetName(): void
    {
        $this->assertEquals(TriggerEventType::TYPE, $this->type->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        $this->assertIsBool($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testConvertToDatabaseValue(): void
    {
        $actionInterface = $this->createMock(TriggerEventColumn::class);
        $actionInterface->expects($this->once())->method('getEntityClassName')
            ->willReturn(self::class);

        $this->assertEquals(
            self::class,
            $this->type->convertToDatabaseValue($actionInterface, $this->platform)
        );
    }

    public function testConvertToDatabaseValueWithException(): void
    {
        $this->expectException(ConversionException::class);
        $this->type->convertToDatabaseValue(['test' => ['olo'], 'can_json' => 'yes'], $this->platform);
    }

    public function testGetSQLDeclaration(): void
    {
        $this->platform->expects($this->once())->method('getStringTypeDeclarationSQL')
            ->with($this->equalTo(['foo', 'test']))
            ->willReturn('AnyTypeDoctrine');

        $this->assertIsString(
            $this->type->getSQLDeclaration(['foo', 'test'], $this->platform)
        );
    }

    public function testConvertToPHPValue(): void
    {
        $this->assertInstanceOf(TriggerEventColumn::class, $this->type->convertToPHPValue('NameClass', $this->platform));
    }
}
