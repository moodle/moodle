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

namespace Google\Service\Spanner;

class PartitionOptions extends \Google\Model
{
  /**
   * **Note:** This hint is currently ignored by `PartitionQuery` and
   * `PartitionRead` requests. The desired maximum number of partitions to
   * return. For example, this might be set to the number of workers available.
   * The default for this option is currently 10,000. The maximum value is
   * currently 200,000. This is only a hint. The actual number of partitions
   * returned can be smaller or larger than this maximum count request.
   *
   * @var string
   */
  public $maxPartitions;
  /**
   * **Note:** This hint is currently ignored by `PartitionQuery` and
   * `PartitionRead` requests. The desired data size for each partition
   * generated. The default for this option is currently 1 GiB. This is only a
   * hint. The actual size of each partition can be smaller or larger than this
   * size request.
   *
   * @var string
   */
  public $partitionSizeBytes;

  /**
   * **Note:** This hint is currently ignored by `PartitionQuery` and
   * `PartitionRead` requests. The desired maximum number of partitions to
   * return. For example, this might be set to the number of workers available.
   * The default for this option is currently 10,000. The maximum value is
   * currently 200,000. This is only a hint. The actual number of partitions
   * returned can be smaller or larger than this maximum count request.
   *
   * @param string $maxPartitions
   */
  public function setMaxPartitions($maxPartitions)
  {
    $this->maxPartitions = $maxPartitions;
  }
  /**
   * @return string
   */
  public function getMaxPartitions()
  {
    return $this->maxPartitions;
  }
  /**
   * **Note:** This hint is currently ignored by `PartitionQuery` and
   * `PartitionRead` requests. The desired data size for each partition
   * generated. The default for this option is currently 1 GiB. This is only a
   * hint. The actual size of each partition can be smaller or larger than this
   * size request.
   *
   * @param string $partitionSizeBytes
   */
  public function setPartitionSizeBytes($partitionSizeBytes)
  {
    $this->partitionSizeBytes = $partitionSizeBytes;
  }
  /**
   * @return string
   */
  public function getPartitionSizeBytes()
  {
    return $this->partitionSizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionOptions::class, 'Google_Service_Spanner_PartitionOptions');
