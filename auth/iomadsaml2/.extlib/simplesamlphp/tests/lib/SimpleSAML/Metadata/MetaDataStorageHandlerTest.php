<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Metadata;

use SimpleSAML\Configuration;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Test\Utils\ClearStateTestCase;

class MetaDataStorageHandlerTest extends ClearStateTestCase
{
    /**
     * Test that loading specific entities works, and that metadata source precedence is followed
     * @return void
     */
    public function testLoadEntities(): void
    {
        $c = [
            'metadata.sources' => [
                ['type' => 'flatfile', 'directory' => __DIR__ . '/test-metadata/source1'],
                ['type' => 'serialize', 'directory' => __DIR__ . '/test-metadata/source2'],
            ],
        ];
        Configuration::loadFromArray($c, '', 'simplesaml');
        $handler = MetaDataStorageHandler::getMetadataHandler();

        $entities = $handler->getMetaDataForEntities([
            'entityA',
            'entityB',
            'nosuchEntity',
            'entityInBoth',
            'expiredInSrc1InSrc2'
        ], 'saml20-sp-remote');
        $this->assertCount(4, $entities);
        $this->assertEquals('entityA SP from source1', $entities['entityA']['name']['en']);
        $this->assertEquals('entityB SP from source2', $entities['entityB']['name']['en']);
        $this->assertEquals(
            'entityInBoth SP from source1',
            $entities['entityInBoth']['name']['en'],
            "Entity is in both sources, but should get loaded from the first"
        );
        $this->assertEquals(
            'expiredInSrc1InSrc2 SP from source2',
            $entities['expiredInSrc1InSrc2']['name']['en'],
            "Entity is in both sources, expired in src1 and available from src2"
        );
    }
}
