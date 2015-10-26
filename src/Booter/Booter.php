<?php namespace Mascame\Extender\Booter;

class Booter extends AbstractBooter implements BooterInterface {

    /**
     * @param $instance
     * @param $name
     * @return bool
     */
    public function boot($instance, $name)
    {
        if (! $this->getManager()->isInstalled($name)) return false;

        return $instance->boot();
    }

    /**
     * @param $instance
     * @param $name
     */
    public function beforeBooting($instance, $name) {
        if (! $instance->namespace) $instance->namespace = $name;
    }
}
