<?php

declare(strict_types=1);

namespace SimpleSAML\Test;

use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimpleSAML\Configuration;
use SimpleSAML\Database;

/**
 * This test ensures that the \SimpleSAML\Database class can properly
 * query a database.
 *
 * It currently uses sqlite to test, but an alternate config.php file
 * should be created for test cases to ensure that it will work
 * in an environment.
 *
 * @author Tyler Antonio, University of Alberta. <tantonio@ualberta.ca>
 * @package SimpleSAMLphp
 */

class DatabaseTest extends TestCase
{
    /**
     * @var \SimpleSAML\Configuration
     */
    protected $config;

    /**
     * @var \SimpleSAML\Database
     */
    protected $db;


    /**
     * Make protected functions available for testing
     *
     * @param string $getMethod The method to get.
     * @return mixed The method itself.
     */
    protected static function getMethod($getMethod)
    {
        $class = new ReflectionClass(Database::class);
        $method = $class->getMethod($getMethod);
        $method->setAccessible(true);
        return $method;
    }


    /**
     * @covers SimpleSAML\Database::getInstance
     * @covers SimpleSAML\Database::generateInstanceId
     * @covers SimpleSAML\Database::__construct
     * @covers SimpleSAML\Database::connect
     * @return void
     */
    public function setUp()
    {
        $config = [
            'database.dsn'        => 'sqlite::memory:',
            'database.username'   => null,
            'database.password'   => null,
            'database.prefix'     => 'phpunit_',
            'database.persistent' => true,
            'database.slaves'     => [],
        ];

        $this->config = new Configuration($config, "test/SimpleSAML/DatabaseTest.php");

        // Ensure that we have a functional configuration class
        $this->assertInstanceOf(Configuration::class, $this->config);
        $this->assertEquals($config['database.dsn'], $this->config->getString('database.dsn'));

        $this->db = Database::getInstance($this->config);

        // Ensure that we have a functional database class.
        $this->assertInstanceOf(Database::class, $this->db);
    }


    /**
     * @covers SimpleSAML\Database::getInstance
     * @covers SimpleSAML\Database::generateInstanceId
     * @covers SimpleSAML\Database::__construct
     * @covers SimpleSAML\Database::connect
     * @test
     * @return void
     */
    public function connectionFailure(): void
    {
        $this->expectException(Exception::class);
        $config = [
            'database.dsn'        => 'mysql:host=localhost;dbname=saml',
            'database.username'   => 'notauser',
            'database.password'   => 'notausersinvalidpassword',
            'database.prefix'     => 'phpunit_',
            'database.persistent' => true,
            'database.slaves'     => [],
        ];

        $this->config = new Configuration($config, "test/SimpleSAML/DatabaseTest.php");
        Database::getInstance($this->config);
    }


    /**
     * @covers SimpleSAML\Database::getInstance
     * @covers SimpleSAML\Database::generateInstanceId
     * @covers SimpleSAML\Database::__construct
     * @covers SimpleSAML\Database::connect
     * @test
     * @return void
     */
    public function instances(): void
    {
        $config = [
            'database.dsn'        => 'sqlite::memory:',
            'database.username'   => null,
            'database.password'   => null,
            'database.prefix'     => 'phpunit_',
            'database.persistent' => true,
            'database.slaves'     => [],
        ];
        $config2 = [
            'database.dsn'        => 'sqlite::memory:',
            'database.username'   => null,
            'database.password'   => null,
            'database.prefix'     => 'phpunit2_',
            'database.persistent' => true,
            'database.slaves'     => [],
        ];

        $config1 = new Configuration($config, "test/SimpleSAML/DatabaseTest.php");
        $config2 = new Configuration($config2, "test/SimpleSAML/DatabaseTest.php");
        $config3 = new Configuration($config, "test/SimpleSAML/DatabaseTest.php");

        $db1 = Database::getInstance($config1);
        $db2 = Database::getInstance($config2);
        $db3 = Database::getInstance($config3);

        $generateInstanceId = self::getMethod('generateInstanceId');

        $instance1 = $generateInstanceId->invokeArgs($db1, [$config1]);
        $instance2 = $generateInstanceId->invokeArgs($db2, [$config2]);
        $instance3 = $generateInstanceId->invokeArgs($db3, [$config3]);

        // Assert that $instance1 and $instance2 have different instance ids
        $this->assertNotEquals(
            $instance1,
            $instance2,
            "Database instances should be different, but returned the same id"
        );
        // Assert that $instance1 and $instance3 have identical instance ids
        $this->assertEquals(
            $instance1,
            $instance3,
            "Database instances should have the same id, but returned different id"
        );

        // Assert that $db1 and $db2 are different instances
        $this->assertNotEquals(
            spl_object_hash($db1),
            spl_object_hash($db2),
            "Database instances should be different, but returned the same spl_object_hash"
        );
        // Assert that $db1 and $db3 are identical instances
        $this->assertEquals(
            spl_object_hash($db1),
            spl_object_hash($db3),
            "Database instances should be the same, but returned different spl_object_hash"
        );
    }


    /**
     * @covers SimpleSAML\Database::getInstance
     * @covers SimpleSAML\Database::generateInstanceId
     * @covers SimpleSAML\Database::__construct
     * @covers SimpleSAML\Database::connect
     * @covers SimpleSAML\Database::getSlave
     * @test
     * @return void
     */
    public function slaves(): void
    {
        $getSlave = self::getMethod('getSlave');

        $master = spl_object_hash(\PHPUnit\Framework\Assert::readAttribute($this->db, 'dbMaster'));
        $slave = spl_object_hash($getSlave->invokeArgs($this->db, []));

        $this->assertTrue(($master == $slave), "getSlave should have returned the master database object");

        $config = [
            'database.dsn'        => 'sqlite::memory:',
            'database.username'   => null,
            'database.password'   => null,
            'database.prefix'     => 'phpunit_',
            'database.persistent' => true,
            'database.slaves'     => [
                [
                    'dsn'      => 'sqlite::memory:',
                    'username' => null,
                    'password' => null,
                ],
            ],
        ];

        $sspConfiguration = new Configuration($config, "test/SimpleSAML/DatabaseTest.php");
        $msdb = Database::getInstance($sspConfiguration);

        $slaves = \PHPUnit\Framework\Assert::readAttribute($msdb, 'dbSlaves');
        $gotSlave = spl_object_hash($getSlave->invokeArgs($msdb, []));

        $this->assertEquals(
            spl_object_hash($slaves[0]),
            $gotSlave,
            "getSlave should have returned a slave database object"
        );
    }


    /**
     * @covers SimpleSAML\Database::applyPrefix
     * @test
     * @return void
     */
    public function prefix(): void
    {
        $prefix = $this->config->getString('database.prefix');
        $table = "saml20_idp_hosted";
        $pftable = $this->db->applyPrefix($table);

        $this->assertEquals($prefix . $table, $pftable, "Did not properly apply the table prefix");
    }

    /**
     * @test
     */
    public function testGetDriver(): void
    {
        $this->assertEquals('sqlite', $this->db->getDriver());
    }

    /**
     * @covers SimpleSAML\Database::write
     * @covers SimpleSAML\Database::read
     * @covers SimpleSAML\Database::exec
     * @covers SimpleSAML\Database::query
     * @test
     * @return void
     */
    public function querying(): void
    {
        $table = $this->db->applyPrefix("sspdbt");
        $this->assertEquals($this->config->getString('database.prefix') . "sspdbt", $table);

        $this->db->write(
            "CREATE TABLE IF NOT EXISTS $table (ssp_key INT(16) NOT NULL, ssp_value TEXT NOT NULL)"
        );

        /** @var \PDOStatement $query1 */
        $query1 = $this->db->read("SELECT * FROM $table");
        $this->assertEquals(0, $query1->fetch(), "Table $table is not empty when it should be.");

        $ssp_key = time();
        $ssp_value = md5(strval(rand(0, 10000)));
        $stmt = $this->db->write(
            "INSERT INTO $table (ssp_key, ssp_value) VALUES (:ssp_key, :ssp_value)",
            ['ssp_key' => [$ssp_key, PDO::PARAM_INT], 'ssp_value' => $ssp_value]
        );
        $this->assertEquals(1, $stmt, "Could not insert data into $table.");

        /** @var \PDOStatement $query2 */
        $query2 = $this->db->read("SELECT * FROM $table WHERE ssp_key = :ssp_key", ['ssp_key' => $ssp_key]);
        $data = $query2->fetch();
        $this->assertEquals($data['ssp_value'], $ssp_value, "Inserted data doesn't match what is in the database");
    }


    /**
     * @covers SimpleSAML\Database::read
     * @covers SimpleSAML\Database::query
     * @test
     * @return void
     */
    public function readFailure(): void
    {
        $this->expectException(Exception::class);
        $table = $this->db->applyPrefix("sspdbt");
        $this->assertEquals($this->config->getString('database.prefix') . "sspdbt", $table);

        $this->db->read("SELECT * FROM $table");
    }


    /**
     * @covers SimpleSAML\Database::write
     * @covers SimpleSAML\Database::exec
     * @test
     * @return void
     */
    public function noSuchTable(): void
    {
        $this->expectException(Exception::class);
        $this->db->write("DROP TABLE phpunit_nonexistent");
    }


    /**
     * @return void
     */
    public function tearDown()
    {
        $table = $this->db->applyPrefix("sspdbt");
        $this->db->write("DROP TABLE IF EXISTS $table");

        unset($this->config);
        unset($this->db);
    }
}
