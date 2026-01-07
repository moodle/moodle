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

namespace Google\Service\Contentwarehouse;

class GoogleCloudDocumentaiV1DocumentShardInfo extends \Google\Model
{
  /**
   * Total number of shards.
   *
   * @var string
   */
  public $shardCount;
  /**
   * The 0-based index of this shard.
   *
   * @var string
   */
  public $shardIndex;
  /**
   * The index of the first character in Document.text in the overall document
   * global text.
   *
   * @var string
   */
  public $textOffset;

  /**
   * Total number of shards.
   *
   * @param string $shardCount
   */
  public function setShardCount($shardCount)
  {
    $this->shardCount = $shardCount;
  }
  /**
   * @return string
   */
  public function getShardCount()
  {
    return $this->shardCount;
  }
  /**
   * The 0-based index of this shard.
   *
   * @param string $shardIndex
   */
  public function setShardIndex($shardIndex)
  {
    $this->shardIndex = $shardIndex;
  }
  /**
   * @return string
   */
  public function getShardIndex()
  {
    return $this->shardIndex;
  }
  /**
   * The index of the first character in Document.text in the overall document
   * global text.
   *
   * @param string $textOffset
   */
  public function setTextOffset($textOffset)
  {
    $this->textOffset = $textOffset;
  }
  /**
   * @return string
   */
  public function getTextOffset()
  {
    return $this->textOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentShardInfo::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentShardInfo');
