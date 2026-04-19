@php
    $isDirectory = count($children) > 0;
    $isSelected = !$isDirectory && $filePath === $markdownNavSelected;
    /**
     * @see resources/views/partials/file-nav/tree-branch.blade.php
     * @see resources/views/partials/file-nav/tree-leaf.blade.php
     */
    $view = $isDirectory ? 'tree-branch' : 'tree-leaf';
@endphp
<li class="{{ $isDirectory ? 'directory' : 'file' }} {{ $isSelected ? 'selected-file' : '' }}">
    @include("livewire-markdown-navigator::partials.file-nav.$view", [
        'entry' => $entry,
        'filePath' => $filePath,
        'children' => collect($children)->filter(function($child, $key) {
            // Filter out empty directories
            return $key !== ':path' && !(is_array($child) && count($child) === 0) ;
        }),
        'docPath' => $docPath,
        'markdownNavSelected' => $markdownNavSelected,
    ])
</li>
