<?php namespace Mascame\Extender\Event;

/**
 * Wrapper for Laravel's event system
 *
 * Class LaravelEventDispatcher
 * @package Mascame\Extender\Event
 */
class LaravelEventDispatcher implements EventInterface {

    /**
     * @return \Illuminate\Events\Dispatcher
     */
    protected function getInstance() {
        return app('events');
    }

    /**
     * @param $eventName
     * @param $listener
     * @return mixed
     */
    public function listen($eventName, $listener)
    {
        $this->getInstance()->listen($eventName, $listener);
    }

    /**
     * @param $eventName
     * @return mixed
     */
    public function fire($eventName)
    {
        return $this->getInstance()->fire($eventName);
    }


}
