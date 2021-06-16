# Call event rule

1. First need create event object

```php
<?php

namespace App\Event;

use DrinksIt\RuleEngineBundle\Event\RuleEventInterface;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;

class MyCustomRuleEvent implements RuleEventInterface 
{
    
    public function run(iterable $data, RuleEntityInterface $ruleEntity): void
    {
     // TODO: Implement run() method.
    }

    public static function getName() : string
    {
     // TODO: Implement getName() method.
    }

}

```


2. Call use `EventDispatcherInterface` Symfony [Events and Event Listeners symfony.com](https://symfony.com/doc/current/event_dispatcher.html)

```php

use DrinksIt\RuleEngineBundle\Event\ObserverEntityEvent;
use DrinksIt\RuleEngineBundle\Event\RuleEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnotherService {
    
    protected $dispatcher;
    
    public function __construct(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }
    
    public function runService() {
        $this->dispatcher->dispatch(new ObserverEntityEvent([
            new Entity(),
            // ... 
        ], 'App\Event\MyCustomRuleEvent'), RuleEvent::EVENT);
    }
}

```
