<?php

namespace VV\Classify\Tests\Unit;

use Illuminate\Support\Facades\Config;
use VV\Classify\Modifiers\Classify;
use VV\Classify\Tests\TestCase;

class ClassifyTest extends TestCase
{
    protected Classify $classify;

    public function setUp(): void
    {
        parent::setUp();

        $config = [
            'default'  => [
                'h1' => 'headline',
                'a'  => 'link'
            ],
        ];

        Config::set('classify', $config);

        $this->classify = new Classify();
    }

    /** @test */
    public function a_single_tag_will_be_extended_with_the_given_class_names()
    {
        $bardInput = '<h1>Hello world</h1>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<h1 class="headline">Hello world</h1>', $classified);
    }

    /** @test */
    public function a_singletag_will_be_extended_with_the_given_class_names()
    {
        $bardInput = '<a href="#">Link</a>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<a class="link" href="#">Link</a>', $classified);
    }
}
