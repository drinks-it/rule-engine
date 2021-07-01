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
use DrinksIt\RuleEngineBundle\Rule\Action\Types\NumberActionTypeInterface;

class NumberActionType extends Action implements NumberActionTypeInterface
{
    protected array $actionsFields = [
        'math' => null,
        'macros' => [],
    ];

    public function getActionType(): string
    {
        return 'number';
    }

    /**
     * @inheritdoc
     */
    public function decodeAction($action): self
    {
        if (!\is_string($action)) {
            throw new \RuntimeException('Argument `$action` must be type is string');
        }
        $this->actionsFields['math'] = str_replace(
            NumberActionTypeInterface::OPERATION_OF_X,
            NumberActionTypeInterface::OPERATION_OF,
            $action
        );

        preg_match_all('/(?<macros>\%[a-z0-9._]+\%)/i', $this->actionsFields['math'], $matchResults);

        if ($matchResults['macros']) {
            $this->actionsFields['macros'] = array_fill_keys($matchResults['macros'] ?? [], null);
        }

        return $this;
    }

    public function executeAction($objectEntity)
    {
        $methodSetField = StrEntity::getSetterNameMethod($this->getFieldName());

        if (!method_exists($objectEntity, $methodSetField)) {
            // todo
            throw new \RuntimeException('Method not found');
        }

        if ($this->actionsFields['macros']) {
            foreach ($this->actionsFields['macros'] as $pathToField => $val) {
                $this->actionsFields['math'] = str_replace(
                    $pathToField,
                    $this->getValueFromObjectByMacros($objectEntity, $pathToField),
                    $this->actionsFields['math']
                );
            }
        }
        $objectEntity->{$methodSetField}(math_eval($this->actionsFields['math']));

        return $objectEntity;
    }

    public function getAction()
    {
        return $this->actionsFields;
    }

    public function setAction($action): ActionInterface
    {
        if (!\is_array($action)) {
            throw new \RuntimeException('Argument `$action` must be type array with keys math, macros');
        }

        if (!isset($action['math']) || !isset($action['macros'])) {
            throw new \RuntimeException('Argument `$action` must be type array with keys math, macros');
        }

        $this->actionsFields = $action;

        return $this;
    }
}
