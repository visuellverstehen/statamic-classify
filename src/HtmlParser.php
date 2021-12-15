<?php

namespace VV\Classify;

use Symfony\Component\DomCrawler\Crawler;

class HtmlParser implements ClassifyParser
{
    public function parse(Tag $tag, string $value): string
    {
        $selector = $tag->tag;

        if (count($tag->before) > 0) {
            $selector = implode(' > ', $tag->before) . ' > '.$selector;

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

        // Generate the HTML with our class adjustments made.
        $result = $crawler->html();

        // Removes the <body> and </body> tags that get added since it's a fragment.
        $result = substr($result, 6);

        return substr($result, 0, -7);
    }
}