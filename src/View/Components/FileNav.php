<?php

namespace Perturbatio\LivewireMarkdownNavigator\View\Components;

use Illuminate\Contracts\View\View;
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
        return view('livewire-markdown-navigator::components.file-nav');
    }
}
