<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ObjectManager;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityExtractorInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\CollectionRuleEntityResourceNameFactory;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityNameCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class CollectionRuleEntityResourceNameFactoryTest extends TestCase
{
    /**
     * @dataProvider casesCreateCollections
     * @param mixed $exceptedResult
     */
    public function testCreate(array $setReturnClassMetadata, callable $setMockExtractor, $exceptedResult): void
    {
        $mockMetaDataFactory = $this->createMock(ClassMetadataFactory::class);
        $mockMetaDataFactory->expects($this->once())->method('getAllMetadata')
            ->willReturn($setReturnClassMetadata);

        $mockObjectManager = $this->createMock(ObjectManager::class);
        $mockObjectManager->expects($this->once())->method('getMetadataFactory')
            ->willReturn($mockMetaDataFactory);

        $mockManageRegistry = $this->createMock(ManagerRegistry::class);
        $mockManageRegistry->expects($this->once())->method('getManager')
            ->willReturn($mockObjectManager);

        $mockExtractor = $this->createMock(RuleEntityExtractorInterface::class);
        $setMockExtractor($mockExtractor);

        $ruleFactory = new CollectionRuleEntityResourceNameFactory(
            $mockManageRegistry,
            $mockExtractor
        );
        $this->assertEquals($exceptedResult, $ruleFactory->create());
    }

    public function casesCreateCollections(): array
    {
        return [
            'Empty List from Manager Registry' => [
                [],
                function (MockObject $extractor): void {
                    $extractor->expects($this->never())->method('getRuleEntityResourceAnnotation');
                },
                new ResourceRuleEntityNameCollection([]),
            ],
            'Has class, not support entity' => [
                [
                    (function (MockObject $mockObject) {
                        $mockObject->method('getName')->willReturn('ClassEntityName');

                        return $mockObject;
                    })($this->createMock(ClassMetadata::class)),
                ],
                function (MockObject $extractor): void {
                    $extractor->expects($this->once())
                        ->method('getRuleEntityResourceAnnotation')
                        ->with($this->equalTo('ClassEntityName'))
                        ->willReturn(null);
                },
                new ResourceRuleEntityNameCollection(),
            ],
            'Has class, support entity' => [
                [
                    (function (MockObject $mockObject) {
                        $mockObject->method('getName')->willReturn('ClassEntityName');

                        return $mockObject;
                    })($this->createMock(ClassMetadata::class)),
                ],
                function (MockObject $extractor): void {
                    $extractor->expects($this->once())
                        ->method('getRuleEntityResourceAnnotation')
                        ->with($this->equalTo('ClassEntityName'))
                        ->willReturn(
                            new RuleEntityResource()
                        );
                },
                new ResourceRuleEntityNameCollection(['ClassEntityName']),
            ],
        ];
    }
}
