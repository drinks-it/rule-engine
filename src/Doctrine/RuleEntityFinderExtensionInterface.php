<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

interface RuleEntityFinderExtensionInterface
{
    public function updateQueryBuilder(QueryBuilder $builder, ManagerRegistry $managerRegistry, string $ruleEventClassName);

    public function supportExtension(string $ruleEventClassName): bool;
}
