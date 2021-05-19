<?php

namespace VV\Classify\Modifiers;

use Statamic\Modifiers\Modifier;

class Classify extends Modifier
{
    /**
     * If only {{ classify }} is provided the default styleset will be applied.
     *
     * You can define a specified styleset as well by defining {{ classify:foo }}
     * In case the set can not be found, the original value will get returned.
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

    /**
     * Check if the given styleset is available in the config.
     */
    private function isStylesetAvailable(string $styleset): bool
    {
        return config()->has('classify.' . $styleset);
    }
}
