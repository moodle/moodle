<?php
/*
 * Copyright 2016-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\GridFS;

use ArrayIterator;
use MongoDB\Collection;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\UpdateResult;
use MultipleIterator;

use function abs;
use function assert;
use function count;
use function is_numeric;
use function is_object;
use function sprintf;

/**
 * CollectionWrapper abstracts the GridFS files and chunks collections.
 *
 * @internal
 */
class CollectionWrapper
{
    /** @var string */
    private $bucketName;

    /** @var Collection */
    private $chunksCollection;

    /** @var string */
    private $databaseName;

    /** @var boolean */
    private $checkedIndexes = false;

    /** @var Collection */
    private $filesCollection;

    /**
     * Constructs a GridFS collection wrapper.
     *
     * @see Collection::__construct() for supported options
     * @param Manager $manager           Manager instance from the driver
     * @param string  $databaseName      Database name
     * @param string  $bucketName        Bucket name
     * @param array   $collectionOptions Collection options
     * @throws InvalidArgumentException
     */
    public function __construct(Manager $manager, string $databaseName, string $bucketName, array $collectionOptions = [])
    {
        $this->databaseName = $databaseName;
        $this->bucketName = $bucketName;

        $this->filesCollection = new Collection($manager, $databaseName, sprintf('%s.files', $bucketName), $collectionOptions);
        $this->chunksCollection = new Collection($manager, $databaseName, sprintf('%s.chunks', $bucketName), $collectionOptions);
    }

    /**
     * Deletes all GridFS chunks for a given file ID.
     *
     * @param mixed $id
     */
    public function deleteChunksByFilesId($id): void
    {
        $this->chunksCollection->deleteMany(['files_id' => $id]);
    }

    /**
     * Deletes a GridFS file and related chunks by ID.
     *
     * @param mixed $id
     */
    public function deleteFileAndChunksById($id): void
    {
        $this->filesCollection->deleteOne(['_id' => $id]);
        $this->chunksCollection->deleteMany(['files_id' => $id]);
    }

    /**
     * Drops the GridFS files and chunks collections.
     */
    public function dropCollections(): void
    {
        $this->filesCollection->drop(['typeMap' => []]);
        $this->chunksCollection->drop(['typeMap' => []]);
    }

    /**
     * Finds GridFS chunk documents for a given file ID and optional offset.
     *
     * @param mixed   $id        File ID
     * @param integer $fromChunk Starting chunk (inclusive)
     */
    public function findChunksByFileId($id, int $fromChunk = 0): Cursor
    {
        return $this->chunksCollection->find(
            [
                'files_id' => $id,
                'n' => ['$gte' => $fromChunk],
            ],
            [
                'sort' => ['n' => 1],
                'typeMap' => ['root' => 'stdClass'],
            ]
        );
    }

    /**
     * Finds a GridFS file document for a given filename and revision.
     *
     * Revision numbers are defined as follows:
     *
     *  * 0 = the original stored file
     *  * 1 = the first revision
     *  * 2 = the second revision
     *  * etc…
     *  * -2 = the second most recent revision
     *  * -1 = the most recent revision
     *
     * @see Bucket::downloadToStreamByName()
     * @see Bucket::openDownloadStreamByName()
     */
    public function findFileByFilenameAndRevision(string $filename, int $revision): ?object
    {
        $filename = $filename;
        $revision = $revision;

        if ($revision < 0) {
            $skip = abs($revision) - 1;
            $sortOrder = -1;
        } else {
            $skip = $revision;
            $sortOrder = 1;
        }

        $file = $this->filesCollection->findOne(
            ['filename' => $filename],
            [
                'skip' => $skip,
                'sort' => ['uploadDate' => $sortOrder],
                'typeMap' => ['root' => 'stdClass'],
            ]
        );
        assert(is_object($file) || $file === null);

        return $file;
    }

    /**
     * Finds a GridFS file document for a given ID.
     *
     * @param mixed $id
     */
    public function findFileById($id): ?object
    {
        $file = $this->filesCollection->findOne(
            ['_id' => $id],
            ['typeMap' => ['root' => 'stdClass']]
        );
        assert(is_object($file) || $file === null);

        return $file;
    }

    /**
     * Finds documents from the GridFS bucket's files collection.
     *
     * @see Find::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return Cursor
     */
    public function findFiles($filter, array $options = [])
    {
        return $this->filesCollection->find($filter, $options);
    }

    /**
     * Finds a single document from the GridFS bucket's files collection.
     *
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return array|object|null
     */
    public function findOneFile($filter, array $options = [])
    {
        return $this->filesCollection->findOne($filter, $options);
    }

    public function getBucketName(): string
    {
        return $this->bucketName;
    }

    public function getChunksCollection(): Collection
    {
        return $this->chunksCollection;
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function getFilesCollection(): Collection
    {
        return $this->filesCollection;
    }

    /**
     * Inserts a document into the chunks collection.
     *
     * @param array|object $chunk Chunk document
     */
    public function insertChunk($chunk): void
    {
        if (! $this->checkedIndexes) {
            $this->ensureIndexes();
        }

        $this->chunksCollection->insertOne($chunk);
    }

    /**
     * Inserts a document into the files collection.
     *
     * The file document should be inserted after all chunks have been inserted.
     *
     * @param array|object $file File document
     */
    public function insertFile($file): void
    {
        if (! $this->checkedIndexes) {
            $this->ensureIndexes();
        }

        $this->filesCollection->insertOne($file);
    }

    /**
     * Updates the filename field in the file document for a given ID.
     *
     * @param mixed $id
     */
    public function updateFilenameForId($id, string $filename): UpdateResult
    {
        return $this->filesCollection->updateOne(
            ['_id' => $id],
            ['$set' => ['filename' => $filename]]
        );
    }

    /**
     * Create an index on the chunks collection if it does not already exist.
     */
    private function ensureChunksIndex(): void
    {
        $expectedIndex = ['files_id' => 1, 'n' => 1];

        foreach ($this->chunksCollection->listIndexes() as $index) {
            if ($index->isUnique() && $this->indexKeysMatch($expectedIndex, $index->getKey())) {
                return;
            }
        }

        $this->chunksCollection->createIndex($expectedIndex, ['unique' => true]);
    }

    /**
     * Create an index on the files collection if it does not already exist.
     */
    private function ensureFilesIndex(): void
    {
        $expectedIndex = ['filename' => 1, 'uploadDate' => 1];

        foreach ($this->filesCollection->listIndexes() as $index) {
            if ($this->indexKeysMatch($expectedIndex, $index->getKey())) {
                return;
            }
        }

        $this->filesCollection->createIndex($expectedIndex);
    }

    /**
     * Ensure indexes on the files and chunks collections exist.
     *
     * This method is called once before the first write operation on a GridFS
     * bucket. Indexes are only be created if the files collection is empty.
     */
    private function ensureIndexes(): void
    {
        if ($this->checkedIndexes) {
            return;
        }

        $this->checkedIndexes = true;

        if (! $this->isFilesCollectionEmpty()) {
            return;
        }

        $this->ensureFilesIndex();
        $this->ensureChunksIndex();
    }

    private function indexKeysMatch(array $expectedKeys, array $actualKeys): bool
    {
        if (count($expectedKeys) !== count($actualKeys)) {
            return false;
        }

        $iterator = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $iterator->attachIterator(new ArrayIterator($expectedKeys));
        $iterator->attachIterator(new ArrayIterator($actualKeys));

        foreach ($iterator as $key => $value) {
            [$expectedKey, $actualKey]     = $key;
            [$expectedValue, $actualValue] = $value;

            if ($expectedKey !== $actualKey) {
                return false;
            }

            /* Since we don't expect special indexes (e.g. text), we mark any
             * index with a non-numeric definition as unequal. All others are
             * compared against their int value to avoid differences due to
             * some drivers using float values in the key specification. */
            if (! is_numeric($actualValue) || (int) $expectedValue !== (int) $actualValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether the files collection is empty.
     */
    private function isFilesCollectionEmpty(): bool
    {
        return null === $this->filesCollection->findOne([], [
            'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
            'projection' => ['_id' => 1],
            'typeMap' => [],
        ]);
    }
}
