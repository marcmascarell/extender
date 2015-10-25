<?php namespace Mascame\Extender\Booter;

class Booter extends AbstractBooter implements BooterInterface {

    /**
     * @param $instance
     * @param bool|false $isInstalled
     * @return bool
     */
    public function boot($instance, $isInstalled = false)
    {
        if (! $isInstalled) return false;

        return $instance->boot();
    }

    /**
     * @param $instance
     * @param $name
     */
    public function setProperties($instance, $name) {
        if (! $instance->namespace) $instance->namespace = $name;
    }
}
