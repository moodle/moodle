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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1BillingConfig extends \Google\Model
{
  /**
   * Billing category is not specified.
   */
  public const BILLING_CATEGORY_BILLING_CATEGORY_UNSPECIFIED = 'BILLING_CATEGORY_UNSPECIFIED';
  /**
   * GCP/Technical connector.
   */
  public const BILLING_CATEGORY_GCP_AND_TECHNICAL_CONNECTOR = 'GCP_AND_TECHNICAL_CONNECTOR';
  /**
   * Non-GCP connector.
   */
  public const BILLING_CATEGORY_NON_GCP_CONNECTOR = 'NON_GCP_CONNECTOR';
  /**
   * Output only. Billing category for the connector.
   *
   * @var string
   */
  public $billingCategory;

  /**
   * Output only. Billing category for the connector.
   *
   * Accepted values: BILLING_CATEGORY_UNSPECIFIED, GCP_AND_TECHNICAL_CONNECTOR,
   * NON_GCP_CONNECTOR
   *
   * @param self::BILLING_CATEGORY_* $billingCategory
   */
  public function setBillingCategory($billingCategory)
  {
    $this->billingCategory = $billingCategory;
  }
  /**
   * @return self::BILLING_CATEGORY_*
   */
  public function getBillingCategory()
  {
    return $this->billingCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1BillingConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1BillingConfig');
