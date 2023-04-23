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
use DrinksIt\RuleEngineBundle\Rule\Action\Types\BooleanActionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\MethodDoesNotExistException;
use DrinksIt\RuleEngineBundle\Rule\Exception\TypeArgumentRuleException;

class BooleanActionType extends Action implements BooleanActionTypeInterface
{
    protected bool $actionsSavedField = false;

    public static function getType(): string
    {
        return 'boolean';
    }

    /**
     * @inheritDoc
     */
    public function decodeAction($action): ActionInterface
    {
        if (!$action) {
            $this->actionsSavedField = false;

            return $this;
        }

        if (\is_string($action)) {
            if (mb_strtolower($action) === 'false') {
                $this->actionsSavedField = false;

                return $this;
            }

            if (mb_strtolower($action) === 'true') {
                $this->actionsSavedField = true;

                return $this;
            }
        }

        if (!\is_bool($action) && !is_numeric($action)) {
            throw new TypeArgumentRuleException(\gettype($action), static::class, 'decodeAction', 'boolean, number or (empty)');
        }

        $this->actionsSavedField = (bool) $action;

        return $this;
    }

    public function getAction()
    {
        return $this->actionsSavedField;
    }

    public function setAction($action): ActionInterface
    {
        $this->actionsSavedField = (bool) $action;

        return $this;
    }

    public function executeAction($objectEntity)
    {
        $methodSetField = StrEntity::getSetterNameMethod($this->getFieldName());
        $objectToSet = $objectEntity;

        if (!method_exists($objectToSet, $methodSetField)) {
            $methodGetResource = StrEntity::getGetterNameMethod(
                StrEntity::getShortName($this->getResourceClass())
            );

            if (method_exists($objectEntity, $methodGetResource)) {
                $objectToSet = $objectEntity->{$methodGetResource}();
            }
        }

        if (!method_exists($objectToSet, $methodSetField)) {
            throw new MethodDoesNotExistException($objectToSet::class, $methodSetField);
        }

        $objectToSet->{$methodSetField}(
            $this->normalizeResult($this->actionsSavedField, $objectToSet::class, $this->getFieldName())
        );

        return $objectEntity;
    }

    public function getPatternExecute(): string
    {
        return $this->actionsSavedField ? 'true' : 'false';
    }

    public function validateExecutedAction(): bool
    {
        return true;
    }
}
