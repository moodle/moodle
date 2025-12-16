<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Storagetransfer;

class TransferCounters extends \Google\Model
{
  /**
   * Bytes that are copied to the data sink.
   *
   * @var string
   */
  public $bytesCopiedToSink;
  /**
   * Bytes that are deleted from the data sink.
   *
   * @var string
   */
  public $bytesDeletedFromSink;
  /**
   * Bytes that are deleted from the data source.
   *
   * @var string
   */
  public $bytesDeletedFromSource;
  /**
   * Bytes that failed to be deleted from the data sink.
   *
   * @var string
   */
  public $bytesFailedToDeleteFromSink;
  /**
   * Bytes found in the data source that are scheduled to be transferred,
   * excluding any that are filtered based on object conditions or skipped due
   * to sync.
   *
   * @var string
   */
  public $bytesFoundFromSource;
  /**
   * Bytes found only in the data sink that are scheduled to be deleted.
   *
   * @var string
   */
  public $bytesFoundOnlyFromSink;
  /**
   * Bytes in the data source that failed to be transferred or that failed to be
   * deleted after being transferred.
   *
   * @var string
   */
  public $bytesFromSourceFailed;
  /**
   * Bytes in the data source that are not transferred because they already
   * exist in the data sink.
   *
   * @var string
   */
  public $bytesFromSourceSkippedBySync;
  /**
   * For transfers involving PosixFilesystem only. Number of listing failures
   * for each directory found at the source. Potential failures when listing a
   * directory include permission failure or block failure. If listing a
   * directory fails, no files in the directory are transferred.
   *
   * @var string
   */
  public $directoriesFailedToListFromSource;
  /**
   * For transfers involving PosixFilesystem only. Number of directories found
   * while listing. For example, if the root directory of the transfer is
   * `base/` and there are two other directories, `a/` and `b/` under this
   * directory, the count after listing `base/`, `base/a/` and `base/b/` is 3.
   *
   * @var string
   */
  public $directoriesFoundFromSource;
  /**
   * For transfers involving PosixFilesystem only. Number of successful listings
   * for each directory found at the source.
   *
   * @var string
   */
  public $directoriesSuccessfullyListedFromSource;
  /**
   * Number of successfully cleaned up intermediate objects.
   *
   * @var string
   */
  public $intermediateObjectsCleanedUp;
  /**
   * Number of intermediate objects failed cleaned up.
   *
   * @var string
   */
  public $intermediateObjectsFailedCleanedUp;
  /**
   * Objects that are copied to the data sink.
   *
   * @var string
   */
  public $objectsCopiedToSink;
  /**
   * Objects that are deleted from the data sink.
   *
   * @var string
   */
  public $objectsDeletedFromSink;
  /**
   * Objects that are deleted from the data source.
   *
   * @var string
   */
  public $objectsDeletedFromSource;
  /**
   * Objects that failed to be deleted from the data sink.
   *
   * @var string
   */
  public $objectsFailedToDeleteFromSink;
  /**
   * Objects found in the data source that are scheduled to be transferred,
   * excluding any that are filtered based on object conditions or skipped due
   * to sync.
   *
   * @var string
   */
  public $objectsFoundFromSource;
  /**
   * Objects found only in the data sink that are scheduled to be deleted.
   *
   * @var string
   */
  public $objectsFoundOnlyFromSink;
  /**
   * Objects in the data source that failed to be transferred or that failed to
   * be deleted after being transferred.
   *
   * @var string
   */
  public $objectsFromSourceFailed;
  /**
   * Objects in the data source that are not transferred because they already
   * exist in the data sink.
   *
   * @var string
   */
  public $objectsFromSourceSkippedBySync;

  /**
   * Bytes that are copied to the data sink.
   *
   * @param string $bytesCopiedToSink
   */
  public function setBytesCopiedToSink($bytesCopiedToSink)
  {
    $this->bytesCopiedToSink = $bytesCopiedToSink;
  }
  /**
   * @return string
   */
  public function getBytesCopiedToSink()
  {
    return $this->bytesCopiedToSink;
  }
  /**
   * Bytes that are deleted from the data sink.
   *
   * @param string $bytesDeletedFromSink
   */
  public function setBytesDeletedFromSink($bytesDeletedFromSink)
  {
    $this->bytesDeletedFromSink = $bytesDeletedFromSink;
  }
  /**
   * @return string
   */
  public function getBytesDeletedFromSink()
  {
    return $this->bytesDeletedFromSink;
  }
  /**
   * Bytes that are deleted from the data source.
   *
   * @param string $bytesDeletedFromSource
   */
  public function setBytesDeletedFromSource($bytesDeletedFromSource)
  {
    $this->bytesDeletedFromSource = $bytesDeletedFromSource;
  }
  /**
   * @return string
   */
  public function getBytesDeletedFromSource()
  {
    return $this->bytesDeletedFromSource;
  }
  /**
   * Bytes that failed to be deleted from the data sink.
   *
   * @param string $bytesFailedToDeleteFromSink
   */
  public function setBytesFailedToDeleteFromSink($bytesFailedToDeleteFromSink)
  {
    $this->bytesFailedToDeleteFromSink = $bytesFailedToDeleteFromSink;
  }
  /**
   * @return string
   */
  public function getBytesFailedToDeleteFromSink()
  {
    return $this->bytesFailedToDeleteFromSink;
  }
  /**
   * Bytes found in the data source that are scheduled to be transferred,
   * excluding any that are filtered based on object conditions or skipped due
   * to sync.
   *
   * @param string $bytesFoundFromSource
   */
  public function setBytesFoundFromSource($bytesFoundFromSource)
  {
    $this->bytesFoundFromSource = $bytesFoundFromSource;
  }
  /**
   * @return string
   */
  public function getBytesFoundFromSource()
  {
    return $this->bytesFoundFromSource;
  }
  /**
   * Bytes found only in the data sink that are scheduled to be deleted.
   *
   * @param string $bytesFoundOnlyFromSink
   */
  public function setBytesFoundOnlyFromSink($bytesFoundOnlyFromSink)
  {
    $this->bytesFoundOnlyFromSink = $bytesFoundOnlyFromSink;
  }
  /**
   * @return string
   */
  public function getBytesFoundOnlyFromSink()
  {
    return $this->bytesFoundOnlyFromSink;
  }
  /**
   * Bytes in the data source that failed to be transferred or that failed to be
   * deleted after being transferred.
   *
   * @param string $bytesFromSourceFailed
   */
  public function setBytesFromSourceFailed($bytesFromSourceFailed)
  {
    $this->bytesFromSourceFailed = $bytesFromSourceFailed;
  }
  /**
   * @return string
   */
  public function getBytesFromSourceFailed()
  {
    return $this->bytesFromSourceFailed;
  }
  /**
   * Bytes in the data source that are not transferred because they already
   * exist in the data sink.
   *
   * @param string $bytesFromSourceSkippedBySync
   */
  public function setBytesFromSourceSkippedBySync($bytesFromSourceSkippedBySync)
  {
    $this->bytesFromSourceSkippedBySync = $bytesFromSourceSkippedBySync;
  }
  /**
   * @return string
   */
  public function getBytesFromSourceSkippedBySync()
  {
    return $this->bytesFromSourceSkippedBySync;
  }
  /**
   * For transfers involving PosixFilesystem only. Number of listing failures
   * for each directory found at the source. Potential failures when listing a
   * directory include permission failure or block failure. If listing a
   * directory fails, no files in the directory are transferred.
   *
   * @param string $directoriesFailedToListFromSource
   */
  public function setDirectoriesFailedToListFromSource($directoriesFailedToListFromSource)
  {
    $this->directoriesFailedToListFromSource = $directoriesFailedToListFromSource;
  }
  /**
   * @return string
   */
  public function getDirectoriesFailedToListFromSource()
  {
    return $this->directoriesFailedToListFromSource;
  }
  /**
   * For transfers involving PosixFilesystem only. Number of directories found
   * while listing. For example, if the root directory of the transfer is
   * `base/` and there are two other directories, `a/` and `b/` under this
   * directory, the count after listing `base/`, `base/a/` and `base/b/` is 3.
   *
   * @param string $directoriesFoundFromSource
   */
  public function setDirectoriesFoundFromSource($directoriesFoundFromSource)
  {
    $this->directoriesFoundFromSource = $directoriesFoundFromSource;
  }
  /**
   * @return string
   */
  public function getDirectoriesFoundFromSource()
  {
    return $this->directoriesFoundFromSource;
  }
  /**
   * For transfers involving PosixFilesystem only. Number of successful listings
   * for each directory found at the source.
   *
   * @param string $directoriesSuccessfullyListedFromSource
   */
  public function setDirectoriesSuccessfullyListedFromSource($directoriesSuccessfullyListedFromSource)
  {
    $this->directoriesSuccessfullyListedFromSource = $directoriesSuccessfullyListedFromSource;
  }
  /**
   * @return string
   */
  public function getDirectoriesSuccessfullyListedFromSource()
  {
    return $this->directoriesSuccessfullyListedFromSource;
  }
  /**
   * Number of successfully cleaned up intermediate objects.
   *
   * @param string $intermediateObjectsCleanedUp
   */
  public function setIntermediateObjectsCleanedUp($intermediateObjectsCleanedUp)
  {
    $this->intermediateObjectsCleanedUp = $intermediateObjectsCleanedUp;
  }
  /**
   * @return string
   */
  public function getIntermediateObjectsCleanedUp()
  {
    return $this->intermediateObjectsCleanedUp;
  }
  /**
   * Number of intermediate objects failed cleaned up.
   *
   * @param string $intermediateObjectsFailedCleanedUp
   */
  public function setIntermediateObjectsFailedCleanedUp($intermediateObjectsFailedCleanedUp)
  {
    $this->intermediateObjectsFailedCleanedUp = $intermediateObjectsFailedCleanedUp;
  }
  /**
   * @return string
   */
  public function getIntermediateObjectsFailedCleanedUp()
  {
    return $this->intermediateObjectsFailedCleanedUp;
  }
  /**
   * Objects that are copied to the data sink.
   *
   * @param string $objectsCopiedToSink
   */
  public function setObjectsCopiedToSink($objectsCopiedToSink)
  {
    $this->objectsCopiedToSink = $objectsCopiedToSink;
  }
  /**
   * @return string
   */
  public function getObjectsCopiedToSink()
  {
    return $this->objectsCopiedToSink;
  }
  /**
   * Objects that are deleted from the data sink.
   *
   * @param string $objectsDeletedFromSink
   */
  public function setObjectsDeletedFromSink($objectsDeletedFromSink)
  {
    $this->objectsDeletedFromSink = $objectsDeletedFromSink;
  }
  /**
   * @return string
   */
  public function getObjectsDeletedFromSink()
  {
    return $this->objectsDeletedFromSink;
  }
  /**
   * Objects that are deleted from the data source.
   *
   * @param string $objectsDeletedFromSource
   */
  public function setObjectsDeletedFromSource($objectsDeletedFromSource)
  {
    $this->objectsDeletedFromSource = $objectsDeletedFromSource;
  }
  /**
   * @return string
   */
  public function getObjectsDeletedFromSource()
  {
    return $this->objectsDeletedFromSource;
  }
  /**
   * Objects that failed to be deleted from the data sink.
   *
   * @param string $objectsFailedToDeleteFromSink
   */
  public function setObjectsFailedToDeleteFromSink($objectsFailedToDeleteFromSink)
  {
    $this->objectsFailedToDeleteFromSink = $objectsFailedToDeleteFromSink;
  }
  /**
   * @return string
   */
  public function getObjectsFailedToDeleteFromSink()
  {
    return $this->objectsFailedToDeleteFromSink;
  }
  /**
   * Objects found in the data source that are scheduled to be transferred,
   * excluding any that are filtered based on object conditions or skipped due
   * to sync.
   *
   * @param string $objectsFoundFromSource
   */
  public function setObjectsFoundFromSource($objectsFoundFromSource)
  {
    $this->objectsFoundFromSource = $objectsFoundFromSource;
  }
  /**
   * @return string
   */
  public function getObjectsFoundFromSource()
  {
    return $this->objectsFoundFromSource;
  }
  /**
   * Objects found only in the data sink that are scheduled to be deleted.
   *
   * @param string $objectsFoundOnlyFromSink
   */
  public function setObjectsFoundOnlyFromSink($objectsFoundOnlyFromSink)
  {
    $this->objectsFoundOnlyFromSink = $objectsFoundOnlyFromSink;
  }
  /**
   * @return string
   */
  public function getObjectsFoundOnlyFromSink()
  {
    return $this->objectsFoundOnlyFromSink;
  }
  /**
   * Objects in the data source that failed to be transferred or that failed to
   * be deleted after being transferred.
   *
   * @param string $objectsFromSourceFailed
   */
  public function setObjectsFromSourceFailed($objectsFromSourceFailed)
  {
    $this->objectsFromSourceFailed = $objectsFromSourceFailed;
  }
  /**
   * @return string
   */
  public function getObjectsFromSourceFailed()
  {
    return $this->objectsFromSourceFailed;
  }
  /**
   * Objects in the data source that are not transferred because they already
   * exist in the data sink.
   *
   * @param string $objectsFromSourceSkippedBySync
   */
  public function setObjectsFromSourceSkippedBySync($objectsFromSourceSkippedBySync)
  {
    $this->objectsFromSourceSkippedBySync = $objectsFromSourceSkippedBySync;
  }
  /**
   * @return string
   */
  public function getObjectsFromSourceSkippedBySync()
  {
    return $this->objectsFromSourceSkippedBySync;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferCounters::class, 'Google_Service_Storagetransfer_TransferCounters');
