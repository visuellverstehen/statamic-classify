<?php

namespace VV\Classify;

class HtmlParser implements ClassifyParser
{
    public function parse(string $tags, string $classes, string $value): string
    {
        if ($this->isSingleTag($tags)) {
            return str_replace($this->tagFilter($tags), $this->replaceTag($tags, $classes), $value);
        }

        return $value;
    }

    /**
     * Build string wich should be replaced.
     */
    private function tagFilter(string $tag): string
    {
        return "<{$tag}";
    }

    /**
     * Replace filtered tag and add css classes.
     */
    private function replaceTag(string $tag, string $class): string
    {
        return "<{$tag} class=\"{$class}\"";
    }

    private function isSingleTag(string $tags): bool
    {
        return count(explode(' ', $tags)) === 1;
    }
}
