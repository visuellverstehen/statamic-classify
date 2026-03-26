<?php

use Illuminate\Support\Facades\Config;
use VV\Classify\Modifiers\Classify;

beforeEach(function () {
    Config::set('classify', [
        'default' => [
            'h1' => 'headline',
            'a'  => 'link',
            'p' => 'text-base',
            'li p' => 'text-sm',
            'strong, em' => 'text-red',
        ],
    ]);

    $this->classify = new Classify();
});

it('applies class to a single tag', function () {
    expect($this->classify->index('<h1>Hello world</h1>', [], []))
        ->toBe('<h1 class="headline">Hello world</h1>');
});

it('applies class to a tag with existing attributes', function () {
    expect($this->classify->index('<a href="#">Link</a>', [], []))
        ->toBe('<a href="#" class="link">Link</a>');
});

it('recognizes a nested selector', function () {
    Config::set('classify', ['default' => ['li p' => 'text-sm']]);

    expect($this->classify->index('<li><p>Some text</p></li>', [], []))
        ->toBe('<li><p class="text-sm">Some text</p></li>');
});

it('recognizes a nested tag with text in between', function () {
    Config::set('classify', ['default' => ['a span' => 'text-red']]);

    expect($this->classify->index('<a>Some <span>styled</span> text</a>', [], []))
        ->toBe('<a>Some <span class="text-red">styled</span> text</a>');
});

it('handles nested tags across multiple lines', function () {
    Config::set('classify', ['default' => ['li p' => 'text-bold']]);

    $input = <<<'EOT'
                     <li>Bad formatted HTML
                        <p>Some more</p>
                     </li>
                     EOT;

    $expected = <<<'EOT'
                          <li>Bad formatted HTML
                             <p class="text-bold">Some more</p>
                          </li>
                          EOT;

    expect($this->classify->index($input, [], []))->toBe($expected);
});

it('parses nested tags with already defined classes', function () {
    Config::set('classify', ['default' => ['a span' => 'text-red']]);

    expect($this->classify->index('<a href="#">Some<span>thing</span></a>', [], []))
        ->toBe('<a href="#">Some<span class="text-red">thing</span></a>');
});

it('replaces nested selectors without overwriting simpler ones', function () {
    Config::set('classify', ['default' => ['p' => 'single', 'li p' => 'nested']]);

    $input = <<<'EOT'
                     <li>
                        <p>I am nested</p>
                     </li>
                     
                     <p>I am not</p>
                     EOT;

    $expected = <<<'EOT'
                          <li>
                             <p class="nested">I am nested</p>
                          </li>
                          
                          <p class="single">I am not</p>
                          EOT;

    expect($this->classify->index($input, [], []))->toBe($expected);
});

it('assigns classes to all matching list items', function () {
    Config::set('classify', ['default' => ['ul' => 'parent', 'ul li' => 'nested']]);

    $input = <<<'EOT'
                     <ul>
                        <li>I am nested 1</li>
                        <li>I am nested 2</li>
                        <li>I am nested 3</li>
                     </ul>
                     EOT;

    $expected = <<<'EOT'
                          <ul class="parent">
                             <li class="nested">I am nested 1</li>
                             <li class="nested">I am nested 2</li>
                             <li class="nested">I am nested 3</li>
                          </ul>
                          EOT;

    expect($this->classify->index($input, [], []))->toBe($expected);
});

it('targets deeply nested elements', function () {
    Config::set('classify', ['default' => [
        'a' => 'root-link',
        'ul' => 'parent',
        'ul li' => 'nested',
        'ul li a' => 'first-nested-links',
        'ul li ul' => 'nested-parent',
        'ul li ul li' => 'nested-item',
        'ul li ul li a' => 'deeply-nested-links',
    ]]);

    $input = <<<'EOT'
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

    $expected = <<<'EOT'
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

    expect($this->classify->index($input, [], []))->toBe($expected);
});

it('targets deeply nested elements with css pseudo-selectors', function () {
    Config::set('classify', ['default' => [
        'a' => 'root-link',
        'ul' => 'parent',
        'ul li' => 'nested',
        'ul li:nth-child(2n+2)' => 'nested nested-even',
        'ul li a' => 'first-nested-links',
        'ul li ul' => 'nested-parent',
        'ul li ul li' => 'nested-item',
        'ul li ul li a' => 'deeply-nested-links',
    ]]);

    $input = <<<'EOT'
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

    $expected = <<<'EOT'
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

    expect($this->classify->index($input, [], []))->toBe($expected);
});

it('falls back to root selector when no nested match exists', function () {
    Config::set('classify', ['default' => [
        'a' => 'root-link',
        'ul' => 'parent',
        'ul li' => 'nested',
        'ul li a' => 'first-nested-links',
        'ul li ul' => 'nested-parent',
        'ul li ul li' => 'nested-item',
    ]]);

    $input = <<<'EOT'
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

    $expected = <<<'EOT'
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

    expect($this->classify->index($input, [], []))->toBe($expected);
});

it('handles an explicit body root in selectors', function () {
    Config::set('classify', ['default' => ['body a span' => 'text-red']]);

    expect($this->classify->index('<a href="#">Some<span>thing</span></a>', [], []))
        ->toBe('<a href="#">Some<span class="text-red">thing</span></a>');
});

it('normalizes excessive whitespace in selectors', function () {
    Config::set('classify', ['default' => ['   a       span    ' => 'text-red']]);

    expect($this->classify->index('<a href="#">Some<span>thing</span></a>', [], []))
        ->toBe('<a href="#">Some<span class="text-red">thing</span></a>');
});

it('handles explicit greater-than symbols without doubling selectors', function () {
    Config::set('classify', ['default' => [' body >    a>span    ' => 'text-red']]);

    expect($this->classify->index('<a href="#">Some<span>thing</span></a>', [], []))
        ->toBe('<a href="#">Some<span class="text-red">thing</span></a>');
});

it('recognizes comma-separated selectors', function () {
    expect($this->classify->index('<strong>wonderful!</strong>', [], []))
        ->toBe('<strong class="text-red">wonderful!</strong>');
});
