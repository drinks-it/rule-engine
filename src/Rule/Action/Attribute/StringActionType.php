<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action\Attribute;

use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;
use DrinksIt\RuleEngineBundle\Rule\Action\Action;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\Types\StringActionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\MethodDoesNotExistException;

class StringActionType extends Action implements StringActionTypeInterface
{
    protected array $actionsFields = [
        'pattern' => '',
        'macros' => [],
    ];

    public static function getType(): string
    {
        return 'string';
    }

    public function decodeAction($action): ActionInterface
    {
        if (!\is_string($action)) {
            throw new \RuntimeException('Parameter `$action` must be type is string');
        }

        $this->actionsFields['pattern'] = $action;
        preg_match_all('/(?<macros>\%[a-z0-9_.]+\%)/i', $action, $matches);

        if ($matches['macros']) {
            $this->actionsFields['macros'] = $matches['macros'];
        }

        return $this;
    }

    public function executeAction($objectEntity)
    {
        $setMethodNameField = StrEntity::getSetterNameMethod($this->getFieldName());

        $objectToSet = $objectEntity;

        if (!method_exists($objectToSet, $setMethodNameField)) {
            $methodGetResource = StrEntity::getGetterNameMethod(
                StrEntity::getShortName($this->getResourceClass())
            );

            if (method_exists($objectEntity, $methodGetResource)) {
                $objectToSet = $objectEntity->{$methodGetResource}();
            }
        }

        if (!method_exists($objectToSet, $setMethodNameField)) {
            throw new MethodDoesNotExistException($objectToSet::class, $setMethodNameField);
        }

        $resultSet = $this->actionsFields['pattern'];
        foreach ($this->actionsFields['macros'] as $macro) {
            $resultSet = str_replace($macro, $this->getValueFromObjectByMacros($objectEntity, $macro), $resultSet);
        }

        $resultSet = $this->normalizeResult($resultSet, $objectToSet::class, $this->getFieldName());

        $objectToSet->{$setMethodNameField}($resultSet);

        return $objectEntity;
    }

    public function getAction()
    {
        return $this->actionsFields;
    }

    public function setAction($action): ActionInterface
    {
        if (!\is_array($action)) {
            throw new \RuntimeException('Argument `$action` must be type array with keys pattern, macros');
        }

        if (!isset($action['pattern']) || !isset($action['macros'])) {
            throw new \RuntimeException('Argument `$action` must be type array with keys pattern, macros');
        }

        $this->actionsFields = $action;

        return $this;
    }

    public function getPatternExecute(): string
    {
        return $this->actionsFields['pattern'];
    }

    public function validateExecutedAction(): bool
    {
        $resultSet = $this->actionsFields['pattern'];
        foreach ($this->actionsFields['macros'] as $macro) {
            $resultSet = str_replace($macro, "[any_{$macro}]", $resultSet);
        }

        foreach ($this->actionsFields['macros'] as $macro) {
            $resultSet = str_replace("[any_{$macro}]", $macro, $resultSet);
        }

        return $resultSet === $this->actionsFields['pattern'];
    }
}
