<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 11/23/14
 * Time: 2:12 PM
 */

class IOC {

    private $bindings = [];

    public function make($className)
    {
        $className = $this->getRealClass( $className )->getName();
        $class = new ReflectionClass($className);
        if( ! $this->hasConstructor($class)) {
            return new $className;
        }
        $deps = $this->resolveDependencies($class);
        $result = new ReflectionClass($className);
        return $result->newInstanceArgs($deps);
    }

    private function resolveDependencies(ReflectionClass $class)
    {
        $deps = [];
        foreach( $depName = $class->getConstructor()->getParameters() as $dependency )
        {
            $depName = $dependency->getClass()->name;
            $depReflection = $this->getRealClass($depName);
            if( $this->hasConstructor($depReflection) ) {
                $deps[] = $depReflection->newInstanceArgs($this->resolveDependencies($depReflection));
                continue;
            }
            $depRealName = $depReflection->getName();
            $deps[] = new $depRealName;
        }
        return $deps;
    }

    private function hasConstructor(ReflectionClass $class)
    {
        return !is_null($class->getConstructor());
    }


    public function bind($interface, $implementation)
    {
        $this->bindings[$interface] = $implementation;
    }

    public function getBinding($interface)
    {
        if( ! isset($this->bindings[$interface]) ){
            throw new Exception("Interface $interface does not have binded class.");
        }
        if( is_callable($this->bindings[$interface]) ) {
            return $this->bindings[$interface]();
        }
        return $this->bindings[$interface];
    }
    private function getRealClass($depName)
    {
        $ref = new ReflectionClass($depName);
        if( ! $ref->isInterface() ) {
            return $ref;
        }
        return new ReflectionClass( $this->getBinding($depName) );
    }
}
