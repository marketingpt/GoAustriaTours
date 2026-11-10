<?php

namespace App\Support;

/** Allow-list HTML sanitiser for rich text saved from the admin (Trix output). */
class Html
{
    private const ALLOWED = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'a', 'blockquote', 'div', 'span'];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $doc = new \DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><body>'.$html.'</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $doc->getElementsByTagName('body')->item(0);
        if (! $body) {
            return strip_tags($html);
        }
        self::walk($body);

        $out = '';
        foreach (iterator_to_array($body->childNodes) as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out) === '' ? null : trim($out);
    }

    private static function walk(\DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                $tag = strtolower($child->tagName);
                if (! in_array($tag, self::ALLOWED, true)) {
                    // Drop dangerous elements entirely; unwrap harmless unknown ones.
                    if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'svg', 'math'], true)) {
                        $node->removeChild($child);

                        continue;
                    }
                    self::walk($child);
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);

                    continue;
                }
                foreach (iterator_to_array($child->attributes) as $attr) {
                    $keep = $tag === 'a' && $attr->name === 'href' && preg_match('#^(https?://|mailto:|tel:|/|\#)#i', trim($attr->value));
                    if (! $keep) {
                        $child->removeAttribute($attr->name);
                    }
                }
                if ($tag === 'a' && $child->hasAttribute('href')) {
                    $child->setAttribute('rel', 'noopener');
                }
                self::walk($child);
            }
        }
    }
}
