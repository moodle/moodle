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

namespace Google\Service\MigrationCenterAPI;

class HostsEntry extends \Google\Collection
{
  protected $collection_key = 'hostNames';
  /**
   * List of host names / aliases.
   *
   * @var string[]
   */
  public $hostNames;
  /**
   * IP (raw, IPv4/6 agnostic).
   *
   * @var string
   */
  public $ip;

  /**
   * List of host names / aliases.
   *
   * @param string[] $hostNames
   */
  public function setHostNames($hostNames)
  {
    $this->hostNames = $hostNames;
  }
  /**
   * @return string[]
   */
  public function getHostNames()
  {
    return $this->hostNames;
  }
  /**
   * IP (raw, IPv4/6 agnostic).
   *
   * @param string $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }
  /**
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HostsEntry::class, 'Google_Service_MigrationCenterAPI_HostsEntry');
