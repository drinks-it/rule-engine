<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use DrinksIt\RuleEngineBundle\Rule\PlainFieldRuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;

final class PlainRuleEntityDataPersister implements ContextAwareDataPersisterInterface, ResumableDataPersisterInterface
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
    public function supports($data, array $context = []): bool
    {
        return $data instanceof PlainFieldRuleEntityInterface;
    }

    /**
     * @param PlainFieldRuleEntityInterface $data
     * @inheritDoc
     */
    public function persist($data, array $context = [])
    {
        $context = array_merge($context, [
            'rule_entity_class_name' => \get_class($data),
            'rule_default_resource' => null,
        ]);

        if ($defaultResource = $data->getDefaultResourceClassName()) {
            $context['rule_default_resource'] = $defaultResource;
        }

        if ($conditions = $data->getPlainConditions()) {
            $data->setConditions(
                $this->serializerConditionsField->decodeToConditionCollection($conditions, $context)
            );
        }

        if ($actions = $data->getPlainActions()) {
            $data->setActions(
                $this->serializerActionsField->decodeToActionCollection($actions, $context)
            );
        }

        if ($event = $data->getPlainTriggerEvent()) {
            $data->setTriggerEvent(new TriggerEventColumn($event));
        }

        $data->clearPlains();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        return $data;
    }

    public function resumable(array $context = []): bool
    {
        return true;
    }
}
