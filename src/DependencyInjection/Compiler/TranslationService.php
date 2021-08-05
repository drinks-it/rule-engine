<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\DependencyInjection\Compiler;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TranslationService implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('translator.default')) {
            return;
        }
        $basePath = __DIR__ . '/../../Resources/translation';
        $translationFiles = array_filter(scandir($basePath), fn ($item) => !\in_array($item, ['.', '..']));

        if (!$translationFiles) {
            return;
        }

        /** @var Translator $translationExtractor */
        $translationExtractor = $container->get('translator.default');

        if (!$translationExtractor) {
            return;
        }

        foreach ($translationFiles as $translationFileName) {
            [$domain, $locale, $format] = explode('.', $translationFileName);
            $translationExtractor->addResource($format, $basePath . DIRECTORY_SEPARATOR . $translationFileName, $locale, $domain);
        }
    }
}
