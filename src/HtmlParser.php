<?php

namespace VV\Classify;

class HtmlParser implements ClassifyParser
{
    public function parse(Tag $tag, string $value): string
    {
        return preg_replace(
            $this->defineRegexPattern($tag),
            $this->defineReplacement($tag),
            $value
        );
    }

    private function defineRegexPattern(Tag $tag): string
    {
        $pattern = '';

        foreach ($tag->before as $name) {
            $pattern .= "<{$name}[^>]*>[^<]*";
        }

        return "/({$pattern})(<{$tag->tag})(?! class)/iU";
    }

    private function defineReplacement(Tag $tag): string
    {
        return "$1<{$tag->tag} class=\"{$tag->classes}\"";
    }
}
