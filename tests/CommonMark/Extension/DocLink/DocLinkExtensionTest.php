<?php

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Elements\Type;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkExtension;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkRenderer;

use function Livewire\invade;

covers([
    DocLinkExtension::class,
]);

it('configures expected schema', function () {
    /** @var DocLinkExtension $extension */
    $extension = app(DocLinkExtension::class);
    $mockConfigurationBuilderInterface = mock(ConfigurationBuilderInterface::class, function ($mock) {
        $mock->shouldReceive('addSchema')
            ->once()
            ->withArgs(function (string $name, Structure $schema) {
                $shape = $schema->getShape();

                /** @var Structure $context */
                $context = $shape['context'];
                //                dd($context->getShape());
                expect($name)->toBe('doc_link')
                    ->and($shape)->toBeArray()
                    ->and($shape['context'])->toBeInstanceOf(Structure::class)
                    ->and($context->getShape())
                    ->toBeArray()
                    ->toHaveKeys([
                        'doc_path',
                        'disk_name',
                        'current_file',
                    ])
                    ->and($context->getShape())
                    ->each(function ($expectation, $name) {
                        expect($expectation->value)->toBeInstanceOf(Type::class)

                            ->when($name === 'current_file', function ($expectation) {
                                $expectation->and(invade($expectation->value)->type)->toBe('null|string');
                            })
                            ->when(in_array($name, ['doc_path', 'disk_name']), function ($expectation) {
                                expect(invade($expectation->value))->required->toBeTrue()
                                    ->type->toBe('string');
                            })
                            ->and($name)
                            ->toBeIn(['doc_path', 'disk_name', 'current_file']);
                    });

                //                dd($schema);
                return true;
            });
    });
    $extension->configureSchema($mockConfigurationBuilderInterface);
});

it('registers the renderer with the expected priority', function () {
    $extension = app(DocLinkExtension::class);
    $mockEnvironment = mock(EnvironmentBuilderInterface::class, function ($mock) {
        $mock->shouldReceive('addRenderer')
            ->once()
            ->withArgs(function (string $nodeClass, object $renderer, int $priority) {
                expect($nodeClass)->toBe(Link::class)
                    ->and($renderer)->toBeInstanceOf(DocLinkRenderer::class)
                    ->and($priority)->toBe(50);

                return true;
            });
    });

    $extension->register($mockEnvironment);
});
