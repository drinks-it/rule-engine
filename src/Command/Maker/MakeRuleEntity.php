<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Command\Maker;

use Doctrine\DBAL\Types\Types;
use DrinksIt\RuleEngineBundle\Doctrine\Types as ColumnType;
use DrinksIt\RuleEngineBundle\Rule as Rules;
use PhpParser\Builder\Param;
use PhpParser\Node\Expr as NodeExpr;
use PhpParser\Node\Stmt as Stmt;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\EntityClassGenerator;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @method string getCommandDescription()
 */
final class MakeRuleEntity extends AbstractMaker
{
    private EntityClassGenerator $entityClassGenerator;

    private FileManager $fileManager;

    private string $collectionConditionClassPath;

    private string $collectionConditionClassName;

    public function __construct(FileManager $fileManager, EntityClassGenerator $entityClassGenerator, ContainerInterface $container)
    {
        $this->fileManager = $fileManager;
        $this->entityClassGenerator = $entityClassGenerator;

        if ($container->hasParameter('rule_engine.collection_condition_class')) {
            $this->collectionConditionClassPath = (string) $container->getParameter('rule_engine.collection_condition_class');

            if (!class_exists($this->collectionConditionClassPath)) {
                throw new RuntimeException(sprintf('Class `%s` not found. Please check configuration `rule_engine.collection_condition_class`', $this->collectionConditionClassPath));
            }
        } else {
            $this->collectionConditionClassPath = Rules\ConditionsInterface::class;
        }

        $this->collectionConditionClassName = (new \ReflectionClass($this->collectionConditionClassPath))->getShortName();
    }

    public static function getCommandName(): string
    {
        return 'make:rule-entity';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::OPTIONAL, 'Class name of the entity to create or update <fg=yellow>Rule</>', 'Rule')
            ->addOption('api-resource', 'a', InputOption::VALUE_NONE, 'Mark this class as an API Platform resource (expose a CRUD API for it)')
        ;

        $inputConfig->setArgumentAsNonInteractive('name');
    }

    public function configureDependencies(DependencyBuilder $dependencies, InputInterface $input = null): void
    {
        if (null === $input) {
            return;
        }

        if ($input->getOption('api-resource')) {
            $dependencies->addClassDependency('\ApiPlatform\Core\Annotation\ApiResource', 'api');
        }
        $dependencies->addClassDependency('\Doctrine\Bundle\DoctrineBundle\DoctrineBundle', 'orm');
        $dependencies->addClassDependency('\Doctrine\ORM\Mapping\Column', 'orm');
    }

    /**
     * @throws \Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $ruleEntity = $generator->createClassNameDetails($input->getArgument('name'), 'Entity');

        $classExists = class_exists($ruleEntity->getFullName());

        if ($classExists) {
            $io->writeln("Class is exists. Please use ./bin/console make:entity " . $input->getArgument('name'));

            return;
        }

        $entityPath = $this->entityClassGenerator->generateEntityClass($ruleEntity, !empty($input->getOption('api-resource')));
        $generator->writeChanges();

        $manipulation = new ClassSourceManipulator($this->fileManager->getFileContents($entityPath), $classExists);

        $manipulation->addInterface(Rules\RuleEntityInterface::class);
        $manipulation->addUseStatementIfNecessary(Rules\ConditionsInterface::class);
        $manipulation->addUseStatementIfNecessary($this->collectionConditionClassPath);
        $manipulation->addUseStatementIfNecessary(Rules\ActionColumn::class);
        $manipulation->addUseStatementIfNecessary(Rules\TriggerEventColumn::class);
        $manipulation->addUseStatementIfNecessary(Rules\Condition\Condition::class);

        $manipulation->addConstructor(
            [],
            sprintf(<<<'CODE'
                <?php
                $this->conditions = new %s();
                CODE, $this->collectionConditionClassName)
        );

        $manipulation->addEntityField('name', [
            'type' => Types::STRING,
            'length' => 255,
            'nullable' => false,
        ]);

        $manipulation->addEntityField('description', [
            'nullable' => true,
            'type' => Types::TEXT,
        ]);

        $manipulation->addEntityField('active', [
            'type' => Types::BOOLEAN,
            'nullable' => false,
        ]);

        // conditions
        $manipulation->addProperty('conditions', ['@ORM\Column(type="'.ColumnType\ConditionsType::TYPE.'")']);
        $manipulation->addGetter('conditions', $this->collectionConditionClassName, false);
        $manipulation->addSetter('conditions', 'ConditionsInterface', false);

        $manipulation->addMethodBuilder($this->addElementToCollection());
        $manipulation->addMethodBuilder($this->removeElementFromCollection());

        // action
        $manipulation->addProperty('action', ['@ORM\Column(type="'.ColumnType\ActionType::TYPE.'")']);
        $manipulation->addGetter('action', 'ActionColumn', false);
        $manipulation->addSetter('action', 'ActionColumn', false);

        // triggerEvent
        $manipulation->addProperty('triggerEvent', ['@ORM\Column(type="'.ColumnType\TriggerEventType::TYPE.'")']);
        $manipulation->addGetter('triggerEvent', 'TriggerEventColumn', false);
        $manipulation->addSetter('triggerEvent', 'TriggerEventColumn', false);

        $manipulation->addEntityField('priority', [
            'type' => Types::INTEGER,
            'nullable' => false,
        ]);

        $manipulation->addEntityField('stopRuleProcessing', [
            'type' => Types::BOOLEAN,
            'nullable' => false,
        ]);

        $manipulation->addEntityField('createdAt', [
            'type' => Types::DATETIME_IMMUTABLE,
        ]);

        $manipulation->addEntityField('updatedAt', [
            'type' => Types::DATETIME_IMMUTABLE,
        ]);

        $this->fileManager->dumpFile($entityPath, $manipulation->getSourceCode());
    }

    private function addElementToCollection()
    {
        $methodAddCondition = (new \PhpParser\Builder\Method('addCondition'))->makePublic();
        $methodAddCondition->setReturnType('self');
        $paramBuilder = new Param('condition');
        $paramBuilder->setType('Condition');
        $methodAddCondition->addParam($paramBuilder->getNode());

        $conteinerMethodCallNode = new NodeExpr\MethodCall(
            new NodeExpr\PropertyFetch(new NodeExpr\Variable('this'), 'conditions'),
            'contains',
            [new NodeExpr\Variable('condition')]
        );

        $ifNotContaint = new Stmt\If_(new NodeExpr\BooleanNot($conteinerMethodCallNode));
        $methodAddCondition->addStmt($ifNotContaint);
        // append the item
        $ifNotContaint->stmts[] = new Stmt\Expression(
            new NodeExpr\Assign(
                new NodeExpr\ArrayDimFetch(
                    new NodeExpr\PropertyFetch(new NodeExpr\Variable('this'), 'conditions')
                ),
                new NodeExpr\Variable('condition')
            ),
        )
        ;

        $methodAddCondition->addStmt(
            new Stmt\Return_(new NodeExpr\Variable('this'))
        );

        return $methodAddCondition;
    }

    private function removeElementFromCollection()
    {
        $methodAddCondition = (new \PhpParser\Builder\Method('removedCondition'))->makePublic();
        $methodAddCondition->setReturnType('self');
        $paramBuilder = new Param('condition');
        $paramBuilder->setType('Condition');
        $methodAddCondition->addParam($paramBuilder->getNode());

        $conteinerMethodCallNode = new NodeExpr\MethodCall(
            new NodeExpr\PropertyFetch(new NodeExpr\Variable('this'), 'conditions'),
            'contains',
            [new NodeExpr\Variable('condition')]
        );

        $ifContaint = new Stmt\If_($conteinerMethodCallNode);
        $methodAddCondition->addStmt($ifContaint);
        // append the item
        $ifContaint->stmts[] = new Stmt\Expression(
            new NodeExpr\MethodCall(
                new NodeExpr\PropertyFetch(new NodeExpr\Variable('this'), 'conditions'),
                'removeElement',
                [new NodeExpr\Variable('condition')]
            ),
        )
        ;

        $methodAddCondition->addStmt(
            new Stmt\Return_(new NodeExpr\Variable('this'))
        );

        return $methodAddCondition;
    }

    public function __call($name, $arguments): string
    {
        if ('getCommandDescription' === $name) {
            return 'Make Rule Engine Entity';
        }

        return '';
    }

    public static function __callStatic($name, $arguments): string
    {
        if ('getCommandDescription' === $name) {
            return 'Make Rule Engine Entity';
        }

        return '';
    }
}