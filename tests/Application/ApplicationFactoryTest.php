<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Tests\Application;

use Cawolf\PhpDependencyAnalysisSuite\Application\ApplicationFactory;

class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $factory = new ApplicationFactory();
        $application = $factory->create();
        $this->assertInstanceOf('Symfony\Component\Console\Application', $application);
        $this->assertNotNull($application->get('cawolf:phpda:generate-config'));
    }
}
