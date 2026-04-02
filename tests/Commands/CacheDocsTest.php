<?php

use Perturbatio\LivewireMarkdownNavigator\Commands\CacheDocs;

covers([
    CacheDocs::class,
]);

it('requires disks to be configured', function ($value) {
    config()->set('livewire-markdown-navigator.permitted_disks', $value);
    $this->artisan('livewire-markdown-navigator:cache-docs')->expectsOutputToContain('No disks configured.')->assertFailed();
})->with([
    'null disks' => [
        'value' => null,
    ],
    'empty array' => [
        'value' => [],
    ],
    'integer value' => [
        'value' => 1,
    ],
]);

it('fails when a disk that is not permitted has been requested', function () {
    config()->set('livewire-markdown-navigator.permitted_disks', [
        'docs',
    ]);
    $this->artisan('livewire-markdown-navigator:cache-docs --disk=local')
        ->expectsOutputToContain('Disk [local] not permitted.')
        ->assertFailed();
});

it('fails when a disk name is not a string', function () {
    config()->set('livewire-markdown-navigator.permitted_disks', [
        234,
    ]);
    $this->artisan('livewire-markdown-navigator:cache-docs')
        ->expectsOutputToContain('Invalid disk name in configuration')
        ->assertFailed();
});

it('caches the rendering of each file in the permitted disks', function () {
    config()->set('livewire-markdown-navigator.permitted_disks', [
        'docs',
        'other',
    ]);
    $file1Content = <<<'MARKDOWN'
# Test Cache
MARKDOWN;

    $file2Content = <<<'MARKDOWN'
# Test Cache 2
MARKDOWN;

    $docsDisk = Storage::fake('docs');
    $dir1 = 'test-dir';
    $docsDisk->makeDirectory($dir1);
    $docsDisk->put("$dir1/test-doc.md", $file1Content);

    $otherDisk = Storage::fake('other');
    $dir2 = 'test-dir2';
    $otherDisk->makeDirectory($dir2);
    $otherDisk->put("$dir2/test-doc.md", $file2Content);

    $this->artisan('livewire-markdown-navigator:cache-docs');
    $key1 = "livewire-markdown-navigator:docs:$dir1/test-doc.md";
    $key2 = "livewire-markdown-navigator:other:$dir2/test-doc.md";

    expect(Cache::has($key1))->toBeTrue()
        ->and(Cache::get($key1))->toContain('<h1 id="test-cache">Test Cache</h1>')
        ->and(Cache::has($key2))->toBeTrue()
        ->and(Cache::get($key2))->toContain('<h1 id="test-cache-2">Test Cache 2</h1>');
});

it('re-caches the rendering if the force flag is provided', function () {
    $diskName = 'docs';
    config()->set('livewire-markdown-navigator.permitted_disks', [
        $diskName,
    ]);
    $disk = Storage::fake($diskName);
    $dir1 = 'test-dir';
    $fileName = 'test-force.md';
    $originalContent = 'TEST BEFORE RECACHE';
    $newContent = <<<'MARKDOWN'
# Test Cache
MARKDOWN;
    $expected = '<h1 id="test-cache">Test Cache</h1>';
    $disk->makeDirectory($dir1);
    $disk->put("$dir1/$fileName", $newContent);
    $key = "livewire-markdown-navigator:$diskName:$dir1/$fileName";
    Cache::put($key, $originalContent);

    expect(Cache::has($key))->toBeTrue();

    $this->artisan('livewire-markdown-navigator:cache-docs --force')->assertOk();

    expect(Cache::has($key))->toBeTrue()
        ->and(Cache::get($key))->toContain($expected);
});
