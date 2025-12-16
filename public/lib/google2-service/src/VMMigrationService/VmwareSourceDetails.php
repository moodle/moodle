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

class VmwareSourceDetails extends \Google\Model
{
  /**
   * Input only. The credentials password. This is write only and can not be
   * read in a GET operation.
   *
   * @var string
   */
  public $password;
  /**
   * The hostname of the vcenter.
   *
   * @var string
   */
  public $resolvedVcenterHost;
  /**
   * The thumbprint representing the certificate for the vcenter.
   *
   * @var string
   */
  public $thumbprint;
  /**
   * The credentials username.
   *
   * @var string
   */
  public $username;
  /**
   * The ip address of the vcenter this Source represents.
   *
   * @var string
   */
  public $vcenterIp;

  /**
   * Input only. The credentials password. This is write only and can not be
   * read in a GET operation.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * The hostname of the vcenter.
   *
   * @param string $resolvedVcenterHost
   */
  public function setResolvedVcenterHost($resolvedVcenterHost)
  {
    $this->resolvedVcenterHost = $resolvedVcenterHost;
  }
  /**
   * @return string
   */
  public function getResolvedVcenterHost()
  {
    return $this->resolvedVcenterHost;
  }
  /**
   * The thumbprint representing the certificate for the vcenter.
   *
   * @param string $thumbprint
   */
  public function setThumbprint($thumbprint)
  {
    $this->thumbprint = $thumbprint;
  }
  /**
   * @return string
   */
  public function getThumbprint()
  {
    return $this->thumbprint;
  }
  /**
   * The credentials username.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * The ip address of the vcenter this Source represents.
   *
   * @param string $vcenterIp
   */
  public function setVcenterIp($vcenterIp)
  {
    $this->vcenterIp = $vcenterIp;
  }
  /**
   * @return string
   */
  public function getVcenterIp()
  {
    return $this->vcenterIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareSourceDetails::class, 'Google_Service_VMMigrationService_VmwareSourceDetails');
