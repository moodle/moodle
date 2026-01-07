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

namespace Google\Service\CloudRedis;

class RemoteCluster extends \Google\Model
{
  /**
   * Output only. The full resource path of the remote cluster in the format:
   * projects//locations//clusters/
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. The unique identifier of the remote cluster.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. The full resource path of the remote cluster in the format:
   * projects//locations//clusters/
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Output only. The unique identifier of the remote cluster.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoteCluster::class, 'Google_Service_CloudRedis_RemoteCluster');
