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
                'a'  => 'link',
                'p' => 'text-base',
                'li p' => 'text-sm',
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

    /** @test */
    public function a_nested_tag_will_be_recognized()
    {
        $config = [
            'default'  => [
                'li p' => 'text-sm',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = '<li><p>Some text</p></li>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<li><p class="text-sm">Some text</p></li>', $classified);
    }


    /** @test */
    public function a_nested_tag_with_text_inbetween_will_be_recognized()
    {
        $config = [
            'default'  => [
                'a span' => 'text-red',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = '<a>Some <span>styled</span> text</a>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<a>Some <span class="text-red">styled</span> text</a>', $classified);
    }

    /** @test */
    public function a_nested_tag_with_text_inbetween_will_be_recognized_on_multilines_as_well()
    {
        $config = [
            'default'  => [
                'li p' => 'text-bold',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = <<<EOT
                     <li>Bad formatted HTML
                        <p>Some more</p>
                     </li>
                     EOT;

        $expedtedOutput = <<<EOT
                          <li>Bad formatted HTML
                             <p class="text-bold">Some more</p>
                          </li>
                          EOT;


        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals($expedtedOutput, $classified);
    }

    /** @test */
    public function a_nested_tag_with_already_defined_classes_will_be_parsed_correctly()
    {
        $config = [
            'default'  => [
                'a span' => 'text-red',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = '<a href="#">Some<span>thing</span></a>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<a href="#">Some<span class="text-red">thing</span></a>', $classified);
    }
}
