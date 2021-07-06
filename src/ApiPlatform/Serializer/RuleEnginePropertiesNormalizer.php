<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\Serializer;

use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RuleEnginePropertiesNormalizer implements NormalizerInterface
{
    private SerializerConditionsFieldInterface $serializerConditionsField;

    private SerializerActionsFieldInterface $serializerActionsField;

    private NormalizerInterface $decorated;

    public function __construct(
        NormalizerInterface $decorated,
        SerializerConditionsFieldInterface $serializerConditionsField,
        SerializerActionsFieldInterface $serializerActionsField
    ) {
        $this->decorated = $decorated;
        $this->serializerConditionsField = $serializerConditionsField;
        $this->serializerActionsField = $serializerActionsField;
    }

    /**
     * @param mixed $object
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof Condition) {
            return $this->serializerConditionsField->encodeItemConditionToArray($object, $context);
        }

        if ($object instanceof CollectionConditionInterface) {
            return $this->serializerConditionsField->encodeCollectionConditionToArray($object, $context);
        }

        if ($object instanceof ActionInterface) {
            return $this->serializerActionsField->encodeActionToArray($object, $context);
        }

        if ($object instanceof CollectionActionsInterface) {
            return $this->serializerActionsField->encodeActionCollectionToArray($object, $context);
        }

        if ($object instanceof TriggerEventColumn) {
            return $object->getEntityClassName();
        }

        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, string $format = null)
    {
        if ($data instanceof Condition || $data instanceof CollectionConditionInterface) {
            return true;
        }

        if ($data instanceof ActionInterface || $data instanceof CollectionActionsInterface) {
            return true;
        }

        if ($data instanceof TriggerEventColumn) {
            return true;
        }

        return $this->decorated->supportsNormalization($data, $format);
    }
}
