<?php

namespace Perturbatio\LivewireMarkdownNavigator\Actions;

use Cache;
use Exception;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkExtension;

/**
 * @phpstan-import-type DocLinkContext from DocLinkExtension
 */
class CacheRenderedFileAction
{
    /**
     * @param  array{doc_link: array{context: DocLinkContext}}  $commonMarkOptions
     *
     * @throws Exception
     */
    public static function execute(string $file, string $diskName, int $cacheDuration, array $commonMarkOptions): string
    {

        $cacheKey = "livewire-markdown-navigator:{$diskName}:{$file}";

        return Cache::remember($cacheKey, $cacheDuration, function () use ($commonMarkOptions, $file, $diskName) {
            return RenderFileAction::execute($file, $diskName, $commonMarkOptions);
        });
    }
}
