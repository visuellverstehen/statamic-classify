<?php

namespace VV\Classify\Tests\Unit;

use Illuminate\Support\Facades\Config;
use VV\Classify\Commands\Export;
use VV\Classify\Tests\TestCase;

class ExportTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $config = [
            'default' => [
                'h1' => 'headline',
                'a' => 'link',
                'p' => 'text-base',
                'li p' => 'text-sm',
            ],
            'profile-2' => [
                'a' => 'root-link',
                'ul' => 'parent',
                'ul li' => 'nested',
                'ul li a' => 'first-nested-links',
                'ul li ul' => 'nested-parent',
                'ul li ul li' => 'nested-item',
            ],
        ];

        Config::set('classify', $config);
    }

    /** @test */
    public function export_discovers_all_class_names()
    {
        $configuration = Export::getConfigurationContents();

        $expectedConfiguration = <<<'EOT'
module.exports.classes = [
    "headline",
    "link",
    "text-base",
    "text-sm",
    "root-link",
    "parent",
    "nested",
    "first-nested-links",
    "nested-parent",
    "nested-item"
];
EOT;

        $this->assertEquals($expectedConfiguration, $configuration);
    }
}
