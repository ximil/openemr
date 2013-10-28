<?php
namespace Application\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Listener extends AbstractActionController implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    protected $applicationTable;
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    { 	
      $sharedEvents      = $events->getSharedManager();
      $this->listeners[] = $events->attach('aclcheckEvent', array($this, 'onAclcheckEvent'));
    }
    

    public function detach(EventManagerInterface $events)
    {
      foreach ($this->listeners as $index => $listener) {
        if ($events->detach($listener)) {
          unset($this->listeners[$index]);
        }
      }
    }
   
    /**
     * Language converter
     * @param string $str
     * @return string
     */
    public function zht($str)
    {
      return xlt($str);
    }
}