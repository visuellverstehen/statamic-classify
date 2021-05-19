<?php

namespace VV\Classify;

interface ClassifyParser
{
    public function parse(string $tag, string $class, string $value): string;
}
