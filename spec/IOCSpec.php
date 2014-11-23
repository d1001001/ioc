<?php


namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestClass {}
class HasDeps{
    private $dep;
    private $dep2;
    private $dep3;
    public function __construct(DepClass $dep, DepClass2 $dep2, DepClass3 $dep3){
        $this->dep = $dep;
        $this->dep2 = $dep2;
        $this->dep3 = $dep3;
    }
}
class DepClass{}
class DepClass2{}
class DepClass3{}
class HasDeepDeps{
    public function __construct(DepDeepClass $dep){
    }
}
class DepDeepClass{
    public function __construct(DepDeepClass2 $dep, DepDeepClass3 $dep2){
    }
}
class DepDeepClass2{}
class DepDeepClass3{}
interface TestInterface {}
class ConcreteTest implements TestInterface {}

class IOCSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('IOC');
    }

    public function it_returns_instance_when_no_dependencies()
    {
        $this->make('spec\\TestClass')->shouldHaveType('spec\\TestClass');
    }

    public function it_fills_constructor_with_dependencies()
    {
        $this->make('spec\\HasDeps')->shouldHaveType('spec\\HasDeps');
    }

    public function it_recursively_resolves_all_dependencies()
    {
        $this->make('spec\\HasDeepDeps')->shouldHaveType('spec\\HasDeepDeps');
    }

    public function it_receives_binding_to_an_interface()
    {
        $this->bind('spec\\TestInterface', 'spec\\ConcreteTest');
        $this->make('spec\\TestInterface')->shouldHaveType('spec\\ConcreteTest');
    }

    public function it_can_bind_closure_to_interface()
    {
        $this->bind('spec\\TestInterface', function(){
            return new ConcreteTest;
        });
        $this->make('spec\\TestInterface')->shouldHaveType('spec\\ConcreteTest');
    }

    public function it_gets_binding_for_interface()
    {
        $this->bind('spec\\TestInterface', 'spec\\ConcreteTest');
        $this->getBinding('spec\\TestInterface')->shouldReturn('spec\\ConcreteTest');
    }
}

