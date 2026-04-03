<?php

use League\CommonMark\Util\HtmlElement;
use Perturbatio\LivewireMarkdownNavigator\CommonMark\Extension\DocLink\DocLinkRewriter;

covers([
    DocLinkRewriter::class,
]);

test('renderer returns the element unmodified if it does not match the expected pattern', function () {
    $linkRewriter = new DocLinkRewriter;
    $element = new HtmlElement('a', ['href' => 'https://example.com']);
    $returnedElement = $linkRewriter->renderer($element, [
        'doc_path' => 'test',
        'disk_name' => 'test',
    ]);
    expect($returnedElement)->toBe($element);
});

test('renderer returns the element unmodified if the context is invalid', function (string $docPath, string $diskName) {
    $linkRewriter = new DocLinkRewriter;
    $element = new HtmlElement('a', ['href' => './index.md']);
    $returnedElement = $linkRewriter->renderer($element, [
        'doc_path' => $docPath,
        'disk_name' => $diskName,
    ]);
    expect($returnedElement)->toBe($element);
})->with([
    'empty docpath' => [
        'docPath' => '',
        'diskName' => 'test',
    ],
    'empty diskname' => [
        'docPath' => 'test',
        'diskName' => '',
    ],
]);

test('rendered returns the element if the context is not an array', function () {
    $linkRewriter = new DocLinkRewriter;
    $element = new HtmlElement('a', ['href' => './index.md']);
    $returnedElement = $linkRewriter->renderer($element, null);
    expect($returnedElement)->toBe($element);
});

test('rewrite returns the element if the href attribute is not a string', function () {
    $linkRewriter = new DocLinkRewriter;
    $element = new HtmlElement('a', ['href' => true]);
    $returnedElement = $linkRewriter->rewrite($element);
    expect($returnedElement)->toBe($element);
});

test('rewrite returns the element if the href attribute empty', function () {
    $linkRewriter = new DocLinkRewriter;
    $element = new HtmlElement('a', ['href' => '']);
    $returnedElement = $linkRewriter->rewrite($element);
    expect($returnedElement)->toBe($element);
});
