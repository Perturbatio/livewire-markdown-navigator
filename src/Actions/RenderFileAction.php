<?php

namespace Perturbatio\LivewireMarkdownNavigator\Actions;

use Exception;
use Illuminate\Support\Facades\Storage;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer;

/**
 * @phpstan-import-type DocLinkContext from DocLinkExtension
 */
class RenderFileAction
{
    /**
     * @param  array{doc_link: array{context: DocLinkContext}}  $commonMarkOptions
     *
     * @throws Exception
     */
    public static function execute(string $file, string $diskName, array $commonMarkOptions): string
    {
        $permittedDisks = config('livewire-markdown-navigator.permitted_disks', []);

        if (empty($permittedDisks) || ! is_array($permittedDisks)) {
            throw new Exception('No disks configured.');
        }

        if (! in_array($diskName, $permittedDisks, true)) {
            throw new Exception("Disk [$diskName] is not permitted.");
        }

        if (! Storage::disk($diskName)->exists($file)) {
            throw new Exception("File [$file] does not exist on disk [$diskName].");
        }

        return app(MarkdownRenderer::class)
            ->addExtension(app(DocLinkExtension::class))
            ->commonmarkOptions($commonMarkOptions)
            ->toHtml(Storage::disk($diskName)->get($file) ?? '');
    }
}
