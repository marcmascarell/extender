<?php namespace Mascame\Extender\Booter;

use Mascame\Extender\Event\Eventable;
use Mascame\Extender\ManagerInterface;

abstract class AbstractBooter implements BooterInterface {
    use Eventable;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param $instance
     * @param $name
     * @throws \Exception
     */
    public function addListeners($events, $instance, $name)
    {
        foreach ($events as $eventName => $eventMethod) {

            if (method_exists($instance, $eventMethod)) {
                $this->listen($eventName . '.' . $name, function() use ($instance, $eventMethod) {
                    $instance->{$eventMethod}();
                });
            }

        }
    }
}
