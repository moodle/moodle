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

namespace Google\Service\NetworkSecurity;

class GatewaySecurityPolicy extends \Google\Model
{
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Name of the resource. Name is of the form projects/{project}/loca
   * tions/{location}/gatewaySecurityPolicies/{gateway_security_policy}
   * gateway_security_policy should match the
   * pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Name of a TLS Inspection Policy resource that defines how TLS
   * inspection will be performed for any rule(s) which enables it.
   *
   * @var string
   */
  public $tlsInspectionPolicy;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Free-text description of the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Name of the resource. Name is of the form projects/{project}/loca
   * tions/{location}/gatewaySecurityPolicies/{gateway_security_policy}
   * gateway_security_policy should match the
   * pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
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
   * Optional. Name of a TLS Inspection Policy resource that defines how TLS
   * inspection will be performed for any rule(s) which enables it.
   *
   * @param string $tlsInspectionPolicy
   */
  public function setTlsInspectionPolicy($tlsInspectionPolicy)
  {
    $this->tlsInspectionPolicy = $tlsInspectionPolicy;
  }
  /**
   * @return string
   */
  public function getTlsInspectionPolicy()
  {
    return $this->tlsInspectionPolicy;
  }
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GatewaySecurityPolicy::class, 'Google_Service_NetworkSecurity_GatewaySecurityPolicy');
