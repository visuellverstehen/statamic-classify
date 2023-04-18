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
                'strong, em' => 'text-red',
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

        $this->assertEquals('<a href="#" class="link">Link</a>', $classified);
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

        $bardInput = <<<'EOT'
                     <li>Bad formatted HTML
                        <p>Some more</p>
                     </li>
                     EOT;

        $expedtedOutput = <<<'EOT'
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

    /** @test */
    public function a_nested_tag_will_be_replaced_and_wont_be_overwritten()
    {
        $config = [
            'default'  => [
                'p' => 'single',
                'li p' => 'nested',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = <<<'EOT'
                     <li>
                        <p>I am nested</p>
                     </li>
                     
                     <p>I am not</p>
                     EOT;

        $expedtedOutput = <<<'EOT'
                          <li>
                             <p class="nested">I am nested</p>
                          </li>
                          
                          <p class="single">I am not</p>
                          EOT;

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals($expedtedOutput, $classified);
    }

    /** @test */
    public function all_list_items_will_be_assigned_to_a_class()
    {
        $config = [
            'default'  => [
                'ul' => 'parent',
                'ul li' => 'nested',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = <<<'EOT'
                     <ul>
                        <li>I am nested 1</li>
                        <li>I am nested 2</li>
                        <li>I am nested 3</li>
                     </ul>
                     EOT;

        $expedtedOutput = <<<'EOT'
                          <ul class="parent">
                             <li class="nested">I am nested 1</li>
                             <li class="nested">I am nested 2</li>
                             <li class="nested">I am nested 3</li>
                          </ul>
                          EOT;

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals($expedtedOutput, $classified);
    }

    /** @test */
    public function deeply_nested_elements_can_be_targeted()
    {
        $config = [
            'default'  => [
                'a' => 'root-link',
                'ul' => 'parent',
                'ul li' => 'nested',
                'ul li a' => 'first-nested-links',
                'ul li ul' => 'nested-parent',
                'ul li ul li' => 'nested-item',
                'ul li ul li a' => 'deeply-nested-links',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = <<<'EOT'
 <a>Root link</a>
 <ul>
    <li><a>I am nested 1</a></li>
    <li><a>I am nested 2</a></li>
    <li><a>I am nested 3</a></li>
    <li>
        <ul>
            <li><a>I am nested 2.1</a></li>
            <li><a>I am nested 2.2</a></li>
            <li><a>I am nested 2.3</a></li>
        </ul>
    </li>
 </ul>
 EOT;

        $expedtedOutput = <<<'EOT'
<a class="root-link">Root link</a>
<ul class="parent">
   <li class="nested"><a class="first-nested-links">I am nested 1</a></li>
   <li class="nested"><a class="first-nested-links">I am nested 2</a></li>
   <li class="nested"><a class="first-nested-links">I am nested 3</a></li>
   <li class="nested">
       <ul class="nested-parent">
           <li class="nested-item"><a class="deeply-nested-links">I am nested 2.1</a></li>
           <li class="nested-item"><a class="deeply-nested-links">I am nested 2.2</a></li>
           <li class="nested-item"><a class="deeply-nested-links">I am nested 2.3</a></li>
       </ul>
   </li>
</ul>
EOT;

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals($expedtedOutput, $classified);
    }

    /** @test */
    public function deeply_nested_elements_can_be_targeted_with_css_selectors()
    {
        $config = [
            'default'  => [
                'a' => 'root-link',
                'ul' => 'parent',
                'ul li' => 'nested',
                'ul li:nth-child(2n+2)' => 'nested nested-even',
                'ul li a' => 'first-nested-links',
                'ul li ul' => 'nested-parent',
                'ul li ul li' => 'nested-item',
                'ul li ul li a' => 'deeply-nested-links',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = <<<'EOT'
 <a>Root link</a>
 <ul>
    <li><a>I am nested 1</a></li>
    <li><a>I am nested 2</a></li>
    <li><a>I am nested 3</a></li>
    <li>
        <ul>
            <li><a>I am nested 2.1</a></li>
            <li><a>I am nested 2.2</a></li>
            <li><a>I am nested 2.3</a></li>
        </ul>
    </li>
 </ul>
 EOT;

        $expedtedOutput = <<<'EOT'
<a class="root-link">Root link</a>
<ul class="parent">
   <li class="nested"><a class="first-nested-links">I am nested 1</a></li>
   <li class="nested nested-even"><a class="first-nested-links">I am nested 2</a></li>
   <li class="nested"><a class="first-nested-links">I am nested 3</a></li>
   <li class="nested nested-even">
       <ul class="nested-parent">
           <li class="nested-item"><a class="deeply-nested-links">I am nested 2.1</a></li>
           <li class="nested-item"><a class="deeply-nested-links">I am nested 2.2</a></li>
           <li class="nested-item"><a class="deeply-nested-links">I am nested 2.3</a></li>
       </ul>
   </li>
</ul>
EOT;

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals($expedtedOutput, $classified);
    }

    /** @test */
    public function root_link_class_is_applied_to_all_but_first_list_item_links()
    {
        $config = [
            'default'  => [
                'a' => 'root-link',
                'ul' => 'parent',
                'ul li' => 'nested',
                'ul li a' => 'first-nested-links',
                'ul li ul' => 'nested-parent',
                'ul li ul li' => 'nested-item',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = <<<'EOT'
 <a>Root link</a>
 <ul>
    <li><a>I am nested 1</a></li>
    <li><a>I am nested 2</a></li>
    <li><a>I am nested 3</a></li>
    <li>
        <ul>
            <li><a>I am nested 2.1</a></li>
            <li><a>I am nested 2.2</a></li>
            <li><a>I am nested 2.3</a></li>
        </ul>
    </li>
 </ul>
 EOT;

        $expedtedOutput = <<<'EOT'
<a class="root-link">Root link</a>
<ul class="parent">
   <li class="nested"><a class="first-nested-links">I am nested 1</a></li>
   <li class="nested"><a class="first-nested-links">I am nested 2</a></li>
   <li class="nested"><a class="first-nested-links">I am nested 3</a></li>
   <li class="nested">
       <ul class="nested-parent">
           <li class="nested-item"><a class="root-link">I am nested 2.1</a></li>
           <li class="nested-item"><a class="root-link">I am nested 2.2</a></li>
           <li class="nested-item"><a class="root-link">I am nested 2.3</a></li>
       </ul>
   </li>
</ul>
EOT;

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals($expedtedOutput, $classified);
    }

    /** @test */
    public function adding_an_explicit_body_root_does_not_break_selectors()
    {
        $config = [
            'default'  => [
                'body a span' => 'text-red',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = '<a href="#">Some<span>thing</span></a>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<a href="#">Some<span class="text-red">thing</span></a>', $classified);
    }

    /** @test */
    public function adding_excessive_whitespace_produces_compatible_selectors()
    {
        $config = [
            'default'  => [
                '   a       span    ' => 'text-red',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = '<a href="#">Some<span>thing</span></a>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<a href="#">Some<span class="text-red">thing</span></a>', $classified);
    }

    /** @test */
    public function adding_explicit_greater_than_symbols_doesnt_double_up_internal_selectors()
    {
        $config = [
            'default'  => [
                ' body >    a>span    ' => 'text-red',
            ],
        ];

        Config::set('classify', $config);

        $bardInput = '<a href="#">Some<span>thing</span></a>';

        $classified = $this->classify->index($bardInput, [], []);

        $this->assertEquals('<a href="#">Some<span class="text-red">thing</span></a>', $classified);
    }
    
    /** @test */
    public function a_chained_selector_will_be_recognized()
    {
        $bardInput = '<strong>wonderful!</strong>';
        
        $classified = $this->classify->index($bardInput, [], []);
        
        $this->assertEquals('<strong class="text-red">wonderful!</strong>', $classified);
    }
}
