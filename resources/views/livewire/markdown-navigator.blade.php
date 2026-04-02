<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Perturbatio\LivewireMarkdownNavigator\Actions\CacheRenderedFileAction;
use Perturbatio\LivewireMarkdownNavigator\Actions\RenderFileAction;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Spatie\LaravelMarkdown\MarkdownBladeComponent;

new class extends Component {
    public string $docPath;
    public string $content = '';
    public int $cacheDuration;
    #[Url]
    public ?string $markdownNavSelected = null;
    public string $diskName;
    protected array $config = [];

    public function mount(
        string $docPath,
        null|string $diskName = null,
        int $cacheDuration = 60 * 24, // cache for 24 hours by default
        null|string $defaultFile = null,
    ): void {

        $this->config = config('livewire-markdown-navigator');
        $this->docPath = $docPath;

        $diskName = $diskName ?? $this->getConfig('default_disk', 'docs');
        $this->checkDisk($diskName);
        $this->diskName = $diskName;

        $this->cacheDuration = $cacheDuration;
        if (!$defaultFile) {
            $defaultFile = collect($this->files)
                ->first() ?? throw new NotFoundHttpException("No markdown files found in the documentation path '{$this->docPath}' on the '{$this->diskName}' disk.");
        } else {
            $defaultFile = Str::of($defaultFile)->start($this->docPath . '/')
                ->finish('.md')
                ->toString();
        }

        $this->content = CacheRenderedFileAction::execute($defaultFile, $diskName, $this->cacheDuration, $this->getCommonMarkOptions());
    }

    #[Computed(persist: true)]
    public function files(): array
    {
        if (!Storage::disk($this->diskName)->exists($this->docPath)) {
            throw new NotFoundHttpException("The documentation path '{$this->docPath}' does not exist on the '{$this->diskName}' disk.");
        }

        return collect(Storage::disk($this->diskName)->allFiles($this->docPath))
            ->filter(fn($file) => str_ends_with($file, '.md'))
            ->values()
            ->toArray();
    }

    protected function getCommonMarkOptions(): array
    {
        $docLinkOptions = [
            'doc_link' => [
                'context' => [
                    'doc_path' => $this->docPath,
                    'disk_name' => $this->diskName,
                    'current_file' => $this->markdownNavSelected,
                ],
            ]
        ];
        return array_merge_recursive(config('markdown-navigator.markdown_options', []), $docLinkOptions);
    }

    protected function checkDisk(string $diskName): void
    {
        if (!in_array($diskName, $this->getConfig('permitted_disks', 'docs'))) {
            throw new InvalidArgumentException("The disk '{$diskName}' is not permitted. Please choose a disk from the permitted list in the configuration.");
        }
    }

    /**
     * @param  string  $file
     * @return void
     * @throws NotFoundHttpException
     */
    public function viewDoc(string $file): void
    {
        // ensure the file is within the docPath and is a markdown file
        if (
            !str_starts_with($file, $this->docPath.'/')
            || !str_ends_with($file, '.md')
            || !in_array($file, $this->files)
        ) {
            throw new NotFoundHttpException("The file '{$file}' is not found within this documentation.");
        }

        $this->markdownNavSelected = $file;

        if ($this->cacheDuration > 0) {
            $this->content = CacheRenderedFileAction::execute($file, $this->diskName, $this->cacheDuration,
                $this->getCommonMarkOptions());
        } else {
            $this->content = RenderFileAction::execute($file, $diskName,
                $commonMarkOptions);
        }

        $this->dispatch('markdown-navigator:file-selected', ['file' => $file]);
    }

    /**
     * @return array|mixed
     */
    protected function getConfig(string $key, mixed $default): mixed
    {
        return data_get($this->config, $key, $default);
    }
};
?>

<div class="markdown-navigator" x-cloak>
    {{$markdownNavSelected}}
    <div wire:model.live.debounce="files" class="">
        @php
            $lastDirectory = null;
            $slugParts = $markdownNavSelected ? Str::of($markdownNavSelected)->after($docPath . '/')->explode('/') : [];
        @endphp
        <div class="mn-container">
            <div class="mn-container--inner">
                <ul class="file-list">
                    @foreach($this->files as $file)
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
            </div>
            <div class="markdown-content">
                <div wire:loading.remove>{{ collect($slugParts)->join(' / ') }}</div>
                <div wire:loading>
                    <livewire:perturbatio::docs-loading-indicator class="loading-indicator"/>
                </div>
                <div class="content">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // function loadFromHash() {
    //     const file = window.location.hash.replace('#', '');
    //     if (file) {
    //         $wire.viewDoc(file);
    //     }
    // }

    // if the URL has a fragment on page load, load that file
    // loadFromHash();

    // window.addEventListener('hashchange', loadFromHash)

    // $wire.on('markdown-navigator:file-selected', params => {
    //     // push the param to the current URL as a fragment
    //     window.location.hash = params[0].file;
    // });
</script>
