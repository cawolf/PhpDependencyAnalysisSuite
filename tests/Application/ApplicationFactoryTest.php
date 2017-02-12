<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Tests\Application;

use Cawolf\PhpDependencyAnalysisSuite\Application\ApplicationFactory;
use Symfony\Component\Console\Application;

class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $factory = new ApplicationFactory();
        $application = $factory->create();
        self::assertInstanceOf(Application::class, $application);
        self::assertNotNull($application->get('generate-config'));
        self::assertNotNull($application->get('process-result'));
    }
}
