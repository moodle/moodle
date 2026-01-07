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

class ShufflePushReadQuantileMetrics extends \Google\Model
{
  protected $corruptMergedBlockChunksType = Quantiles::class;
  protected $corruptMergedBlockChunksDataType = '';
  protected $localMergedBlocksFetchedType = Quantiles::class;
  protected $localMergedBlocksFetchedDataType = '';
  protected $localMergedBytesReadType = Quantiles::class;
  protected $localMergedBytesReadDataType = '';
  protected $localMergedChunksFetchedType = Quantiles::class;
  protected $localMergedChunksFetchedDataType = '';
  protected $mergedFetchFallbackCountType = Quantiles::class;
  protected $mergedFetchFallbackCountDataType = '';
  protected $remoteMergedBlocksFetchedType = Quantiles::class;
  protected $remoteMergedBlocksFetchedDataType = '';
  protected $remoteMergedBytesReadType = Quantiles::class;
  protected $remoteMergedBytesReadDataType = '';
  protected $remoteMergedChunksFetchedType = Quantiles::class;
  protected $remoteMergedChunksFetchedDataType = '';
  protected $remoteMergedReqsDurationType = Quantiles::class;
  protected $remoteMergedReqsDurationDataType = '';

  /**
   * @param Quantiles $corruptMergedBlockChunks
   */
  public function setCorruptMergedBlockChunks(Quantiles $corruptMergedBlockChunks)
  {
    $this->corruptMergedBlockChunks = $corruptMergedBlockChunks;
  }
  /**
   * @return Quantiles
   */
  public function getCorruptMergedBlockChunks()
  {
    return $this->corruptMergedBlockChunks;
  }
  /**
   * @param Quantiles $localMergedBlocksFetched
   */
  public function setLocalMergedBlocksFetched(Quantiles $localMergedBlocksFetched)
  {
    $this->localMergedBlocksFetched = $localMergedBlocksFetched;
  }
  /**
   * @return Quantiles
   */
  public function getLocalMergedBlocksFetched()
  {
    return $this->localMergedBlocksFetched;
  }
  /**
   * @param Quantiles $localMergedBytesRead
   */
  public function setLocalMergedBytesRead(Quantiles $localMergedBytesRead)
  {
    $this->localMergedBytesRead = $localMergedBytesRead;
  }
  /**
   * @return Quantiles
   */
  public function getLocalMergedBytesRead()
  {
    return $this->localMergedBytesRead;
  }
  /**
   * @param Quantiles $localMergedChunksFetched
   */
  public function setLocalMergedChunksFetched(Quantiles $localMergedChunksFetched)
  {
    $this->localMergedChunksFetched = $localMergedChunksFetched;
  }
  /**
   * @return Quantiles
   */
  public function getLocalMergedChunksFetched()
  {
    return $this->localMergedChunksFetched;
  }
  /**
   * @param Quantiles $mergedFetchFallbackCount
   */
  public function setMergedFetchFallbackCount(Quantiles $mergedFetchFallbackCount)
  {
    $this->mergedFetchFallbackCount = $mergedFetchFallbackCount;
  }
  /**
   * @return Quantiles
   */
  public function getMergedFetchFallbackCount()
  {
    return $this->mergedFetchFallbackCount;
  }
  /**
   * @param Quantiles $remoteMergedBlocksFetched
   */
  public function setRemoteMergedBlocksFetched(Quantiles $remoteMergedBlocksFetched)
  {
    $this->remoteMergedBlocksFetched = $remoteMergedBlocksFetched;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteMergedBlocksFetched()
  {
    return $this->remoteMergedBlocksFetched;
  }
  /**
   * @param Quantiles $remoteMergedBytesRead
   */
  public function setRemoteMergedBytesRead(Quantiles $remoteMergedBytesRead)
  {
    $this->remoteMergedBytesRead = $remoteMergedBytesRead;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteMergedBytesRead()
  {
    return $this->remoteMergedBytesRead;
  }
  /**
   * @param Quantiles $remoteMergedChunksFetched
   */
  public function setRemoteMergedChunksFetched(Quantiles $remoteMergedChunksFetched)
  {
    $this->remoteMergedChunksFetched = $remoteMergedChunksFetched;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteMergedChunksFetched()
  {
    return $this->remoteMergedChunksFetched;
  }
  /**
   * @param Quantiles $remoteMergedReqsDuration
   */
  public function setRemoteMergedReqsDuration(Quantiles $remoteMergedReqsDuration)
  {
    $this->remoteMergedReqsDuration = $remoteMergedReqsDuration;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteMergedReqsDuration()
  {
    return $this->remoteMergedReqsDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShufflePushReadQuantileMetrics::class, 'Google_Service_Dataproc_ShufflePushReadQuantileMetrics');
