<?php

namespace Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink;

use Illuminate\Support\Facades\Storage;
use League\CommonMark\Util\HtmlElement;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

/**
 * @phpstan-import-type DocLinkContext from DocLinkExtension
 */
final class DocLinkRewriter implements ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    // rewrite the href attribute to point to the correct location
    public function rewrite(HtmlElement $element): HtmlElement
    {
        $href = $element->getAttribute('href');
        if (! is_string($href) || empty($href)) {
            return $element;
        }

        /**
         * @var DocLinkContext $context
         */
        $context = $this->config->get('doc_link.context');

        // pass both the href and the context to the renderer
        return $this->renderer($element, $context);
    }

    /**
     * @param  DocLinkContext|null  $context
     */
    public function renderer(HtmlElement $element, ?array $context): HtmlElement
    {
        if (! is_array($context)) {
            return $element; // return the original href if it's an absolute URL or a protocol-relative URL
        }
        // Extract context values with fallback to component properties
        $docPath = $context['doc_path'];
        $diskName = $context['disk_name'];

        if (empty($docPath) || empty($diskName)) {
            return $element;
        }

        // if the link is not a URL that we want to rewrite, return it as is
        $href = strval($element->getAttribute('href'));
        $invalidUrlRegex = '/^(\w+:|\/\/|\\\\)/';
        if (preg_match($invalidUrlRegex, $href)) {
            return $element; // return the original href if it's an absolute URL or a protocol-relative URL
        }

        // otherwise, assume it's a relative link to another markdown file in the same directory
        $path = pathinfo($href);

        // check if the file exists in the disk
        $filePath = $docPath.'/'.$path['filename'].'.md';
        if (Storage::disk($diskName)->exists($filePath)) {

            $element->setAttribute(
                'href',
                '?'.$filePath
            );
            // href="#" wire:click.prevent="viewDoc('{{$file}}')" class="file-link"
            $element->setAttribute(
                'wire:click.prevent',
                "viewDoc('{$filePath}')"
            );
            $element->setAttribute('className', 'file-link');
            $element->setAttribute('rel', 'internal');
        }

        return $element;
    }
}
