<?php

namespace VV\Classify\Tests\Unit;

use Illuminate\Support\Facades\Config;
use VV\Classify\Modifiers\Classify;
use VV\Classify\Tests\TestCase;

class ClassifyTest extends TestCase
{
    /**
     * @var Classify
     */
    protected $classify;

    public function setUp(): void
    {
        parent::setUp();

        $config = [
            'default'  => [
                'h1' => 'headline',
            ],
        ];

        Config::set('classify', $config);

        $this->classify = new Classify();


    }

    /** @test */
    public function test()
    {
        $value = '<h1> Hello world</h1>';
        $params = [];
        $context = [];

        $classified = $this->classify->index($value, $params, $context);

        $expectedClassifiedString = '<h1 class="headline"> Hello world</h1>';

        $this->assertEquals($classified, $expectedClassifiedString);
    }
}
