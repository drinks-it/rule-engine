<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action;

/**
 * Interface ActionInterface
 * @package DrinksIt\RuleEngineBundle\Rule\Action

 */
interface ActionInterface
{
    public function __construct(string $fieldName, string $resourceClass, $action = null);

    public function getFieldName(): string;

    public function getResourceClass(): string;

    public static function getType(): string;
    /**
     * @param mixed $action
     */
    public function decodeAction($action): self;

    public function getAction();

    public function setAction($action): self;

    public function executeAction($objectEntity);

    public function getPatternExecute(): string;

    public function validateExecutedAction(): bool;
}
