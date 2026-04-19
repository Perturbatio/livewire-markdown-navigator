<?php

namespace Perturbatio\LivewireMarkdownNavigator\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\Component;

/**
 * @phpstan-type FileTree array<string, mixed>
 */
class FileNav extends Component
{
    /** @var FileTree */
    private array $fileTree = [];

    public function __construct(
        public \Livewire\Component $markdownNavigator,
        public string $docPath,
        public ?string $markdownNavSelected,
        public int $startingDepth,
    ) {
        $this->setFileTree($markdownNavigator->files ?? []);
        $this->startingDepth = max(0, $this->startingDepth);
    }

    public function render(): View
    {
        // using a ViewFactory to work around larastan's view-string causing problems in github actions
        /* @see /resources/views/components/file-nav.blade.php */
        return ViewFactory::make('livewire-markdown-navigator::components.file-nav', [
            'fileTree' => $this->fileTree,
        ]);
    }

    /**
     * @param  list<string>  $fileTree
     */
    protected function setFileTree(array $fileTree): void
    {
        $this->fileTree = $this->buildFileTree($fileTree, $this->startingDepth);
    }

    /**
     * @param  list<string>  $files
     * @return FileTree
     */
    protected function buildFileTree(array $files, int $startingDepth): array
    {
        $startingDepth = max(0, $startingDepth);
        /** @var FileTree $nested */
        $nested = [];

        foreach ($files as $file) {
            $parts = array_slice(explode('/', $file), $startingDepth);
            $this->appendFilePath($nested, $parts, $file);
        }

        return $nested;
    }

    /**
     * @param  FileTree  $tree
     * @param  list<string>  $parts
     */
    private function appendFilePath(array &$tree, array $parts, string $file): void
    {
        if ($parts === []) {
            return;
        }

        $part = $parts[0];
        $remainingParts = array_slice($parts, 1);

        if (! array_key_exists($part, $tree) || ! is_array($tree[$part])) {
            $tree[$part] = [];
        }

        /** @var FileTree $branch */
        $branch = $tree[$part];

        $this->appendFilePath($branch, $remainingParts, $file);
        $path = empty($branch) ? [':path' => $file] : [];
        $tree[$part] = array_merge($path, $branch);
    }
}
