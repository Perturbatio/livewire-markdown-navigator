<?php

use Livewire\Component;
use Perturbatio\LivewireMarkdownNavigator\View\Components\FileNav;

covers([
    FileNav::class,
]);

it('ensures a minimum depth of 0', function () {
    $component = new FileNav(
        markdownNavigator: new class extends Component
        {
            public array $files = [
                'test-docs/index.md',
                'test-docs/test-docs.md',
            ];
        },
        docPath: 'test-docs',
        markdownNavSelected: null,
        startingDepth: -1,
    );

    expect($component->startingDepth)->toBe(0);
});

it('returns an empty list when the starting depth is greater than available depth', function () {
    $component = new FileNav(
        markdownNavigator: new class extends Component
        {
            public array $files = [
                'test-docs/index.md',
                'test-docs/test-docs.md',
            ];
        },
        docPath: 'test-docs',
        markdownNavSelected: null,
        startingDepth: 2,
    );
    expect(invade($component)->buildFileTree(
        [
            'test-docs/index.md',
            'test-docs/test-docs.md',
        ],
        2
    ))->toBeArray()
        ->toBeEmpty()
        ->and($component->startingDepth)->toBe(2);
});

it('does not include doc path in nav when starting depth is greater than 0', function () {
    $component = new FileNav(
        markdownNavigator: new class extends Component
        {
            public array $files = [
                'test-docs/index.md',
                'test-docs/test-docs.md',
            ];
        },
        docPath: 'test-docs',
        markdownNavSelected: null,
        startingDepth: 1,
    );

    $actual = invade($component)->buildFileTree(
        [
            'test-docs/index.md',
            'test-docs/test-docs.md',
        ],
        1
    );

    $expected = [
        'index.md' => [
            ':path' => 'test-docs/index.md',
        ],
        'test-docs.md' => [
            ':path' => 'test-docs/test-docs.md',
        ],
    ];

    expect($component->startingDepth)->toBe(1)
        ->and(json_encode($actual))->toEqual(json_encode($expected));
});

it('includes doc path in nav when starting depth is 0', function () {
    $component = new FileNav(
        markdownNavigator: new class extends Component
        {
            public array $files = [
                'test-docs/index.md',
                'test-docs/test-docs.md',
            ];
        },
        docPath: 'test-docs',
        markdownNavSelected: null,
        startingDepth: 0,
    );

    $actual = invade($component)->buildFileTree(
        [
            'test-docs/index.md',
            'test-docs/test-docs.md',
        ],
        0
    );

    $expected = [
        'test-docs' => [
            'index.md' => [
                ':path' => 'test-docs/index.md',
            ],
            'test-docs.md' => [
                ':path' => 'test-docs/test-docs.md',
            ],
        ],
    ];
    expect($component->startingDepth)->toBe(0)
        ->and(json_encode($actual))->toEqual(json_encode($expected));

});

it('ignores depths lower than 0', function () {
    $component = new FileNav(
        markdownNavigator: new class extends Component
        {
            public array $files = [
                'test-docs/index.md',
                'test-docs/test-docs.md',
            ];
        },
        docPath: 'test-docs',
        markdownNavSelected: null,
        startingDepth: -1,
    );

    $actual = invade($component)->buildFileTree(
        [
            'test-docs/index.md',
            'test-docs/test-docs.md',
        ],
        -1
    );

    $expected = [
        'test-docs' => [
            'index.md' => [
                ':path' => 'test-docs/index.md',
            ],
            'test-docs.md' => [
                ':path' => 'test-docs/test-docs.md',
            ],
        ],
    ];
    expect($component->startingDepth)->toBe(0)
        ->and(json_encode($actual))->toEqual(json_encode($expected));
});

it('defaults to not collapsing children', function () {
    $component = new FileNav(
        markdownNavigator: new class extends Component
        {
            public array $files = [
                'test-docs/index.md',
                'test-docs/test-docs.md',
            ];
        },
        docPath: 'test-docs',
        markdownNavSelected: null,
        startingDepth: 1
    );

    expect($component->collapseChildren)->toBeFalse();
});

it('allows collapsing children', function () {
    $component = new FileNav(
        markdownNavigator: new class extends Component
        {
            public array $files = [
                'test-docs/index.md',
                'test-docs/test-docs.md',
            ];
        },
        docPath: 'test-docs',
        markdownNavSelected: null,
        startingDepth: 1,
        collapseChildren: true,
    );

    expect($component->collapseChildren)->toBeTrue();
});
