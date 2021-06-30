<?php

namespace VV\Classify;

class Tag
{
    /*
     * Contains the parent tag, which might be the only or last tag defined in the config.
     * From a list like `ul li p` it would fx be `p`.
     */
    public string $tag;

    /*
     * If multiple tags have been defined, those are the tags before the parent tag.
     * From a list like `ul li p` it would fx be `ul li`.
     */
    public array $before;

    /*
     * Contains the class list as defined in the config.
     */
    public string $classes;

    /*
     * Defines the number of tags, including the parent tag.
     */
    public int $count;

    public function __construct(string $tags, string $classes)
    {
        $tags = $this->convertTagsToArray($tags);
        $this->count = count($tags);
        $this->classes = $classes;
        $this->setTags($tags);
    }

    /*
     * It's easier to handle tags as an array, so we'll convert them.
     */
    private function convertTagsToArray(string $tags): array
    {
        return explode(' ', $tags);
    }

    /*
     * Set the parent tag and save all other tags in the before attribute.
     */
    private function setTags(array $tags): void
    {
        $this->tag = $this->setParentTagAndRemoveFromOriginalArray($tags);
        $this->before = $tags;
    }

    /*
     * Get the key from the last array and splice / remove it.
     */
    private function setParentTagAndRemoveFromOriginalArray(array &$tags)
    {
        return array_splice($tags, array_key_last($tags))[0];
    }
}
