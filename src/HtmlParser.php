<?php

namespace VV\Classify;

use Symfony\Component\DomCrawler\Crawler;

class HtmlParser implements ClassifyParser
{
    public function parse(Tag $tag, string $value): string
    {
        $selector = $tag->tag;

        if (count($tag->before) > 0) {
            $selector = implode(' > ', $tag->before).' > '.$selector;

            $firstPart = strtolower($tag->before[0]);

            // Guard against producing selectors in the form: body > body > span.
            if ($firstPart != 'body') {
                $selector = 'body > '.$selector;
            }
        }

        $crawler = new Crawler($value);
        $nodes = $crawler->filter($selector);

        if (count($nodes) == 0) {
            return $value;
        }

        foreach ($nodes as $node) {
            $node->setAttribute('class', $tag->classes);
        }

        // DomCrawler wraps fragments in head/body; return the body contents when present.
        $body = $crawler->filter('body');

        if ($body->count() > 0) {
            return $body->html();
        }

        return $crawler->html();
    }
}
