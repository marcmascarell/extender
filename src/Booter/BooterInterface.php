<?php

namespace Mascame\Extender\Booter;

use Mascame\Extender\Event\EventInterface;
use Mascame\Extender\ManagerInterface;

interface BooterInterface
{
    /**
     * @param $instance
     * @param $name
     * @return mixed
     */
    public function boot($instance, $name);

    /**
     * @param ManagerInterface $manager
     * @return mixed
     */
    public function setManager(ManagerInterface $manager);

    /**
     * @return ManagerInterface
     */
    public function getManager();

    /**
     * @return EventInterface
     */
    public function getEventDispatcher();

    /**
     * @param EventInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher);

    /**
     * @param $events
     * @param $instance
     * @param $name
     * @return mixed
     */
    public function addListeners($events, $instance, $name);
}
