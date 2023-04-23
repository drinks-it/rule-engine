<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action;

use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;
use DrinksIt\RuleEngineBundle\Rule\ActionPropertyNormalizerInterface;
use DrinksIt\RuleEngineBundle\Rule\Exception\MethodDoesNotExistRuleException;
use DrinksIt\RuleEngineBundle\Serializer\NormalizerPropertyInterface;

abstract class Action implements ActionInterface, ActionPropertyNormalizerInterface
{
    protected ?NormalizerPropertyInterface $normalizerProperty = null;

    protected string $fieldName;

    protected string $resourceClass;

    public function __construct(string $fieldName, string $resourceClass, $action = null)
    {
        $this->fieldName = $fieldName;
        $this->resourceClass = $resourceClass;

        if ($action) {
            $this->decodeAction($action);
        }
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }

    protected function getValueFromObjectByMacros($objectEntity, $pathToField)
    {
        $pathToField = str_replace('%', '', $pathToField);

        if (strpos($pathToField, '.')) {
            $pathMap = explode('.', $pathToField);
            $objectFromGet = $objectEntity;
            $classNameReturned = $objectFromGet::class;
            $relationNameField = $pathToField;
            foreach ($pathMap as $relationMethodName) {
                if (!\is_object($objectFromGet)) {
                    break;
                }
                $classNameReturned = $objectFromGet::class;
                $relationNameField = $relationMethodName;
                $methodRelationName = StrEntity::getGetterNameMethod($relationMethodName);

                if (!method_exists($objectFromGet, $methodRelationName)) {
                    throw new MethodDoesNotExistRuleException($classNameReturned, $methodRelationName);
                }
                $objectFromGet = $objectFromGet->{$methodRelationName}();
            }

            return $this->normalizerProperty->normalize($objectFromGet, $classNameReturned, $relationNameField, [
                'type' => 'decode_macros',
                'path_field' => $pathToField,
                'source_entity' => $objectEntity::class,
                'action' => $this,
            ]);
        }

        if (mb_strtolower($pathToField) === 'self') {
            $pathToField = $this->getFieldName();
        }
        $methodName = StrEntity::getGetterNameMethod($pathToField);

        if (!method_exists($objectEntity, $methodName)) {
            throw new MethodDoesNotExistRuleException($objectEntity::class, $methodName);
        }

        $value = (string) $objectEntity->{$methodName}();

        if (!$this->normalizerProperty) {
            return $value;
        }

        return $this->normalizerProperty->normalize($value, $objectEntity::class, $pathToField, [
            'type' => 'decode_macros',
            'path_field' => $pathToField,
            'source_entity' => $objectEntity::class,
            'action' => $this,
        ]);
    }

    protected function normalizeResult($value, string $resourceClass, string $propertyName)
    {
        if (!$this->normalizerProperty) {
            return $value;
        }

        return $this->normalizerProperty->normalize($value, $resourceClass, $propertyName, [
            'type' => 'set_action_value',
            'action' => $this,
        ]);
    }

    public function setNormalizer(NormalizerPropertyInterface $normalizerProperty): self
    {
        $this->normalizerProperty = $normalizerProperty;

        return $this;
    }
}
