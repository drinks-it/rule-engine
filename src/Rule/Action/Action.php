<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action;

use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;
use DrinksIt\RuleEngineBundle\Rule\Exception\MethodDoesNotExistRuleException;

abstract class Action implements ActionInterface
{
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
            foreach ($pathMap as $relationMethodName) {
                if (!\is_object($objectFromGet)) {
                    break;
                }

                $methodRelationName = StrEntity::getGetterNameMethod($relationMethodName);

                if (!method_exists($objectFromGet, $methodRelationName)) {
                    throw new MethodDoesNotExistRuleException(\get_class($objectFromGet), $methodRelationName);
                }
                $objectFromGet = $objectFromGet->{$methodRelationName}();
            }

            return $objectFromGet;
        }

        if (mb_strtolower($pathToField) === 'self') {
            $pathToField = $this->getFieldName();
        }
        $methodName = StrEntity::getGetterNameMethod($pathToField);

        if (!method_exists($objectEntity, $methodName)) {
            throw new MethodDoesNotExistRuleException(\get_class($objectEntity), $methodName);
        }

        return (string) $objectEntity->{$methodName}();
    }
}
