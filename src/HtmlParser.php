<?php

namespace VV\Classify;

class HtmlParser implements ClassifyParser
{
    public function parse(string $tag, string $class, string $value): string
    {
        return str_replace($this->tagFilter($tag), $this->replaceTag($tag, $class), $value);
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
}
