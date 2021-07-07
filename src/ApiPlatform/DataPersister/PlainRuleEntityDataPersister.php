<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use DrinksIt\RuleEngineBundle\Rule\PlainFieldRuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;

final class PlainRuleEntityDataPersister implements DataPersisterInterface, ResumableDataPersisterInterface
{
    private SerializerActionsFieldInterface $serializerActionsField;

    private SerializerConditionsFieldInterface $serializerConditionsField;

    public function __construct(
        SerializerActionsFieldInterface $serializerActionsField,
        SerializerConditionsFieldInterface $serializerConditionsField
    ) {
        $this->serializerActionsField = $serializerActionsField;
        $this->serializerConditionsField = $serializerConditionsField;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof PlainFieldRuleEntityInterface;
    }

    /**
     * @param PlainFieldRuleEntityInterface $data
     * @inheritDoc
     */
    public function persist($data)
    {
        if ($conditions = $data->getPlainConditions()) {
            $data->setConditions(
                $this->serializerConditionsField->decodeToConditionCollection($conditions)
            );
        }

        if ($actions = $data->getPlainActions()) {
            $data->setActions(
                $this->serializerActionsField->decodeToActionCollection($actions)
            );
        }

        if ($event = $data->getPlainTriggerEvent()) {
            $data->setTriggerEvent(new TriggerEventColumn($event));
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function remove($data)
    {
        return $data;
    }

    public function resumable(array $context = []): bool
    {
        return true;
    }
}
