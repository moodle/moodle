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

namespace Google\Service\VMMigrationService;

class ListReplicationCyclesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * Output only. A token, which can be sent as `page_token` to retrieve the
   * next page. If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $replicationCyclesType = ReplicationCycle::class;
  protected $replicationCyclesDataType = 'array';
  /**
   * Output only. Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * Output only. A token, which can be sent as `page_token` to retrieve the
   * next page. If this field is omitted, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Output only. The list of replication cycles response.
   *
   * @param ReplicationCycle[] $replicationCycles
   */
  public function setReplicationCycles($replicationCycles)
  {
    $this->replicationCycles = $replicationCycles;
  }
  /**
   * @return ReplicationCycle[]
   */
  public function getReplicationCycles()
  {
    return $this->replicationCycles;
  }
  /**
   * Output only. Locations that could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListReplicationCyclesResponse::class, 'Google_Service_VMMigrationService_ListReplicationCyclesResponse');
