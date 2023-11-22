<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\Process;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use DrinksIt\RuleEngineBundle\Rule\PlainFieldRuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;

final class PlainRuleEntityProcess implements ProcessorInterface
{
    public function __construct(
        private SerializerActionsFieldInterface $serializerActionsField,
        private SerializerConditionsFieldInterface $serializerConditionsField
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PlainFieldRuleEntityInterface
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $data;
        }

        if (!$data instanceof PlainFieldRuleEntityInterface) {
            return $data;
        }

        $context = array_merge($context, [
            'rule_entity_class_name' => $data::class,
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
            $data->setTriggerEvent(
                new TriggerEventColumn($event)
            );
        }

        return $data;
    }
}
