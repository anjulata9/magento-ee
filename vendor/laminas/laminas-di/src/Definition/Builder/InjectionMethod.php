<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Definition\Builder;

use Laminas\Di\Di;

/**
 * Definitions for an injection endpoint method
 */
class InjectionMethod
{
    /**
     * @var string|null
     */
    protected $name = null;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @param  string|null $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string          $name
     * @param  string|null     $class
     * @param  mixed|null      $isRequired
     * @param  mixed|null      $default
     * @return InjectionMethod
     */
    public function addParameter($name, $class = null, $isRequired = null, $default = null)
    {
        $this->parameters[] = [
            $name,
            $class,
            self::detectMethodRequirement($isRequired),
            $default,
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @param mixed $requirement
     * @return int
     */
    public static function detectMethodRequirement($requirement)
    {
        if (is_bool($requirement)) {
            return $requirement ? Di::METHOD_IS_REQUIRED : Di::METHOD_IS_OPTIONAL;
        }

        if (null === $requirement) {
            //This is mismatch to ClassDefinition::addMethod is it ok ? is optional?
            return Di::METHOD_IS_REQUIRED;
        }

        if (is_int($requirement)) {
            return $requirement;
        }

        if (is_string($requirement)) {
            switch (strtolower($requirement)) {
                default:
                case "require":
                case "required":
                    return Di::METHOD_IS_REQUIRED;
                case "aware":
                    return Di::METHOD_IS_AWARE;
                case "optional":
                    return Di::METHOD_IS_OPTIONAL;
                case "constructor":
                    return Di::METHOD_IS_CONSTRUCTOR;
                case "instantiator":
                    return Di::METHOD_IS_INSTANTIATOR;
                case "eager":
                    return Di::METHOD_IS_EAGER;
            }
        }
        return 0;
    }
}