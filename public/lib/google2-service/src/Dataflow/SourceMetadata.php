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

namespace Google\Service\Dataflow;

class SourceMetadata extends \Google\Model
{
  /**
   * An estimate of the total size (in bytes) of the data that would be read
   * from this source. This estimate is in terms of external storage size,
   * before any decompression or other processing done by the reader.
   *
   * @var string
   */
  public $estimatedSizeBytes;
  /**
   * Specifies that the size of this source is known to be infinite (this is a
   * streaming source).
   *
   * @var bool
   */
  public $infinite;
  /**
   * Whether this source is known to produce key/value pairs with the (encoded)
   * keys in lexicographically sorted order.
   *
   * @var bool
   */
  public $producesSortedKeys;

  /**
   * An estimate of the total size (in bytes) of the data that would be read
   * from this source. This estimate is in terms of external storage size,
   * before any decompression or other processing done by the reader.
   *
   * @param string $estimatedSizeBytes
   */
  public function setEstimatedSizeBytes($estimatedSizeBytes)
  {
    $this->estimatedSizeBytes = $estimatedSizeBytes;
  }
  /**
   * @return string
   */
  public function getEstimatedSizeBytes()
  {
    return $this->estimatedSizeBytes;
  }
  /**
   * Specifies that the size of this source is known to be infinite (this is a
   * streaming source).
   *
   * @param bool $infinite
   */
  public function setInfinite($infinite)
  {
    $this->infinite = $infinite;
  }
  /**
   * @return bool
   */
  public function getInfinite()
  {
    return $this->infinite;
  }
  /**
   * Whether this source is known to produce key/value pairs with the (encoded)
   * keys in lexicographically sorted order.
   *
   * @param bool $producesSortedKeys
   */
  public function setProducesSortedKeys($producesSortedKeys)
  {
    $this->producesSortedKeys = $producesSortedKeys;
  }
  /**
   * @return bool
   */
  public function getProducesSortedKeys()
  {
    return $this->producesSortedKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceMetadata::class, 'Google_Service_Dataflow_SourceMetadata');
