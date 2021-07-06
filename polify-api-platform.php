<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

if (!interface_exists('\ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface')) {
    eval(<<<CODE
        namespace ApiPlatform\Core\DataPersister;

        /**
         * Control the resumability of the data persister chain.
         */
        interface ResumableDataPersisterInterface
        {
            /**
             * Should we continue calling the next DataPersister or stop after this one?
             * Defaults to stop the ChainDatapersister if this interface is not implemented.
             */
            public function resumable(array \$context = []): bool;
        }
        CODE);
}
