<?php

namespace VV\Classify\Tags;

use Statamic\Tags\Tags;

class Classify extends Tags
{
    /**
     * {{ classify:[element] }}.
     *
     * Returns the style definitions of the requested element.
     * Takes a styleset as optional `styleset` parameter.
     * e. g. {{ classify:h1 styleset="blog" }}
     */
    public function __call($method, $args): string
    {
        $element = (string) explode(':', $this->tag, 2)[1];
        
        return $this->getElementClasses($element);
    }
    
    
    /**
     * {{ classify element="[element]" }}.
     *
     * Returns the style definitions of the requested element.
     * Takes a styleset as optional `styleset` parameter.
     * e. g. {{ classify element="h1" styleset="blog" }}
     */
    public function index(): string
    {
        $element = (string) $this->params->get('element');
        
        return $this->getElementClasses($element);
    }
    
    /**
     * Gets the element style information from config.
     */
    private function getElementClasses(string $element): string
    {
        $styleset = $this->params->get('styleset') ?? 'default';
        
        if ($classes = config("classify.{$styleset}.{$element}")) {
            return $classes;
        }
        
        foreach (config("classify.{$styleset}") as $tag => $classes) {
            if (! strpos($tag, ',')) {
                continue;
            }
            
            $index = collect(explode(',', $tag))
                ->transform(fn ($item) => trim($item))
                ->search($element);
            
            if ($index !== false) {
                return $classes;
            }
        }
        
        return '';
    }
}