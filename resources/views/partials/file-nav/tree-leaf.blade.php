@php
    $currentQuery = request()->query();
    $currentQuery['markdownNavSelected'] = $filePath;
    $queryString = http_build_query($currentQuery);
@endphp
<a href="?{{$queryString}}" wire:click.prevent="viewDoc('{{$filePath}}')" title="{{$filePath}}" class="file-link">
    {{ \Str::of($entry)->before('.md')->replace('-', ' ')->title() }}
</a>
