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

namespace Google\Service\CloudIdentity;

class Policy extends \Google\Model
{
  /**
   * Unspecified policy type.
   */
  public const TYPE_POLICY_TYPE_UNSPECIFIED = 'POLICY_TYPE_UNSPECIFIED';
  /**
   * Policy type denoting the system-configured policies.
   */
  public const TYPE_SYSTEM = 'SYSTEM';
  /**
   * Policy type denoting the admin-configurable policies.
   */
  public const TYPE_ADMIN = 'ADMIN';
  /**
   * Immutable. Customer that the Policy belongs to. The value is in the format
   * 'customers/{customerId}'. The `customerId` must begin with "C" To find your
   * customer ID in Admin Console see
   * https://support.google.com/a/answer/10070793.
   *
   * @var string
   */
  public $customer;
  /**
   * Output only. Identifier. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the Policy.
   * Format: policies/{policy}.
   *
   * @var string
   */
  public $name;
  protected $policyQueryType = PolicyQuery::class;
  protected $policyQueryDataType = '';
  protected $settingType = Setting::class;
  protected $settingDataType = '';
  /**
   * Output only. The type of the policy.
   *
   * @var string
   */
  public $type;

  /**
   * Immutable. Customer that the Policy belongs to. The value is in the format
   * 'customers/{customerId}'. The `customerId` must begin with "C" To find your
   * customer ID in Admin Console see
   * https://support.google.com/a/answer/10070793.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Output only. Identifier. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the Policy.
   * Format: policies/{policy}.
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
   * Required. The PolicyQuery the Setting applies to.
   *
   * @param PolicyQuery $policyQuery
   */
  public function setPolicyQuery(PolicyQuery $policyQuery)
  {
    $this->policyQuery = $policyQuery;
  }
  /**
   * @return PolicyQuery
   */
  public function getPolicyQuery()
  {
    return $this->policyQuery;
  }
  /**
   * Required. The Setting configured by this Policy.
   *
   * @param Setting $setting
   */
  public function setSetting(Setting $setting)
  {
    $this->setting = $setting;
  }
  /**
   * @return Setting
   */
  public function getSetting()
  {
    return $this->setting;
  }
  /**
   * Output only. The type of the policy.
   *
   * Accepted values: POLICY_TYPE_UNSPECIFIED, SYSTEM, ADMIN
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policy::class, 'Google_Service_CloudIdentity_Policy');
