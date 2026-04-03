<?php

namespace Perturbatio\LivewireMarkdownNavigator\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\Component;

class FileNav extends Component
{
    public function __construct(
        public \Livewire\Component $markdownNavigator,
        public string $docPath,
        public ?string $markdownNavSelected
    ) {}

    public function render(): View
    {
        // using a ViewFactory to work around larastan's view-string causing problems in github actions
        /* @see /resources/views/components/file-nav.blade.php */
        return ViewFactory::make('livewire-markdown-navigator::components.file-nav');
    }
}
