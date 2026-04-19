<ul {{ $attributes->class([
    'can-collapse' => $collapseChildren,
]) }}>
    @foreach($fileTree as $entry => $children)
        @php
            $filePath = collect($children)->get(':path', $entry); // Get the path for leaf nodes, default to entry name for directories
            // if the $markdownNavSelected is not in the current branch, we want to collapse the children
        @endphp
        @include('livewire-markdown-navigator::partials.file-nav.tree-root', [
            'entry' => $entry,
            'filePath' => $filePath, // Pass the path for leaf nodes
            'children' => collect($children)->filter(function($child, $key) {
                // Filter out empty directories
                return $key !== ':path' && !(is_array($child) && count($child) === 0) ;
            }),
            'docPath' => $docPath,
            'markdownNavSelected' => $markdownNavSelected,
            'collapseChildren'=> $collapseChildren
                && $markdownNavSelected
                && !Str::startsWith($markdownNavSelected, $docPath . '/' . $filePath),
        ])
    @endforeach
</ul>
