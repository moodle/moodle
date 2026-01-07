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

namespace Google\Service\CloudSearch;

class PollItemsRequest extends \Google\Collection
{
  protected $collection_key = 'statusCodes';
  /**
   * The name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   *
   * @var string
   */
  public $connectorName;
  protected $debugOptionsType = DebugOptions::class;
  protected $debugOptionsDataType = '';
  /**
   * Maximum number of items to return. The maximum value is 100 and the default
   * value is 20.
   *
   * @var int
   */
  public $limit;
  /**
   * Queue name to fetch items from. If unspecified, PollItems will fetch from
   * 'default' queue. The maximum length is 100 characters.
   *
   * @var string
   */
  public $queue;
  /**
   * Limit the items polled to the ones with these statuses.
   *
   * @var string[]
   */
  public $statusCodes;

  /**
   * The name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   *
   * @param string $connectorName
   */
  public function setConnectorName($connectorName)
  {
    $this->connectorName = $connectorName;
  }
  /**
   * @return string
   */
  public function getConnectorName()
  {
    return $this->connectorName;
  }
  /**
   * Common debug options.
   *
   * @param DebugOptions $debugOptions
   */
  public function setDebugOptions(DebugOptions $debugOptions)
  {
    $this->debugOptions = $debugOptions;
  }
  /**
   * @return DebugOptions
   */
  public function getDebugOptions()
  {
    return $this->debugOptions;
  }
  /**
   * Maximum number of items to return. The maximum value is 100 and the default
   * value is 20.
   *
   * @param int $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return int
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Queue name to fetch items from. If unspecified, PollItems will fetch from
   * 'default' queue. The maximum length is 100 characters.
   *
   * @param string $queue
   */
  public function setQueue($queue)
  {
    $this->queue = $queue;
  }
  /**
   * @return string
   */
  public function getQueue()
  {
    return $this->queue;
  }
  /**
   * Limit the items polled to the ones with these statuses.
   *
   * @param string[] $statusCodes
   */
  public function setStatusCodes($statusCodes)
  {
    $this->statusCodes = $statusCodes;
  }
  /**
   * @return string[]
   */
  public function getStatusCodes()
  {
    return $this->statusCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PollItemsRequest::class, 'Google_Service_CloudSearch_PollItemsRequest');
