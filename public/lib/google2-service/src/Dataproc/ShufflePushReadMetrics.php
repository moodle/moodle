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

namespace Google\Service\Dataproc;

class ShufflePushReadMetrics extends \Google\Model
{
  /**
   * @var string
   */
  public $corruptMergedBlockChunks;
  /**
   * @var string
   */
  public $localMergedBlocksFetched;
  /**
   * @var string
   */
  public $localMergedBytesRead;
  /**
   * @var string
   */
  public $localMergedChunksFetched;
  /**
   * @var string
   */
  public $mergedFetchFallbackCount;
  /**
   * @var string
   */
  public $remoteMergedBlocksFetched;
  /**
   * @var string
   */
  public $remoteMergedBytesRead;
  /**
   * @var string
   */
  public $remoteMergedChunksFetched;
  /**
   * @var string
   */
  public $remoteMergedReqsDuration;

  /**
   * @param string $corruptMergedBlockChunks
   */
  public function setCorruptMergedBlockChunks($corruptMergedBlockChunks)
  {
    $this->corruptMergedBlockChunks = $corruptMergedBlockChunks;
  }
  /**
   * @return string
   */
  public function getCorruptMergedBlockChunks()
  {
    return $this->corruptMergedBlockChunks;
  }
  /**
   * @param string $localMergedBlocksFetched
   */
  public function setLocalMergedBlocksFetched($localMergedBlocksFetched)
  {
    $this->localMergedBlocksFetched = $localMergedBlocksFetched;
  }
  /**
   * @return string
   */
  public function getLocalMergedBlocksFetched()
  {
    return $this->localMergedBlocksFetched;
  }
  /**
   * @param string $localMergedBytesRead
   */
  public function setLocalMergedBytesRead($localMergedBytesRead)
  {
    $this->localMergedBytesRead = $localMergedBytesRead;
  }
  /**
   * @return string
   */
  public function getLocalMergedBytesRead()
  {
    return $this->localMergedBytesRead;
  }
  /**
   * @param string $localMergedChunksFetched
   */
  public function setLocalMergedChunksFetched($localMergedChunksFetched)
  {
    $this->localMergedChunksFetched = $localMergedChunksFetched;
  }
  /**
   * @return string
   */
  public function getLocalMergedChunksFetched()
  {
    return $this->localMergedChunksFetched;
  }
  /**
   * @param string $mergedFetchFallbackCount
   */
  public function setMergedFetchFallbackCount($mergedFetchFallbackCount)
  {
    $this->mergedFetchFallbackCount = $mergedFetchFallbackCount;
  }
  /**
   * @return string
   */
  public function getMergedFetchFallbackCount()
  {
    return $this->mergedFetchFallbackCount;
  }
  /**
   * @param string $remoteMergedBlocksFetched
   */
  public function setRemoteMergedBlocksFetched($remoteMergedBlocksFetched)
  {
    $this->remoteMergedBlocksFetched = $remoteMergedBlocksFetched;
  }
  /**
   * @return string
   */
  public function getRemoteMergedBlocksFetched()
  {
    return $this->remoteMergedBlocksFetched;
  }
  /**
   * @param string $remoteMergedBytesRead
   */
  public function setRemoteMergedBytesRead($remoteMergedBytesRead)
  {
    $this->remoteMergedBytesRead = $remoteMergedBytesRead;
  }
  /**
   * @return string
   */
  public function getRemoteMergedBytesRead()
  {
    return $this->remoteMergedBytesRead;
  }
  /**
   * @param string $remoteMergedChunksFetched
   */
  public function setRemoteMergedChunksFetched($remoteMergedChunksFetched)
  {
    $this->remoteMergedChunksFetched = $remoteMergedChunksFetched;
  }
  /**
   * @return string
   */
  public function getRemoteMergedChunksFetched()
  {
    return $this->remoteMergedChunksFetched;
  }
  /**
   * @param string $remoteMergedReqsDuration
   */
  public function setRemoteMergedReqsDuration($remoteMergedReqsDuration)
  {
    $this->remoteMergedReqsDuration = $remoteMergedReqsDuration;
  }
  /**
   * @return string
   */
  public function getRemoteMergedReqsDuration()
  {
    return $this->remoteMergedReqsDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShufflePushReadMetrics::class, 'Google_Service_Dataproc_ShufflePushReadMetrics');
