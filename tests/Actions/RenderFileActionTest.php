<?php

use Illuminate\Filesystem\Filesystem;
use Perturbatio\LivewireMarkdownNavigator\Actions\RenderFileAction;

covers([
    RenderFileAction::class,
]);

it('throws an exception when no permitted disks are configured', function () {
    config()->set('livewire-markdown-navigator.permitted_disks', null);

    RenderFileAction::execute('file.md', 'docs', []);
})->throws(Exception::class, 'No disks configured.');

it('throws an exception when permitted disks config is not an array', function () {
    config()->set('livewire-markdown-navigator.permitted_disks', 'test');

    RenderFileAction::execute('file.md', 'docs', []);
})->throws(Exception::class, 'No disks configured.');

it('throws an exception when disk is not in permitted disks', function () {
    RenderFileAction::execute('file.md', 'test', []);
})->throws(Exception::class, 'Disk [test] is not permitted.');

it('throws an exception when file does not exist', function () {
    Storage::fake('test');
    config()->set('livewire-markdown-navigator.permitted_disks', ['test']);
    RenderFileAction::execute('file.md', 'test', []);
})->throws(Exception::class, 'File [file.md] does not exist on disk [test].');

it('returns an empty string if the file cannot be loaded', function () {
    Storage::fake('test');
    Storage::disk('test')->put('file.md', '# a file');

    config()->set('livewire-markdown-navigator.permitted_disks', ['test']);
    $mockFileSystem = mock(Filesystem::class);
    Storage::shouldReceive('disk')
        ->once()
        ->with('test')
        ->andReturn($mockFileSystem);

    $mockFileSystem->shouldReceive('exists')
        ->once()
        ->with('file.md')
        ->andReturn(true);

    Storage::shouldReceive('disk')
        ->once()
        ->with('test')
        ->andReturn($mockFileSystem);

    $mockFileSystem->shouldReceive('get')
        ->once()
        ->with('file.md')
        ->andReturn(null);

    expect(RenderFileAction::execute('file.md', 'test', []))->toBe('');
});
