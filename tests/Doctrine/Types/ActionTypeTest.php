<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Doctrine\Types\ActionType;
use DrinksIt\RuleEngineBundle\Rule\ActionColumn;
use PHPUnit\Framework\TestCase;

class ActionTypeTest extends TestCase
{
    private $platform;

    private $type;

    public function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->type = new ActionType();
    }

    public function testGetName(): void
    {
        $this->assertEquals(ActionType::TYPE, $this->type->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        $this->assertIsBool($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testConvertToDatabaseValue(): void
    {
        $actionInterface = new ActionColumn(['test' => ['olo'], 'can_json' => 'yes']);

        $this->assertJson(
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
        $this->platform->expects($this->once())->method('getJsonTypeDeclarationSQL')
            ->with($this->equalTo(['foo', 'test']))
            ->willReturn('AnyTypeDoctrine');

        $this->assertIsString(
            $this->type->getSQLDeclaration(['foo', 'test'], $this->platform)
        );
    }
}
