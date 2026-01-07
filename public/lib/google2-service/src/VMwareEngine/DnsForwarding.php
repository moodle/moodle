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

class DnsForwarding extends \Google\Collection
{
  protected $collection_key = 'forwardingRules';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  protected $forwardingRulesType = ForwardingRule::class;
  protected $forwardingRulesDataType = 'array';
  /**
   * Output only. Identifier. The resource name of this DNS profile. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/dnsForwarding`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Creation time of this resource.
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
   * Required. List of domain mappings to configure
   *
   * @param ForwardingRule[] $forwardingRules
   */
  public function setForwardingRules($forwardingRules)
  {
    $this->forwardingRules = $forwardingRules;
  }
  /**
   * @return ForwardingRule[]
   */
  public function getForwardingRules()
  {
    return $this->forwardingRules;
  }
  /**
   * Output only. Identifier. The resource name of this DNS profile. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/dnsForwarding`
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
   * Output only. Last update time of this resource.
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
class_alias(DnsForwarding::class, 'Google_Service_VMwareEngine_DnsForwarding');
