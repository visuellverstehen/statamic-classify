<?php

namespace VV\Classify\Modifiers;

use Statamic\Modifiers\Modifier;

class Classify extends Modifier
{
    /**
     * If only {{ classify }} is provided the default styleset will be aplied.
     * If a specific styleset {{ classify:foo}} is provided this will be aplied but if it dosn't exist the value will be returnd without changes.
     */
    public function index($value, $params, $context)
    {
        $styleset = $params[0] ?? 'default';

        if (! $this->isStylesetAvailable($styleset)) {
            return $value;
        }

        // The styleset wich will be applied.
        $styleSegments = config('classify.'.$styleset);

        foreach ($styleSegments as $tag => $class) {
            $value = str_replace($this->tagFilter($tag), $this->replaceTag($tag, $class), $value);
        }

        return $value;
    }

    /**
     * Build string wich should be replaced.
     *
     * @param string $tag
     * @return string
     */
    private function tagFilter(string $tag): string
    {
        return "<{$tag}";
    }

    /**
     * Replace filtered tag and add css classes.
     *
     * @param string $tag
     * @param string $class
     * @return string
     */
    private function replaceTag(string $tag, string $class): string
    {
        return "<{$tag} class=\"{$class}\"";
    }

    /**
     * Check if the given styleset is available in the config.
     *
     * @param string $styleset
     * @return bool
     */
    private function isStylesetAvailable(string $styleset): bool
    {
        if (! config()->has('classify.'.$styleset)) {
            return false;
        }

        return true;
    }
}
