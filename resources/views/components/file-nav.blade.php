<ul {{ $attributes->class(['file-nav']) }}>
    @foreach($markdownNavigator->files as $file)
        {{-- recursively render the files in a tree-like structure, separating by directory--}}
        @php
            $relativePath = str_replace($docPath . '/', '', $file);
            $parts = explode('/', $relativePath);
            $fileName = \Str::of(array_pop($parts))->before('.md')->replace('-', ' ')->title();
            $indent = count($parts) * 1.25; // Indent based on directory depth
            // get the second last part of the path as the directory name, if it exists
            $directory = count($parts) > 0 ? $parts[count($parts) - 1] : null;
            if ($directory && $directory !== $lastDirectory) {
        @endphp
        <li class="directory">
            <div class="directory-inner"
                 style="padding-left: calc({{count($parts)}} * 0.5rem);">{{\Str::of($directory)->replace('-', ' ')->title()}}</div>
        </li>
        @php
            }
            $lastDirectory = $directory;
        @endphp
        <li class="file {{ $file === $markdownNavSelected ? 'selected-file' : '' }}">
            @if($file !== $markdownNavSelected)
                @php
                    $currentQuery = request()->query();
                    $currentQuery['markdownNavSelected'] = $file;
                    $queryString = http_build_query($currentQuery);
                @endphp
                <a href="#" wire:click.prevent="viewDoc('{{$file}}')" title="{{$file}}"
                   class="file-link">
                                    <span class="inline-block"
                                          style="padding-left: calc({{ $indent }}rem);">{{$fileName}}</span>
                </a>
            @else
                <div class="selected-file">
                                    <span class="inline-block"
                                          style="padding-left: calc({{ $indent }}rem);">{{$fileName}}</span>
                </div>
            @endif
        </li>
    @endforeach
</ul>
