<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Mapping;

trait AnnotationFixtures
{
    private string $tmpPathTmpEntityClass = '';

    public function loadResourceWithBrokeProperty(string $className): void
    {
        if (class_exists($className)) {
            return;
        }
        $this->tmpPathTmpEntityClass = $_SERVER['TMP_ROOT_DIRECTORY'].DIRECTORY_SEPARATOR.$className.'.php';

        if (file_exists($this->tmpPathTmpEntityClass)) {
            unlink($this->tmpPathTmpEntityClass);
        }
        $entityResource = sprintf(<<<'CODE'
            <?php
            use DrinksIt\RuleEngineBundle\Mapping as RuleEngine;
            /**
             * @RuleEngine\RuleEntityResource()
             */
             class %s {
               /**
                *  @RuleEngine\RuleEntityResource()
                */
                private $test;
             }
            CODE, $className);

        file_put_contents($this->tmpPathTmpEntityClass, $entityResource);
        $this->assertFileExists($this->tmpPathTmpEntityClass);

        require $this->tmpPathTmpEntityClass;
    }

    public function loadResourceWithValidProperty(string $className, $propertyName = 'test'): void
    {
        if (class_exists($className)) {
            return;
        }
        $tmpPathTmpEntityClass = $_SERVER['TMP_ROOT_DIRECTORY'].DIRECTORY_SEPARATOR.$className.'.php';

        if (file_exists($tmpPathTmpEntityClass)) {
            unlink($tmpPathTmpEntityClass);
        }

        $entityResource = sprintf(<<<'CODE'
            <?php
            use DrinksIt\RuleEngineBundle\Mapping as RuleEngine;
            use DrinksIt\RuleEngineBundle\Rule\Types\AttributeConditionTypeInterface;
            /**
             * @RuleEngine\RuleEntityResource()
             */
            class %s {
               /**
                *  @RuleEngine\RuleEntityProperty(
                *        interfaceType=AttributeConditionTypeInterface::class
                *    )
                * @var string
                */
                private $%s;
            }
            CODE, $className, $propertyName);

        file_put_contents($tmpPathTmpEntityClass, $entityResource);
        $this->assertFileExists($tmpPathTmpEntityClass);

        require $tmpPathTmpEntityClass;
    }

    public function tearDown(): void
    {
        if (!$this->tmpPathTmpEntityClass) {
            return;
        }

        if (file_exists($this->tmpPathTmpEntityClass)) {
            unlink($this->tmpPathTmpEntityClass);
        }
    }
}
