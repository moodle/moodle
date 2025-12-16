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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentOutputConfigGcsOutputConfigShardingConfig extends \Google\Model
{
  /**
   * The number of overlapping pages between consecutive shards.
   *
   * @var int
   */
  public $pagesOverlap;
  /**
   * The number of pages per shard.
   *
   * @var int
   */
  public $pagesPerShard;

  /**
   * The number of overlapping pages between consecutive shards.
   *
   * @param int $pagesOverlap
   */
  public function setPagesOverlap($pagesOverlap)
  {
    $this->pagesOverlap = $pagesOverlap;
  }
  /**
   * @return int
   */
  public function getPagesOverlap()
  {
    return $this->pagesOverlap;
  }
  /**
   * The number of pages per shard.
   *
   * @param int $pagesPerShard
   */
  public function setPagesPerShard($pagesPerShard)
  {
    $this->pagesPerShard = $pagesPerShard;
  }
  /**
   * @return int
   */
  public function getPagesPerShard()
  {
    return $this->pagesPerShard;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentOutputConfigGcsOutputConfigShardingConfig::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentOutputConfigGcsOutputConfigShardingConfig');
