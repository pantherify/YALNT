<?php

namespace Pantherify\YALNT\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Nova\Console\ResolvesStubPath;
use Pantherify\YALNT\Generators\LaravelNovaResourceGenerator;
use PhpParser\Builder\Interface_;

class ResourceGeneratorCommand extends Command
{
    use ResolvesStubPath;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yalnt:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Nova Resource, with Automatic Field Mapping';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hasDoctrine = interface_exists('Doctrine\DBAL\Driver');
        if (!$hasDoctrine) {
            $this->error('Doctrine not installed');
            $this->info("Install it with : \n composer require doctrine/dbal");
            return;
        }


        $schema = iterator_to_array(LaravelNovaResourceGenerator::parseModels());

        LaravelNovaResourceGenerator::generateResourceFiles($schema);

        return 0;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/nova/resource.stub');
    }
}
