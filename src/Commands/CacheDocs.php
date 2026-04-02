<?php

namespace Perturbatio\LivewireMarkdownNavigator\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Perturbatio\LivewireMarkdownNavigator\Actions\CacheRenderedFileAction;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer;

/**
 * @phpstan-import-type DocLinkContext from DocLinkExtension
 *
 * @phpstan-type RendererConfig array{
 *   class: string,
 *   renderer: string,
 *   priority: int
 * }
 * @phpstan-type ParserConfig array{
 *   class: string,
 *   priority: int
 * }
 */
#[Signature('livewire-markdown-navigator:cache-docs {--disk=} {--cacheDuration=1440} {--force}')]
#[Description('Prime the cache with the available docs (pre-generates the HTML versions of the markdown)')]
class CacheDocs extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cacheDuration = (int) $this->option('cacheDuration');
        $permittedDisks = config('livewire-markdown-navigator.permitted_disks', []);

        if (empty($permittedDisks) || ! is_array($permittedDisks)) {
            $this->fail('No disks configured.');
        }
        /** @var string $disk */
        $disk = $this->option('disk');
        $force = ! empty($this->option('force'));

        if (! empty($disk)) {
            if (! in_array($disk, $permittedDisks)) {
                $this->fail("Disk [$disk] not permitted.");
            }
        }

        $selectedDisks = $disk ? [$disk] : $permittedDisks;

        foreach ($selectedDisks as $diskName) {
            if (! is_string($diskName)) {
                $this->fail('Invalid disk name in configuration');
            }
            // get the root dirs in the disk
            $rootDirs = Storage::disk($diskName)->directories();
            foreach ($rootDirs as $rootDir) {
                $this->info('Caching for "'.$diskName.DIRECTORY_SEPARATOR.$rootDir.'"');
                /** @var array<int, string> $allFiles */
                $allFiles = collect(Storage::disk($diskName)->allFiles($rootDir))
                    ->filter(fn (string $file) => \Str::of($file)->endsWith('.md'))
                    ->values()
                    ->toArray();
                foreach ($allFiles as $file) {
                    $cacheKey = "livewire-markdown-navigator:{$diskName}:{$file}";
                    if ($force && Cache::has($cacheKey)) {
                        Cache::forget($cacheKey);
                    }
                    CacheRenderedFileAction::execute($file, $diskName, $cacheDuration, $this->getCommonMarkOptions($rootDir, $diskName));
                }
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return array{doc_link: array{context: DocLinkContext}}
     */
    protected function getCommonMarkOptions(string $docPath, string $diskName): array
    {
        $docLinkOptions = [
            'doc_link' => [
                'context' => [
                    'doc_path' => $docPath,
                    'disk_name' => $diskName,
                    'current_file' => null,
                ],
            ],
        ];
        /** @var array{string:mixed} $baseConfig */
        $baseConfig = config('livewire-markdown-navigator.commonmark_options', []);

        /** @var array{doc_link: array{context: DocLinkContext}} */
        return array_merge_recursive($baseConfig, $docLinkOptions);
    }
}
