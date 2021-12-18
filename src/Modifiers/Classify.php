<?php

namespace VV\Classify\Modifiers;

use Statamic\Modifiers\Modifier;
use VV\Classify\ClassifyParser;
use VV\Classify\Tag;

class Classify extends Modifier
{
    /**
     * If only {{ classify }} is provided, the default style set will be applied.
     *
     * You can define a specified styleset as well by defining {{ classify:foo }}
     * In case the set can not be found, the original value will get returned.
     */
    public function index($value, $params, $context)
    {
        $styleSet = $params[0] ?? 'default';

        if ((! $this->isStyleSetAvailable($styleSet)) || (null === $value)) {
            return $value;
        }

        /*
         * Convert style segment information into the Tag class.
         * They will get Sorted by count to parse nested tags first.
         */

        $segments = collect($this->getStyleSegments($styleSet))
            ->map(fn ($classes, $tags) => new Tag($tags, $classes))
            ->sortBy('count');

        $segments->each(function ($segment) use (&$value) {
            $value = app(ClassifyParser::class)->parse($segment, $value);
        });

        return $value;
    }

    /**
     * Check if the given style set is available in the config.
     */
    private function isStyleSetAvailable(string $styleSet): bool
    {
        return config()->has('classify.'.$styleSet);
    }

    /*
     * get the style set from the config.
     */
    private function getStyleSegments($styleSet): array
    {
        return config('classify.'.$styleSet);
    }
}
