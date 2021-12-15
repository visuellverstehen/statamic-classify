<?php

namespace VV\Classify\Commands;

use Illuminate\Console\Command;

class Export extends Command
{
    protected $signature = 'classify:export';
    protected $description = 'Generates a JSON configuration file containing all Classify class names.';

    /**
     * Generates a JavaScript configuration file containing all Classify class names.
     */
    public static function getConfigurationContents(): string
    {
        $allConfig = config('classify');
        $classNames = [];

        foreach ($allConfig as $config) {
            foreach ($config as $classList) {
                $classes = collect(explode(' ', $classList))->reject(function ($class) {
                    return strlen(trim($class)) == 0;
                })->values()->all();

                foreach ($classes as $class) {
                    if (! in_array($class, $classNames)) {
                        $classNames[] = $class;
                    }
                }
            }
        }

        return 'module.exports.classes = '.json_encode($classNames, JSON_PRETTY_PRINT).';';
    }

    public function handle()
    {
        file_put_contents(base_path('classify.config.js'), self::getConfigurationContents());
    }
}