<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action\Attribute;

use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;
use DrinksIt\RuleEngineBundle\Rule\Action\Action;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\Types\StringActionTypeInterface;

class StringActionType extends Action implements StringActionTypeInterface
{
    protected array $actionsFields = [
        'pattern' => '',
        'macros' => [],
    ];

    public function getActionType(): string
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

    public function executeAction($objectEntity): void
    {
        $setMethodNameField = StrEntity::getSetterNameMethod($this->getFieldName());

        if (!method_exists($objectEntity, $setMethodNameField)) {
            // todo
            throw new \RuntimeException('Method not found');
        }
        $resultSet = $this->actionsFields['pattern'];
        foreach ($this->actionsFields['macros'] as $macro) {
            $resultSet = str_replace($macro, $this->getValueFromObjectByMacros($objectEntity, $macro), $resultSet);
        }

        $objectEntity->{$setMethodNameField}($resultSet);
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
}
