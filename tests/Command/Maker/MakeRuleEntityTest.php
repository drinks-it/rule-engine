<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Command\Maker;

use Composer\Autoload\ClassLoader;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\Persistence\ManagerRegistry;
use DrinksIt\RuleEngineBundle\Command\Maker\MakeRuleEntity;
use DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Doctrine\EntityClassGenerator;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Util\AutoloaderUtil;
use Symfony\Bundle\MakerBundle\Util\ComposerAutoloaderFinder;
use Symfony\Bundle\MakerBundle\Util\MakerFileLinkFormatter;
use Symfony\Bundle\MakerBundle\Util\PhpCompatUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Debug\FileLinkFormatter;

/**
 * Class MakeRuleEntityTest
 * @package Tests\DrinksIt\RuleEngineBundle\Command\Maker
 */
class MakeRuleEntityTest extends TestCase
{
    private const TEST_NAMESPACE = '\Tests\DrinksIt\RuleEngineBundle\TmpFixtures';

    public function testGetCommandName(): void
    {
        $this->assertIsString(MakeRuleEntity::getCommandName());
    }

    public function testGetCommandDescriptionStatic(): void
    {
        $this->assertIsString(MakeRuleEntity::getCommandDescription());
        $this->assertIsString(MakeRuleEntity::test());
    }

    /**
     * @param array $containers
     * @depends testInitMakeRuleEntity
     * @cover ::__call
     */
    public function testGetCommandDescription(array $containers): void
    {
        /**
         * @var MakeRuleEntity $makeRuleEntity
         */
        [$makeRuleEntity] = $containers;
        $this->assertIsString($makeRuleEntity->test());
        $this->assertIsString($makeRuleEntity->getCommandDescription());
    }

    public function testInitMakeRuleEntity(): array
    {
        $classAutoLoaderFinder = $this->createMock(ComposerAutoloaderFinder::class);
        $classLoader = $this->createMock(ClassLoader::class);

        $classLoader->method('getPrefixesPsr4')->willReturn([]);
        $classLoader->method('getPrefixes')->willReturn([]);
        $classLoader->method('getFallbackDirsPsr4')->willReturn([]);
        $classLoader->method('getFallbackDirs')->willReturn([
            $_SERVER['TMP_ROOT_DIRECTORY'],
        ]);
        $classAutoLoaderFinder->method('getClassLoader')->willReturn($classLoader);

        $fileManager = new FileManager(
            new Filesystem(),
            new AutoloaderUtil(
                $classAutoLoaderFinder
            ),
            new MakerFileLinkFormatter(
                new FileLinkFormatter()
            ),
            $_SERVER['TMP_ROOT_DIRECTORY']
        );

        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->any())->method('hasParameter')
            ->with($this->callback(fn ($param) => preg_match('/rule_engine\.collection_(condition|actions)_class/', $param) !== false))
            ->willReturn(true);

        $container->expects($this->any())->method('getParameter')
            ->with($this->callback(fn ($param) => preg_match('/rule_engine\.collection_(condition|actions)_class/', $param) !== false))
            ->willReturnCallback(function ($param) {
                if ($param === 'rule_engine.collection_condition_class') {
                    return CollectionCondition::class;
                }

                if ($param === 'rule_engine.collection_actions_class') {
                    return CollectionActions::class;
                }

                return null;
            });

        $generator = new Generator($fileManager, self::TEST_NAMESPACE, new PhpCompatUtil($fileManager));

        $emConfigureation = $this->createMock(Configuration::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getConfiguration')->willReturn($emConfigureation);

        $emConnection = $this->createMock(Connection::class);
        $emConnection->method('getDatabasePlatform')->willReturn(
            new MySqlPlatform()
        );

        $emConfigureation->method('getNamingStrategy')->willReturn(new DefaultNamingStrategy());

        $manageRegistry = $this->createMock(ManagerRegistry::class);
        $manageRegistry->method('getManager')->willReturn($em);
        $manageRegistry->method('getConnection')->willReturn($emConnection);

        $makeRuleEntity = new MakeRuleEntity(
            $fileManager,
            new EntityClassGenerator(
                $generator,
                new DoctrineHelper(
                    'Entity',
                    $manageRegistry
                )
            ),
            $container
        );

        $this->assertInstanceOf(MakeRuleEntity::class, $makeRuleEntity);

        return [$makeRuleEntity, $generator];
    }

    public function testInitMakeRuleEntityEmptyParameter(): void
    {
        $this->expectException(RuntimeException::class);
        $classAutoLoaderFinder = $this->createMock(ComposerAutoloaderFinder::class);
        $classLoader = $this->createMock(ClassLoader::class);

        $classLoader->method('getPrefixesPsr4')->willReturn([]);
        $classLoader->method('getPrefixes')->willReturn([]);
        $classLoader->method('getFallbackDirsPsr4')->willReturn([]);
        $classLoader->method('getFallbackDirs')->willReturn([
            $_SERVER['TMP_ROOT_DIRECTORY'],
        ]);
        $classAutoLoaderFinder->method('getClassLoader')->willReturn($classLoader);

        $fileManager = new FileManager(
            new Filesystem(),
            new AutoloaderUtil(
                $classAutoLoaderFinder
            ),
            new MakerFileLinkFormatter(
                new FileLinkFormatter()
            ),
            $_SERVER['TMP_ROOT_DIRECTORY']
        );

        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())->method('hasParameter')
            ->with($this->callback(fn ($param) => preg_match('/rule_engine\.collection_(condition|actions)_class/', $param) !== false))
            ->willReturn(false);

        $container->expects($this->never())->method('getParameter');

        $generator = new Generator($fileManager, self::TEST_NAMESPACE, new PhpCompatUtil($fileManager));

        $emConfigureation = $this->createMock(Configuration::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getConfiguration')->willReturn($emConfigureation);

        $emConnection = $this->createMock(Connection::class);
        $emConnection->method('getDatabasePlatform')->willReturn(
            new MySqlPlatform()
        );

        $emConfigureation->method('getNamingStrategy')->willReturn(new DefaultNamingStrategy());

        $manageRegistry = $this->createMock(ManagerRegistry::class);
        $manageRegistry->method('getManager')->willReturn($em);
        $manageRegistry->method('getConnection')->willReturn($emConnection);

        $makeRuleEntity = new MakeRuleEntity(
            $fileManager,
            new EntityClassGenerator(
                $generator,
                new DoctrineHelper(
                    'Entity',
                    $manageRegistry
                )
            ),
            $container
        );

        $this->assertInstanceOf(MakeRuleEntity::class, $makeRuleEntity);
    }

    /**
     * @depends testInitMakeRuleEntity
     */
    public function testConfigureCommand(array $containers): void
    {
        /**
         * @var MakeRuleEntity $makeRuleEntity
         */
        [$makeRuleEntity] = $containers;
        $commandMock = $this->createMock(Command::class);
        $inputConfiguration = new InputConfiguration();

        $commandMock->expects($this->once())->method('addArgument')
            ->with(
                $this->equalTo('name'),
                $this->equalTo(InputArgument::OPTIONAL),
                $this->isType('string'),
                $this->isType('string')
            )->willReturnSelf();

        $commandMock->expects($this->once())->method('addOption')
            ->with(
                $this->equalTo('api-resource'),
                $this->equalTo('a'),
                $this->equalTo(InputOption::VALUE_NONE),
                $this->isType('string')
            );

        $makeRuleEntity->configureCommand($commandMock, $inputConfiguration);

        $this->assertTrue(\in_array('name', $inputConfiguration->getNonInteractiveArguments(), true));
    }

    /**
     * @depends testInitMakeRuleEntity
     * @param array $containers
     */
    public function testConfigureDependencies(array $containers): void
    {
        /**
         * @var MakeRuleEntity $makeRuleEntity
         */
        [$makeRuleEntity] = $containers;
        $inputMock = $this->createMock(InputInterface::class);

        $inputMock->expects($this->once())->method('getOption')
            ->with($this->equalTo('api-resource'))->willReturn(true);

        $dependencyBuilder = new DependencyBuilder();

        $makeRuleEntity->configureDependencies($dependencyBuilder, $inputMock);

        $this->assertTrue(\in_array('api', $dependencyBuilder->getAllRequiredDependencies()));
        $this->assertTrue(\in_array('orm', $dependencyBuilder->getAllRequiredDependencies()));

        // check empty input
        $dependencyBuilder = new DependencyBuilder();
        $makeRuleEntity->configureDependencies($dependencyBuilder);
        $this->assertEmpty($dependencyBuilder->getAllRequiredDependencies());
    }

    /**
     * @depends testInitMakeRuleEntity
     * @param mixed $containers
     */
    public function testGenerate($containers): void
    {
        /**
         * @var MakeRuleEntity $makeRuleEntity
         */
        [$makeRuleEntity, $generator] = $containers;
        $input = $this->createMock(InputInterface::class);
        $input->expects($this->any())->method('getArgument')
            ->with($this->equalTo('name'))->willReturn('RuleTest');

        $input->expects($this->once())->method('getOption')
            ->with($this->equalTo('api-resource'))->willReturn(null);

        $makeRuleEntity->generate(
            $input,
            new ConsoleStyle(
                $input,
                new ConsoleOutput()
            ),
            $generator
        );

        $fullRootPath = $_SERVER['TMP_ROOT_DIRECTORY'].\DIRECTORY_SEPARATOR.str_replace('\\', \DIRECTORY_SEPARATOR, self::TEST_NAMESPACE);

        $GeneratedFilePath = implode(\DIRECTORY_SEPARATOR, [
            $fullRootPath,
            'Entity',
            'RuleTest.php',
        ]);

        $GeneratedFileRepositoryPath = implode(\DIRECTORY_SEPARATOR, [
            $fullRootPath,
            'Repository',
            'RuleTestRepository.php',
        ]);

        $fullNameSpace = self::TEST_NAMESPACE.'\\Entity\\RuleTest';

        $this->assertFileExists($GeneratedFilePath);
        $this->assertFileExists($GeneratedFileRepositoryPath);

        require_once $GeneratedFilePath;

        $this->assertTrue(class_exists($fullNameSpace));

        $entity = new $fullNameSpace();
        $this->assertInstanceOf(RuleEntityInterface::class, $entity);

        // test generate if class exist
        $consoleOutPut =  new ConsoleStyle(
            $input,
            new ConsoleOutput()
        );
        $makeRuleEntity->generate(
            $input,
            $consoleOutPut,
            $generator
        );

        $this->assertTrue(true); // not exception, good

        unlink($GeneratedFilePath);
        unlink($GeneratedFileRepositoryPath);
        $this->assertFileDoesNotExist($GeneratedFilePath);
        $this->assertFileDoesNotExist($GeneratedFileRepositoryPath);
    }
}
