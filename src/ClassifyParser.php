<?php

namespace VV\Classify;

interface ClassifyParser
{
    public function parse(Tag $tag, string $value): string;
}
