<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Functional tests related to GSet CRDTs
 *
 * @author Luke Bakken <lbakken@basho.com>
 */
class GSetTest extends TestCase
{
    /**
     * Key to be used for tests
     *
     * @var string
     */
    private static $key = '';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // make completely random key based on time
        static::$key = md5(rand(0, 99) . time());

        try
        {
            // Skip this suite if the "gsets" bucket type is not present
            $command = (new Command\Builder\FetchBucketProperties(static::$riak))
                ->buildBucket('test', static::GSET_BUCKET_TYPE)
                ->build();

            $response = $command->execute();

            if (!$response->isSuccess() || $response->getCode() != 200) {
                throw new \PHPUnit_Framework_SkippedTestSuiteError("gsets bucket type is not enabled and activated, skipping");
            }
        }
        catch (\Exception $ex)
        {
            throw new \PHPUnit_Framework_SkippedTestSuiteError("gsets bucket type is not enabled and activated, skipping");
        }
    }

    public function testAddWithoutKey()
    {
        // build an object
        $command = (new Command\Builder\UpdateGSet(static::$riak))
            ->add('gosabres poked you.')
            ->add('phprocks viewed your profile.')
            ->add('phprocks started following you.')
            ->buildBucket('default', static::GSET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getCode());
        $this->assertNotEmpty($response->getLocation());
    }

    public function testFetchNotFound()
    {
        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'default', static::GSET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode());
    }

    /**
     * @depends      testFetchNotFound
     */
    public function testAddNewWithKey()
    {
        $command = (new Command\Builder\UpdateGSet(static::$riak))
            ->add('Sabres')
            ->add('Canadiens')
            ->add('Bruins')
            ->add('Maple Leafs')
            ->add('Senators')
            ->add('Red Wings')
            ->add('Thrashers')
            ->buildLocation(static::$key, 'Teams', static::GSET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getCode());
        $this->assertEmpty($response->getLocation());

        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::GSET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(7, count($response->getSet()->getData()));
        $this->assertEmpty($response->getSet()->getContext());
    }

    /**
     * @depends      testAddNewWithKey
     */
    public function testAddAnotherNew()
    {
        // add without context
        $command = (new Command\Builder\UpdateGSet(static::$riak))
            ->add('Lightning')
            ->buildLocation(static::$key, 'Teams', static::GSET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::GSET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(8, count($response->getSet()->getData()));
        $this->assertEmpty($response->getSet()->getContext());
    }
}
