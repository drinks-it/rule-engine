<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition;

use Doctrine\Common\Collections\ArrayCollection;
use DrinksIt\RuleEngineBundle\Rule\ConditionsInterface;

class CollectionCondition extends ArrayCollection implements ConditionsInterface
{
}
