<?php

namespace Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

/**
 * @phpstan-type DocLinkContext array{doc_path: string, disk_name: string, current_file: ?string}
 */
final class DocLinkExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('doc_link', Expect::structure([
            'context' => Expect::structure(
                [
                    'doc_path' => Expect::string()->required(),
                    'disk_name' => Expect::string()->required(),
                    'current_file' => Expect::string()->nullable(),
                ]
            ),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        //        $environment->addInlineParser(new DocLinkParser(), 50);
        $environment->addRenderer(
            nodeClass: Link::class,
            renderer: new DocLinkRenderer,
            priority: 50
        );
    }
}
