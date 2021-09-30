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
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\MethodDoesNotExistException;
use DrinksIt\RuleEngineBundle\Rule\Exception\TypeArgumentRuleException;

class NumberActionType extends Action implements NumberActionTypeInterface
{
    protected array $actionsFields = [
        'math' => null,
        'macros' => [],
    ];

    public static function getType(): string
    {
        return 'number';
    }

    /**
     * @inheritdoc
     */
    public function decodeAction($action): self
    {
        if (!\is_string($action)) {
            throw new TypeArgumentRuleException(\gettype($action), static::class, 'decodeAction', 'string');
        }
        $this->actionsFields['math'] = $action;

        preg_match_all('/(?<macros>\%[a-z0-9._]+\%)/i', $this->actionsFields['math'], $matchResults);

        if ($matchResults['macros']) {
            $this->actionsFields['macros'] = array_fill_keys($matchResults['macros'] ?? [], null);
        }

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
            throw new MethodDoesNotExistException(\get_class($objectToSet), $methodSetField);
        }

        if ($this->actionsFields['macros']) {
            foreach ($this->actionsFields['macros'] as $pathToField => $val) {
                $valDecoded = $this->getValueFromObjectByMacros($objectEntity, $pathToField);

                if (!\is_array($valDecoded)) {
                    $valDecoded = (string) $valDecoded;
                }
                $this->actionsFields['math'] = str_replace($pathToField, $valDecoded, $this->actionsFields['math']);
            }
        }

        $value = $this->normalizeResult(math_eval($this->actionsFields['math']), \get_class($objectToSet), $this->getFieldName());

        $objectToSet->{$methodSetField}($value);

        return $objectEntity;
    }

    public function getAction()
    {
        return $this->actionsFields;
    }

    public function setAction($action): ActionInterface
    {
        if (!\is_array($action)) {
            throw new TypeArgumentRuleException(\gettype($action), static::class, 'setAction', 'array');
        }

        if (!isset($action['math']) || !isset($action['macros'])) {
            throw new \RuntimeException('Argument `$action` must be type array with keys math, macros');
        }

        $this->actionsFields = $action;

        return $this;
    }

    public function getPatternExecute(): string
    {
        return $this->actionsFields['math'];
    }
}
