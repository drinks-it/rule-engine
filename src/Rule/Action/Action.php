<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action;

use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;

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
            $pathMap =explode('.', $pathToField);
            $relationMethodName = array_shift($pathMap);
            $methodRelationName = StrEntity::getGetterNameMethod($relationMethodName);

            if (!method_exists($objectEntity, $methodRelationName)) {
                // todo
                throw new \RuntimeException('Method not found');
            }
            $objectEntity = $objectEntity->{$methodRelationName}();

            return $this->getValueFromObjectByMacros($objectEntity, implode('.', $pathMap));
        }

        if (mb_strtolower($pathToField) === 'self') {
            $pathToField = $this->getFieldName();
        }
        $methodName = StrEntity::getGetterNameMethod($pathToField);

        if (!method_exists($objectEntity, $methodName)) {
            // todo
            throw new \RuntimeException('Method not found ' .$methodName);
        }

        return (string) $objectEntity->{$methodName}();
    }
}
