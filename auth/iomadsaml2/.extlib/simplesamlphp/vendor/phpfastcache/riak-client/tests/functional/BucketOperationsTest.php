<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Class BucketOperationsTest
 *
 * Functional tests related to bucket operations
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 * @author Luke Bakken <lbakken@basho.com>
 */
class BucketOperationsTest extends TestCase
{
    private static $hll_present = false;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        try {
            $command = (new Command\Builder\FetchBucketProperties(static::$riak))
                ->buildBucket('test', static::HLL_BUCKET_TYPE)
                ->build();

            $response = $command->execute();

            if ($response->isSuccess() && $response->getCode() == 200) {
                static::$hll_present = true;
            }
        } catch (\Exception $ex) {
            static::$hll_present = false;
        }
    }

    public function testStore()
    {
        $command = (new Command\Builder\SetBucketProperties(static::$riak))
            ->buildBucket('test')
            ->set('allow_mult', false)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testFetch()
    {
        $command = (new Command\Builder\FetchBucketProperties(static::$riak))
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());

        $bucket = $response->getBucket();
        $this->assertNotEmpty($bucket->getProperties());
        $this->assertFalse($bucket->getProperty('allow_mult'));
    }

    public function testStore2()
    {
        $command = (new Command\Builder\SetBucketProperties(static::$riak))
            ->buildBucket('test')
            ->set('allow_mult', true)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testFetch2()
    {
        $command = (new Command\Builder\FetchBucketProperties(static::$riak))
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());

        $bucket = $response->getBucket();
        $this->assertNotEmpty($bucket->getProperties());
        $this->assertTrue($bucket->getProperty('allow_mult'));
    }

    public function testFetchAndStoreHllPrecision()
    {
        if (static::$hll_present) {
            $command = (new Command\Builder\FetchBucketProperties(static::$riak))
                ->buildBucket('test' . md5(rand(0, 99) . time()), static::HLL_BUCKET_TYPE)
                ->build();

            $bucket = $command->execute()->getBucket();
            $this->assertNotEmpty($bucket->getProperties());
            $this->assertEquals(14, $bucket->getProperty('hll_precision'));

            $command = (new Command\Builder\SetBucketProperties(static::$riak))
                ->buildBucket('test', static::HLL_BUCKET_TYPE)
                ->set('hll_precision', 12)
                ->build();

            $response = $command->execute();

            $this->assertEquals('204', $response->getCode(), $response->getMessage());
        } else {
            throw new \PHPUnit_Framework_SkippedTestError("hlls bucket type is not enabled and activated, skipping");
        }
    }
}
