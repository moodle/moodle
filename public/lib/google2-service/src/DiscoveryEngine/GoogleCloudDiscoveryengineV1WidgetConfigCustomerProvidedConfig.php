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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1WidgetConfigCustomerProvidedConfig extends \Google\Model
{
  /**
   * Default customer type.
   */
  public const CUSTOMER_TYPE_DEFAULT_CUSTOMER = 'DEFAULT_CUSTOMER';
  /**
   * Government customer type. Some features are disabled for government
   * customers due to legal requirements.
   */
  public const CUSTOMER_TYPE_GOVERNMENT_CUSTOMER = 'GOVERNMENT_CUSTOMER';
  /**
   * Customer type.
   *
   * @var string
   */
  public $customerType;

  /**
   * Customer type.
   *
   * Accepted values: DEFAULT_CUSTOMER, GOVERNMENT_CUSTOMER
   *
   * @param self::CUSTOMER_TYPE_* $customerType
   */
  public function setCustomerType($customerType)
  {
    $this->customerType = $customerType;
  }
  /**
   * @return self::CUSTOMER_TYPE_*
   */
  public function getCustomerType()
  {
    return $this->customerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigCustomerProvidedConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigCustomerProvidedConfig');
