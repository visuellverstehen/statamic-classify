<?php

namespace VV\Classify;

interface ClassifyParser
{
    public function parse(string $tags, string $classes, string $value): string;
}
