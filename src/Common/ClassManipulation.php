<?php

namespace Pantherify\YALNT\Common;

use Illuminate\Support\Str;

/**
 * Class ClassManipulation
 * @package Pantherify\YALNT\Common
 */
class ClassManipulation
{
    /**
     * @param String $namespace
     * @return Array
     */
    public static function getClassesByNamespace(String $namespace): array
    {
        $composer = require base_path('/vendor/autoload.php');
        $classes = array_keys($composer->getClassMap());

        $namespace = Str::start(strtoupper($namespace), '\\');

        return array_filter($classes, static function ($class) use ($namespace) {
            $className = Str::start(strtoupper($class), '\\');

            return
                0 === strpos($className, $namespace) &&
                false === stripos($className, 'Abstract') &&
                false === stripos($className, 'Interface');
        });
    }

    /**
     * @param ReflectionClass $class
     * @return bool
     */
    public static function isAllOk(\ReflectionClass $class): bool
    {
        $isSubClass = $class->isSubclassOf('Illuminate\Database\Eloquent\Model');
        $isAbstract = $class->isInstantiable();

        return
            $isSubClass &&
            $isAbstract;
    }

    /**
     * @param ReflectionClass[] $classes
     * @return Bool
     */
    public static function hasTrait(array $classes): Bool
    {
        foreach ($classes as $class) {
            if ($class->getShortName() == 'GraphQLModel') {
                return true;
            }
        }

        return false;
    }
}
