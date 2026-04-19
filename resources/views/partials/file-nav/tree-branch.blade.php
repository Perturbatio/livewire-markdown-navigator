
<div class="directory-container {{ $markdownNavSelected === $docPath . '/' . $entry ? 'selected' : '' }}">
    <div class="directory-title">{{ \Str::of($entry)->before('.md')->replace('-', ' ')->title() }}</div>
    <ul class="directory--children">
        @foreach($children as $childEntry => $grandChildren)
            @php
                $filePath = collect($grandChildren)->get(':path', $childEntry); // Get the path for leaf nodes, default to entry name for directories
                $filteredChildren = collect($grandChildren)->filter(function($child, $key) {
                    // Filter out empty directories
                    return $key !== ':path' && !(is_array($child) && count($child) === 0) ;
                });
                $isDirectory = count($filteredChildren) > 0;
                $isSelected = !$isDirectory && $filePath === $markdownNavSelected;

                /**
                 * @see resources/views/partials/file-nav/tree-branch.blade.php
                 * @see resources/views/partials/file-nav/tree-leaf.blade.php
                 */
                $view = $isDirectory ? 'tree-branch' : 'tree-leaf';
            @endphp
            <li class="{{ $isDirectory ? 'directory' : 'file' }} {{ $isSelected ? 'selected-file' : '' }}">
                @include("livewire-markdown-navigator::partials.file-nav.$view", [
                    'entry' => $childEntry,
                    'filePath' => $filePath,
                    'children' => $filteredChildren,
                    'docPath' => $docPath,
                    'markdownNavSelected' => $markdownNavSelected,
                ])
            </li>
        @endforeach
    </ul>
</div>
