<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule;

use DrinksIt\RuleEngineBundle\Serializer\NormalizerPropertyInterface;

interface ActionPropertyNormalizerInterface
{
    public function setNormalizer(NormalizerPropertyInterface $normalizerProperty): self;
}
