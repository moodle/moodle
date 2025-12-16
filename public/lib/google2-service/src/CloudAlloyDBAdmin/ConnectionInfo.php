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

namespace Google\Service\CloudAlloyDBAdmin;

class ConnectionInfo extends \Google\Model
{
  /**
   * Output only. The unique ID of the Instance.
   *
   * @var string
   */
  public $instanceUid;
  /**
   * Output only. The private network IP address for the Instance. This is the
   * default IP for the instance and is always created (even if enable_public_ip
   * is set). This is the connection endpoint for an end-user application.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * The name of the ConnectionInfo singleton resource, e.g.:
   * projects/{project}/locations/{location}/clusters/instances/connectionInfo
   * This field currently has no semantic meaning.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The public IP addresses for the Instance. This is available
   * ONLY when enable_public_ip is set. This is the connection endpoint for an
   * end-user application.
   *
   * @var string
   */
  public $publicIpAddress;

  /**
   * Output only. The unique ID of the Instance.
   *
   * @param string $instanceUid
   */
  public function setInstanceUid($instanceUid)
  {
    $this->instanceUid = $instanceUid;
  }
  /**
   * @return string
   */
  public function getInstanceUid()
  {
    return $this->instanceUid;
  }
  /**
   * Output only. The private network IP address for the Instance. This is the
   * default IP for the instance and is always created (even if enable_public_ip
   * is set). This is the connection endpoint for an end-user application.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * The name of the ConnectionInfo singleton resource, e.g.:
   * projects/{project}/locations/{location}/clusters/instances/connectionInfo
   * This field currently has no semantic meaning.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The public IP addresses for the Instance. This is available
   * ONLY when enable_public_ip is set. This is the connection endpoint for an
   * end-user application.
   *
   * @param string $publicIpAddress
   */
  public function setPublicIpAddress($publicIpAddress)
  {
    $this->publicIpAddress = $publicIpAddress;
  }
  /**
   * @return string
   */
  public function getPublicIpAddress()
  {
    return $this->publicIpAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectionInfo::class, 'Google_Service_CloudAlloyDBAdmin_ConnectionInfo');
