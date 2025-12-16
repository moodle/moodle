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

namespace Google\Service\VMwareEngine;

class ThirdPartyFileService extends \Google\Collection
{
  protected $collection_key = 'servers';
  /**
   * Required. Required Mount Folder name
   *
   * @var string
   */
  public $fileShare;
  /**
   * Required. Required to identify vpc peering used for NFS access network name
   * of NFS's vpc e.g. projects/project-id/global/networks/my-network_id
   *
   * @var string
   */
  public $network;
  /**
   * Required. Server IP addresses of the NFS file service. NFS v3, provide a
   * single IP address or DNS name. Multiple servers can be supported in future
   * when NFS 4.1 protocol support is enabled.
   *
   * @var string[]
   */
  public $servers;

  /**
   * Required. Required Mount Folder name
   *
   * @param string $fileShare
   */
  public function setFileShare($fileShare)
  {
    $this->fileShare = $fileShare;
  }
  /**
   * @return string
   */
  public function getFileShare()
  {
    return $this->fileShare;
  }
  /**
   * Required. Required to identify vpc peering used for NFS access network name
   * of NFS's vpc e.g. projects/project-id/global/networks/my-network_id
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Required. Server IP addresses of the NFS file service. NFS v3, provide a
   * single IP address or DNS name. Multiple servers can be supported in future
   * when NFS 4.1 protocol support is enabled.
   *
   * @param string[] $servers
   */
  public function setServers($servers)
  {
    $this->servers = $servers;
  }
  /**
   * @return string[]
   */
  public function getServers()
  {
    return $this->servers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThirdPartyFileService::class, 'Google_Service_VMwareEngine_ThirdPartyFileService');
