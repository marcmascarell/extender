<?php namespace Mascame\Extender\Event;

interface EventInterface {

    /**
     * @param $eventName
     * @param $listener
     * @return mixed
     */
   public function listen($eventName, $listener);

    /**
     * @param $eventName
     * @return mixed
     */
   public function fire($eventName);


}
