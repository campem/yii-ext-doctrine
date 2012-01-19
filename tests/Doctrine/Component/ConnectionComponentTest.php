<?php

namespace KodeFoundry\Test\YiiFramework\Extension\Doctrine\Component;

use KodeFoundry\Doctrine\Component\ConnectionComponent;

/**
 *
 * @author  Kevin Bradwick <kevin@kodefoundry.com>
 * @package KodeFoundry\YiiExt\Tests
 * @license
 */
class ConnectionComponentTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateConnection()
    {
        $component = new ConnectionComponent();
        $component->setConnections(array(
            'default' => array(
                'host' => 'localhost',
                'user' => 'admin',
                'password' => '',
            ),
        ));

        $conn = $component->getConnection('default');
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $conn);
        $this->assertSame($conn, $component->getConnection());
    }

    /**
     * @expectedException CException
     */
    public function testExceptionThrownOnInvalidConnectionConfig()
    {
        $component = new ConnectionComponent();
        $component->getConnection('foo');
    }
}
