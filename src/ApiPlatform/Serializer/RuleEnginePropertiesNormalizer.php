<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class RuleEnginePropertiesNormalizer implements NormalizerInterface, SerializerAwareInterface, DenormalizerInterface
{
    private SerializerConditionsFieldInterface $serializerConditionsField;

    private SerializerActionsFieldInterface $serializerActionsField;

    /**
     * @var NormalizerInterface|DenormalizerInterface
     */
    private $decorated;

    private RuleEventListenerFactoryInterface $eventListenerFactory;

    public function __construct(
        $decorated,
        SerializerConditionsFieldInterface $serializerConditionsField,
        SerializerActionsFieldInterface $serializerActionsField,
        RuleEventListenerFactoryInterface $eventListenerFactory
    ) {
        $this->decorated = $decorated;
        $this->serializerConditionsField = $serializerConditionsField;
        $this->serializerActionsField = $serializerActionsField;
        $this->eventListenerFactory = $eventListenerFactory;
    }

    /**
     * @param mixed $object
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): mixed
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

        if (!$this->decorated instanceof NormalizerInterface) {
            return null;
        }

        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, string $format = null): bool
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

        if ($this->decorated instanceof NormalizerInterface) {
            return $this->decorated->supportsNormalization($data, $format);
        }

        return false;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): mixed
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

        if ($this->decorated instanceof DenormalizerInterface) {
            return $this->decorated->denormalize($data, $type, $format, $context);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
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

        if ($this->decorated instanceof DenormalizerInterface) {
            return $this->decorated->supportsDenormalization($data, $type, $format);
        }

        return false;
    }
}
