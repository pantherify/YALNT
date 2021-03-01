<?php

namespace Pantherify\YALNT\Parsers;

use Doctrine\DBAL\Schema\Column;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Pantherify\YALNT\Enums\NovaFieldEnum;
use Pantherify\YALNT\Exceptions\ParserException;
use ReflectionClass;

/**
 * Class EloquentModelParser
 * @package Pantherify\GraphQLGenerator\src\Parsers
 */
class EloquentModelParser
{
    /**
     * @param Model $model
     * @return array
     * @throws ParserException
     */
    public static function getTableProperties(Model $model): array
    {
        $properties = array();
        $table = $model->getConnection()->getTablePrefix() . $model->getTable();
        $schema = $model->getConnection()->getDoctrineSchemaManager();

        try {
            $databasePlatform = $schema->getDatabasePlatform();
            $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
        } catch (Exception $e) {
            throw ParserException::doctrineProblem($e);
        }

        $columns = $schema->listTableColumns($table);
        $keyName = $model->getKeyName();


        foreach ($columns as $column) {
            $name = $column->getName();
            $type = EloquentModelParser::mapDbTypeToGraphType($column, $keyName);
            if (config('yalnt.generation.skipIds') && $type === NovaFieldEnum::$ID) {
                continue;
            }

            if (config('yalnt.generation.skipTimeStamps') &&
            (Str::contains($name, 'updated_at') || Str::contains($name, 'created_at') || Str::contains($name, 'deleted_at'))) {
                continue;
            }
            
            if (Str::contains($name, '_id')) {
                continue;
            }

            $properties[] = array(
                'name' => $name,
                'type' => $type
            );
        }

        return $properties;
    }


    /**
     * @param Column $column
     * @param String $key
     * @return String
     * @throws ParserException
     */
    private static function mapDbTypeToGraphType(Column $column, string $key): string
    {
        $type = $column->getType()->getName();
        switch ($type) {
            case 'float':
            case 'double':
            case 'integer':
            case 'bigint':
            case 'smallint':
                if ($column->getName() == $key) {
                    return NovaFieldEnum::$ID;
                }
                if (str_contains($column->getName(), 'currency')) {
                    return NovaFieldEnum::$CURRENCY;
                }
                return NovaFieldEnum::$NUMBER;
            
            case 'boolean':
            case 'bool':
                return NovaFieldEnum::$BOOLEAN;

            case 'string':
                if (str_contains($column->getName(), 'country')) {
                    return NovaFieldEnum::$COUNTRY;
                }
                if (str_contains($column->getName(), 'path')) {
                    return NovaFieldEnum::$FILE;
                }
                if (str_contains($column->getName(), 'image')) {
                    return NovaFieldEnum::$IMAGE;
                }
                return NovaFieldEnum::$TEXT;

            case 'text':
                if (str_contains($column->getName(), 'content')) {
                    return NovaFieldEnum::$TRIX;
                }
                return NovaFieldEnum::$TEXTAREA;

            case 'jsonb':
                return NovaFieldEnum::$KEYVALUE;

            case 'date':
                return NovaFieldEnum::$DATE;

            case 'datetime':
                return NovaFieldEnum::$DATETIME;

            default:
                throw ParserException::columnTypeMapping($column->getName(), $type);
        }
    }


    /**
     * @param ReflectionClass $class
     * @param $name
     * @return array
     * @throws ParserException
     */
    public static function getRelations(ReflectionClass $class, $name)
    {
        $relations = array();
        $model = Application::getInstance()->make($name);


        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != get_class($model) ||
                !empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__
            ) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                if ($return instanceof Relation) {
                    $relations[$method->getName()] = array(
                        'type' => (new ReflectionClass($return))->getShortName(),
                        'model' => (new ReflectionClass($return->getRelated()))->getShortName(),
                        'rel' => $method->getShortName()
                    );
                    ;
                }
            } catch (\Exception $e) {
                throw ParserException::getRelationError($class->getShortName(), $e);
            }
        }
        return $relations;
    }


    private static function mapProperties($properties, $props)
    {
        $output = [];

        if (count($props) > 0) {
            $output = array_merge(array_filter($properties, function ($p) use ($props) {
                return in_array($p['name'], $props, true);
            }), $output);
        } else {
            $output = $properties;
        }

        return $output;
    }
}
