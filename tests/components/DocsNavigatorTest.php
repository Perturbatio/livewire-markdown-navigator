<?php

use Livewire\Livewire;
use Perturbatio\LivewireMarkdownNavigator\Actions\CacheRenderedFileAction;
use Perturbatio\LivewireMarkdownNavigator\Actions\RenderFileAction;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkExtension;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkRenderer;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkRewriter;
use Perturbatio\LivewireMarkdownNavigator\View\Components\FileNav;

beforeEach(function () {
    $this->defaultDiskName = 'docs';
    $this->defaultDocsPath = 'test-docs';
    $this->disk = Storage::fake($this->defaultDiskName);
    $this->disk->makeDirectory($this->defaultDocsPath);
});

covers([
    DocLinkExtension::class,
    DocLinkRenderer::class,
    DocLinkRewriter::class,
    CacheRenderedFileAction::class,
    RenderFileAction::class,
    FileNav::class,
]);

it('renders successfully', function () {
    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
    ])
        ->assertStatus(200)
        ->assertSeeHtml('<h1 id="index-page">Index Page</h1>');
});

it('defaults to 1 for starting depth', function () {
    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
    ])
        ->assertStatus(200)
        ->assertDontSeeHtml('title="test-docs"')
        ->assertDontSeeHtml('>Test Docs</div>')
        ->assertSeeHtml('<h1 id="index-page">Index Page</h1>');
});

it('renders the docs path as a title when starting depth is 0', function () {
    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
        'startingDepth' => 0,
    ])
        ->assertStatus(200)
        ->assertSeeHtml('title="test-docs"')
        ->assertSeeHtml('>Test Docs</div>')
        ->assertSeeHtml('<h1 id="index-page">Index Page</h1>');
});

it('does not allow a starting depth below 0', function () {
    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
        'startingDepth' => -10,
    ])
        ->assertStatus(200)
        ->assertSeeHtml('title="test-docs"')
        ->assertSeeHtml('>Test Docs</div>')
        ->assertSeeHtml('<h1 id="index-page">Index Page</h1>');
});

it('shows the doc paths as a title when starting depth is 0', function () {
    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
        'startingDepth' => 0,
    ])
        ->assertStatus(200)
        ->assertSeeHtml('title="test-docs"')
        ->assertSeeHtml('>Test Docs</div>')
        ->assertSeeHtml('<h1 id="index-page">Index Page</h1>');
});

it('renders a test document', function () {
    $this->disk->put('test-docs/index.md', <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    $this->disk->put('test-docs/test-docs.md', <<<'MARKDOWN'
# test heading

test content

[Back](index.md)
MARKDOWN
    );

    Livewire::withQueryParams([
        'markdownNavSelected' => "{$this->defaultDocsPath}/test-docs.md",
    ])->test('perturbatio::markdown-navigator', [
        'docPath' => $this->defaultDocsPath,
    ])->assertSee('Test Docs')
        ->call('viewDoc', "{$this->defaultDocsPath}/test-docs.md")
        ->assertSeeHtml('<h1 id="test-heading">test heading</h1>')
        ->assertSeeHtml('<p>test content</p>')
        ->assertSeeHtml('<a href="?test-docs/index.md" wire:click.prevent="viewDoc(\'test-docs/index.md\')" className="file-link" rel="internal">Back</a>')
        ->assertStatus(200);
});

it('caches a rendered document', function () {
    $this->disk->put('test-docs/test-docs.md', <<<'MARKDOWN'
# test heading

test content

[Back](index.md)
MARKDOWN
    );

    Livewire::withQueryParams([
        'markdownNavSelected' => 'test-docs/test-docs.md',
    ])->test('perturbatio::markdown-navigator', [
        'docPath' => 'test-docs',
    ])->assertSee('Test Docs')
        ->call('viewDoc', 'test-docs/test-docs.md')
        ->assertSeeHtml('<h1 id="test-heading">test heading</h1>')
        ->assertSeeHtml('<p>test content</p>')
        ->assertStatus(200);

    $key = "livewire-markdown-navigator:{$this->defaultDiskName}:test-docs/test-docs.md";

    expect(Cache::get($key))
        ->toContain('<h1 id="test-heading">test heading</h1>')
        ->toContain('<p>test content</p>');
});

it('scopes files to the specified docPath', function () {
    $otherPath = 'other-path';
    $this->disk->makeDirectory($otherPath);

    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Should Not Display

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
    ])
        ->call('viewDoc', "$otherPath/test-docs.md")
        ->assertDontSee('Should Not Display')
        ->assertStatus(404);

});

it('accepts a different disk from the default', function () {
    $otherDiskName = 'other-disk';
    $otherDisk = Storage::fake($otherDiskName);

    $permittedDisks = Arr::wrap(config('livewire-markdown-navigator.permitted_disks'));
    $permittedDisks[] = $otherDiskName;
    config()->set('livewire-markdown-navigator.permitted_disks', $permittedDisks);

    $docPath = 'other-docs';
    $otherDisk->makeDirectory($docPath);

    $otherDisk->put($docPath.'/test-doc.md', <<<'MARKDOWN'
# test heading

test content

[Back](index.md)
MARKDOWN
    );

    $testFileName = 'test-doc.md';
    expect($otherDisk->exists($docPath.'/'.$testFileName.''))->toBeTrue();

    $testable = Livewire::withQueryParams([
        'markdownNavSelected' => $docPath.'/'.$testFileName,
    ])->test('perturbatio::markdown-navigator', [
        'diskName' => $otherDiskName,
        'docPath' => $docPath,
        'defaultFile' => $testFileName,
    ]);

    $testable->assertSee('Test Doc')
        ->call('viewDoc', $docPath.'/'.$testFileName)
        ->assertSeeHtml('<h1 id="test-heading">test heading</h1>')
        ->assertSeeHtml('<p>test content</p>')
        ->assertStatus(200);
    //    "livewire-markdown-navigator:{$diskName}:{$file}"
    $key = "livewire-markdown-navigator:{$otherDiskName}:{$docPath}/{$testFileName}";

    expect(Cache::get($key))
        ->toContain('<h1 id="test-heading">test heading</h1>')
        ->toContain('<p>test content</p>');
});

it('has loading classes', function () {
    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
    ])
        ->assertStatus(200)
        ->assertSeeHtml('wire:loading.class="loading opacity-75 transition-opacity pointer-events-none"');
});

it('loading classes can be overridden', function () {
    $this->disk->put("{$this->defaultDocsPath}/index.md", <<<'MARKDOWN'
# Index Page

[Test Docs Here](./test-docs.md)

MARKDOWN
    );

    Livewire::test('perturbatio::markdown-navigator', [
        'diskName' => $this->defaultDiskName,
        'docPath' => $this->defaultDocsPath,
        'loadingClasses' => 'loading with-this-class-instead',
    ])
        ->assertStatus(200)
        ->assertSeeHtml('wire:loading.class="loading with-this-class-instead"');
});
