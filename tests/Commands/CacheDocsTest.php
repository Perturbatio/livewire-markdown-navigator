<?php

use Perturbatio\LivewireMarkdownNavigator\Commands\CacheDocs;

use function Livewire\invade;

covers([
    CacheDocs::class,
]);

it('requires cacheDuration to be a non zero integer', function ($value, bool $shouldPass) {
    config()->set('livewire-markdown-navigator.permitted_disks', [
        'docs',
    ]);
    Storage::fake('docs');
    $command = $this->artisan("livewire-markdown-navigator:cache-docs --cacheDuration=$value");
    if ($shouldPass) {
        $command->assertOk();
    } else {
        $command->expectsOutputToContain('Cache duration must be a positive integer.')
            ->assertFailed();
    }
})->with([
    'one' => [
        'value' => 1,
        'shouldPass' => true,
    ],
    'one thousand' => [
        'value' => 1000,
        'shouldPass' => true,
    ],
    'zero' => [
        'value' => 0,
        'shouldPass' => false,
    ],
    'not an integer' => [
        'value' => 'not-an-integer',
        'shouldPass' => false,
    ],
    'negative integer' => [
        'value' => -5,
        'shouldPass' => false,
    ],
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

    $this->artisan('livewire-markdown-navigator:cache-docs')
        ->expectsOutputToContain('Caching for "docs'.DIRECTORY_SEPARATOR.$dir1.'"')
        ->expectsOutputToContain('Caching for "other'.DIRECTORY_SEPARATOR.$dir2.'"')
        ->assertOk();
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

it('does not re-cache the rendering if the force flag is absent', function () {
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

    $disk->makeDirectory($dir1);
    $disk->put("$dir1/$fileName", $newContent);
    $key = "livewire-markdown-navigator:$diskName:$dir1/$fileName";
    Cache::put($key, $originalContent);

    expect(Cache::has($key))->toBeTrue();

    $this->artisan('livewire-markdown-navigator:cache-docs')->assertOk();

    expect(Cache::has($key))->toBeTrue()
        ->and(Cache::get($key))->toContain($originalContent);
});

it('caches the rendering of each file in the specified disk', function () {
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

    $key1 = "livewire-markdown-navigator:docs:$dir1/test-doc.md";
    $key2 = "livewire-markdown-navigator:other:$dir2/test-doc.md";

    $docsCacheContent = 'docs TEST BEFORE RECACHE';
    $otherCacheContent = 'other CACHE TEST BEFORE RECACHE';
    Cache::put($key1, $docsCacheContent);
    Cache::put($key2, $otherCacheContent);

    $this->artisan('livewire-markdown-navigator:cache-docs --disk=other --force')->assertOk();

    expect(Cache::has($key1))->toBeTrue()
        ->and(Cache::get($key1))->toContain($docsCacheContent)
        ->and(Cache::has($key2))->toBeTrue()
        ->and(Cache::get($key2))->toContain('<h1 id="test-cache-2">Test Cache 2</h1>');
});

it('provides expected common mark options', function () {
    config()->set('livewire-markdown-navigator.permitted_disks', [
        'docs',
    ]);
    $command = new CacheDocs;
    $options = invade($command)->getCommonMarkOptions('test-docs', 'docs');

    expect($options)->toHaveKey('doc_link')
        ->and($options['doc_link'])->toHaveKey('context')
        ->and($options['doc_link']['context'])->toHaveKey('doc_path')
        ->and($options['doc_link']['context']['doc_path'])->toBe('test-docs')
        ->and($options['doc_link']['context'])->toHaveKey('disk_name')
        ->and($options['doc_link']['context']['disk_name'])->toBe('docs')
        ->and($options['doc_link']['context'])->toHaveKey('current_file')
        ->and($options['doc_link']['context']['current_file'])->toBeNull();
});
