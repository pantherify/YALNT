<?php

namespace Pantherify\YALNT\Generators;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Pantherify\YALNT\Common\ClassManipulation;
use Pantherify\YALNT\Common\PathManipulation;
use Pantherify\YALNT\Exceptions\GeneratorException;
use Pantherify\YALNT\Parsers\EloquentModelParser;
use Symfony\Component\Intl\Exception\NotImplementedException;

class LaravelNovaResourceGenerator
{
    public static function base_path()
    {
        return base_path(PathManipulation::joinPaths(config('yalnt.nova.path'), '/')) ;
    }

    public static function parseModels()
    {
        $namespace = config('yalnt.models.namespace', "App\\Models");
        $classes = ClassManipulation::getClassesByNamespace($namespace);

        foreach ($classes as $class) {
            if (!class_exists($class)) {
                continue;
            }

            try {
                $reflection_class = new \ReflectionClass($class);

                if (config('yalnt.generation.skipUser') && $reflection_class->getShortName() === 'User') {
                    continue;
                }

                if (!ClassManipulation::isAllOk($reflection_class)) {
                    continue;
                }


                $model = Application::getInstance()->make($class);

                $schemaName = $reflection_class->getShortName();
                $properties = EloquentModelParser::getTableProperties($model);
                $relations = EloquentModelParser::getRelations($reflection_class, $class);

                if (count($properties) === 0) {
                    echo "$schemaName is Empty... Skipping it!! \n";
                    continue;
                }


                $fields = array_map(function ($prop) {
                    return $prop["type"];
                }, $properties);


                foreach ($relations as $key => $rel) {
                    array_push($fields, $rel["type"]);
                }

                yield array(
                    "name" => $schemaName,
                    "namespace" => $reflection_class->getName(),
                    "space" => config('yalnt.nova.namespace', 'App\\Nova'),
                    "fields" => array_unique($fields),
                    "attributes" => $properties,
                    "relations" => $relations
                );
            } catch (\Exception $e) {
                throw GeneratorException::FlowError($e);
            }
        }
    }

    public static function generateResourceFiles(array $resourceFiles = [], array $opt = [])
    {
        foreach ($resourceFiles as $resource) {
            $output = "<?php\n" . View::make('yalnt::laravel-nova-resource', ["model" => $resource])->render();


            File::put(LaravelNovaResourceGenerator::base_path() . "/${resource['name']}.php", $output);

            echo LaravelNovaResourceGenerator::base_path() . "/${resource['name']}.php CREATED!"  . "\n";
        }
    }
}
