<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\Serializer;

use DrinksIt\RuleEngineBundle\Event\Factory\RuleEventListenerFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RuleEnginePropertiesNormalizer implements DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private SerializerConditionsFieldInterface $serializerConditionsField,
        private SerializerActionsFieldInterface $serializerActionsField,
        private RuleEventListenerFactoryInterface $eventListenerFactory
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function normalize(mixed $object, string $format = null, array $context = []): mixed
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

        throw new \RuntimeException('Not supported normalize object');
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
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

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        if (TriggerEventColumn::class === $type) {
            foreach ($this->eventListenerFactory->create() as $eventMetaData) {
                if ($eventMetaData['resource'] !== $data) {
                    continue;
                }

                return new TriggerEventColumn($eventMetaData['resource']);
            }
        }

        if (ActionInterface::class.'[]' === $type) {
            return $this->serializerActionsField->decodeToActionCollection($data);
        }

        if (Condition::class.'[]' === $type) {
            return $this->serializerConditionsField->decodeToConditionCollection($data);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        if (TriggerEventColumn::class === $type) {
            return true;
        }

        if (ActionInterface::class.'[]' === $type) {
            return true;
        }

        if (Condition::class.'[]' === $type) {
            return true;
        }

        return false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Condition::class.'[]' => true,
            CollectionActionsInterface::class => true,
            CollectionConditionInterface::class => true,
            ActionInterface::class.'[]' => true,
            TriggerEventColumn::class => true,
        ];
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}
