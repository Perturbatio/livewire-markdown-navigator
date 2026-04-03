<?php

namespace Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\LinkRenderer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class DocLinkRenderer implements ConfigurationAwareInterface, NodeRendererInterface
{
    protected LinkRenderer $baseRenderer;

    protected DocLinkRewriter $linkRewriter;

    public function __construct()
    {
        $this->baseRenderer = new LinkRenderer;
        $this->linkRewriter = new DocLinkRewriter;
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement|\Stringable
    {
        /** @var Link $node */
        /** @var HtmlElement $element */
        $element = $this->baseRenderer->render($node, $childRenderer);

        return $this->linkRewriter->rewrite($element);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->baseRenderer->setConfiguration($configuration);
        $this->linkRewriter->setConfiguration($configuration);
    }
}
