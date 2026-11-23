<?php

/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2026 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Integration;

use ApiPlatform\Symfony\Bundle\ApiPlatformBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\RuleEngineBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class KernelTest extends TestCase
{
    public function testBundleBootsWithApiPlatform(): void
    {
        $kernel = new class ('test', true) extends Kernel {
            public function registerBundles(): iterable
            {
                yield new FrameworkBundle();
                yield new DoctrineBundle();
                yield new ApiPlatformBundle();
                yield new RuleEngineBundle();
            }

            public function getCacheDir(): string
            {
                return \dirname(__DIR__, 2).'/var/kernel/cache/'.\PHP_VERSION_ID;
            }

            public function getLogDir(): string
            {
                return \dirname(__DIR__, 2).'/var/kernel/log';
            }

            public function registerContainerConfiguration(LoaderInterface $loader): void
            {
                $loader->load(function (ContainerBuilder $container): void {
                    $container->loadFromExtension('framework', [
                        'secret' => 'rule-engine-test',
                        'property_access' => true,
                        'test' => true,
                        'router' => ['resource' => __DIR__.'/routes.php'],
                        'serializer' => ['enabled' => true],
                        'validation' => ['enabled' => true],
                        'property_info' => ['enabled' => true, 'with_constructor_extractor' => true],
                    ]);
                    $container->loadFromExtension('doctrine', [
                        'dbal' => [
                            'url' => 'sqlite:///:memory:',
                            'types' => [
                                'rule_engine_action' => \DrinksIt\RuleEngineBundle\Doctrine\Types\ActionType::class,
                                'rule_engine_conditions' => \DrinksIt\RuleEngineBundle\Doctrine\Types\ConditionsType::class,
                                'rule_engine_event' => \DrinksIt\RuleEngineBundle\Doctrine\Types\TriggerEventType::class,
                            ],
                        ],
                        'orm' => ['auto_generate_proxy_classes' => true, 'controller_resolver' => ['auto_mapping' => false]],
                    ]);
                    $container->loadFromExtension('api_platform', ['title' => 'Rule Engine Test']);
                    $container->loadFromExtension('rule_engine', []);
                    $container->setAlias('test.serializer', 'serializer')->setPublic(true);
                    $container->setAlias('test.schema_factory', 'api_platform.json_schema.schema_factory')->setPublic(true);
                });
            }
        };

        try {
            $kernel->boot();
            $container = $kernel->getContainer();
            $schema = $container->get('test.schema_factory')->buildSchema(Condition::class);
            $this->assertNotEmpty($schema->getDefinitions());
            $serializer = $container->get('test.serializer');
            $this->assertSame([
                'type' => Condition::TYPE_ANY,
                'exceptedResult' => true,
                'subConditions' => [],
            ], $serializer->normalize(new Condition(subConditions: new \DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition())));
            $this->assertSame([], $serializer->normalize(new \DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions()));
            $this->assertInstanceOf(\DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface::class, $serializer->denormalize([], Condition::class.'[]'));
            $this->assertSame('TestEvent', $serializer->normalize(new \DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn('TestEvent')));

            $connection = $container->get('doctrine')->getConnection();
            $connection->executeStatement('CREATE TABLE rule_test (actions TEXT, conditions TEXT, event TEXT)');
            $connection->insert('rule_test', [
                'actions' => new \DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions(),
                'conditions' => new \DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition(),
                'event' => new \DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn('TestEvent'),
            ], ['actions' => 'rule_engine_action', 'conditions' => 'rule_engine_conditions', 'event' => 'rule_engine_event']);
            $row = $connection->fetchAssociative('SELECT * FROM rule_test');
            $this->assertCount(0, $connection->convertToPHPValue($row['actions'], 'rule_engine_action'));
            $this->assertCount(0, $connection->convertToPHPValue($row['conditions'], 'rule_engine_conditions'));
            $this->assertSame('TestEvent', $connection->convertToPHPValue($row['event'], 'rule_engine_event')->getEntityClassName());
        } finally {
            $kernel->shutdown();
        }
    }
}
