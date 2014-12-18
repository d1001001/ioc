ioc
===

Simple class for automatic resolving of PHP classes


Usage
----
Let's create two simple classes with constructor injected dependencies:
```
class Robot {
  private $vacuumCleaner;
  
  public function __construct( VacuumCleaner $vacuumCleaner )
  {
    $this->vacuumCleaner = $vacuumCleaner;
  }
  
  pulic function cleanHouse()
  {
    $this->vacuumCleaner->cleanRoom( 'bathroom' );
    $this->vacuumCleaner->cleanRoom( 'bedroom' );
    $this->vacuumCleaner->cleanRoom( 'kitchen' );
    echo "The house is clean now!\n";
  }
}

class VacuumCleaner {
  public function cleanRoom( $room )
  {
    echo "Cleaning room $room...\n";
  }
}
```
Now we can use the IOC class to automatically resolve the dependency without need to inject the class manually.
```
$ioc = new IOC;

$robot = $ioc->make('Robot');
$robot->cleanHouse();
```

Note that this will even work with interfaces if you provide desired implementation.
```
$ioc->bind('VacuumCleanerInterface', 'TurboVacuumCleaner');
$robot = $ioc->make('Robot');
$robot->cleanHouse(); // Works!
```

Also, the dependency level is not limited to 1. The IOC class resolves them recursively, so `VacuumCleaner` class could also have dependencies to another class.
