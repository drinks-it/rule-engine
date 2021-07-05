<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine;

use Doctrine\ORM\EntityRepository;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

final class RuleEntityFinder implements RuleEntityFinderInterface
{
    private $ruleEntitesRepositories = [];

    /**
     * @var RuleEntityFinderExtensionInterface[]
     */
    private array $ruleExtensions = [];

    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry, iterable $ruleExtensions)
    {
        $this->managerRegistry = $managerRegistry;

        foreach ($managerRegistry->getManager()->getMetadataFactory()->getAllMetadata() as $classMetadata) {
            if (\in_array(RuleEntityInterface::class, $classMetadata->getReflectionClass()->getInterfaceNames())) {
                $this->ruleEntitesRepositories[] = $classMetadata->getName();
            }
        }

        foreach ($ruleExtensions as $extension) {
            if (!$extension instanceof RuleEntityFinderExtensionInterface) {
                continue;
            }
            $this->ruleExtensions[] = $extension;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRulesByEventName(string $eventClassName): iterable
    {
        $ruleEntities = [];
        foreach ($this->ruleEntitesRepositories as $entityClassName) {
            $repository = $this->managerRegistry->getRepository($entityClassName);

            if (!$repository instanceof EntityRepository) {
                continue;
            }

            $qb = $repository->createQueryBuilder('rule');
            $qb->where("rule.triggerEvent = :classEventName");
            $qb->setParameter(':classEventName', $eventClassName)
                ->orderBy('rule.priority', 'ASC');

            foreach ($this->ruleExtensions as $extension) {
                if (!$extension->supportExtension($eventClassName)) {
                    continue;
                }
                $extension->updateQueryBuilder($qb, $this->managerRegistry, $eventClassName);
            }

            $ruleEntities = array_merge($ruleEntities, $qb->getQuery()->getResult());
        }

        return $ruleEntities;
    }
}
