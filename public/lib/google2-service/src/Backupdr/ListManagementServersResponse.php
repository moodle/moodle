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

namespace Google\Service\Backupdr;

class ListManagementServersResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $managementServersType = ManagementServer::class;
  protected $managementServersDataType = 'array';
  /**
   * A token identifying a page of results the server should return.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The list of ManagementServer instances in the project for the specified
   * location. If the '{location}' value in the request is "-", the response
   * contains a list of instances from all locations. In case any location is
   * unreachable, the response will only return management servers in reachable
   * locations and the 'unreachable' field will be populated with a list of
   * unreachable locations.
   *
   * @param ManagementServer[] $managementServers
   */
  public function setManagementServers($managementServers)
  {
    $this->managementServers = $managementServers;
  }
  /**
   * @return ManagementServer[]
   */
  public function getManagementServers()
  {
    return $this->managementServers;
  }
  /**
   * A token identifying a page of results the server should return.
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
   * Locations that could not be reached.
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
class_alias(ListManagementServersResponse::class, 'Google_Service_Backupdr_ListManagementServersResponse');
