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

namespace Google\Service\DataprocMetastore;

class CdcConfig extends \Google\Model
{
  /**
   * @var string
   */
  public $bucket;
  /**
   * @var string
   */
  public $password;
  /**
   * @var string
   */
  public $reverseProxySubnet;
  /**
   * @var string
   */
  public $rootPath;
  /**
   * @var string
   */
  public $subnetIpRange;
  /**
   * @var string
   */
  public $username;
  /**
   * @var string
   */
  public $vpcNetwork;

  /**
   * @param string
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setReverseProxySubnet($reverseProxySubnet)
  {
    $this->reverseProxySubnet = $reverseProxySubnet;
  }
  /**
   * @return string
   */
  public function getReverseProxySubnet()
  {
    return $this->reverseProxySubnet;
  }
  /**
   * @param string
   */
  public function setRootPath($rootPath)
  {
    $this->rootPath = $rootPath;
  }
  /**
   * @return string
   */
  public function getRootPath()
  {
    return $this->rootPath;
  }
  /**
   * @param string
   */
  public function setSubnetIpRange($subnetIpRange)
  {
    $this->subnetIpRange = $subnetIpRange;
  }
  /**
   * @return string
   */
  public function getSubnetIpRange()
  {
    return $this->subnetIpRange;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setVpcNetwork($vpcNetwork)
  {
    $this->vpcNetwork = $vpcNetwork;
  }
  /**
   * @return string
   */
  public function getVpcNetwork()
  {
    return $this->vpcNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CdcConfig::class, 'Google_Service_DataprocMetastore_CdcConfig');
