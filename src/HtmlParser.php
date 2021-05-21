<?php

namespace VV\Classify;

class HtmlParser implements ClassifyParser
{
    public function parse(string $tags, string $classes, string $value): string
    {
        $tags = explode(' ', $tags);
        $last = $this->getLastTagAndRemove($tags);

        return preg_replace(
            $this->defineRegexPattern($tags, $last),
            $this->defineReplacement($classes, $last),
            $value
        );
    }

    private function getLastTagAndRemove(array &$tags): string
    {
        return array_splice($tags, array_key_last($tags))[0];
    }

    private function defineRegexPattern(array $tags, string $last): string
    {
        $pattern = '';

        foreach ($tags as $tag) {
            $pattern .= "<{$tag}[^>]*>[^<]*";
        }

        return "/({$pattern})(<{$last})/iU";
    }

    private function defineReplacement(string $classes, string $last): string
    {
        return "$1<{$last} class=\"{$classes}\"";
    }
}
