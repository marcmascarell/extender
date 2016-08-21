<?php namespace Mascame\Extender\Event;

interface EventInterface {

    /**
     * @param $eventName
     * @param \Closure $callback
     * @return mixed
     */
    public function listen($eventName, \Closure $callback);

    /**
     * @param $eventName
     * @param array $params
     * @return mixed
     */
    public function fire($eventName, $params = []);

}
