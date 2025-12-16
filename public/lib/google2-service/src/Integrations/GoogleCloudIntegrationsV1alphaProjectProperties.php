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

class GoogleCloudIntegrationsV1alphaProjectProperties extends \Google\Collection
{
  public const BILLING_TYPE_BILLING_TYPE_UNSPECIFIED = 'BILLING_TYPE_UNSPECIFIED';
  /**
   * A trial org provisioned through Apigee Provisioning Wizard
   */
  public const BILLING_TYPE_APIGEE_TRIALS = 'APIGEE_TRIALS';
  /**
   * Subscription based on concurrency model for Apigee and Application
   * Integration users.
   */
  public const BILLING_TYPE_APIGEE_SUBSCRIPTION = 'APIGEE_SUBSCRIPTION';
  /**
   * Consumption based users of IP
   */
  public const BILLING_TYPE_PAYG = 'PAYG';
  /**
   * Argentum subscription for Application Integration users. To be used in the
   * future.
   */
  public const BILLING_TYPE_SUBSCRIPTION = 'SUBSCRIPTION';
  /**
   * Special billing type to avoid any billing to clients (eg: trusted tester
   * program). This should not be used without consulting with the leads.
   */
  public const BILLING_TYPE_NO_BILLING = 'NO_BILLING';
  /**
   * The client enablement status is unspecified
   */
  public const IP_ENABLEMENT_STATE_IP_ENABLEMENT_STATE_UNSPECIFIED = 'IP_ENABLEMENT_STATE_UNSPECIFIED';
  /**
   * The client is enabled on Standalone IP
   */
  public const IP_ENABLEMENT_STATE_IP_ENABLEMENT_STATE_STANDALONE = 'IP_ENABLEMENT_STATE_STANDALONE';
  /**
   * The client is enabled on Apigee
   */
  public const IP_ENABLEMENT_STATE_IP_ENABLEMENT_STATE_APIGEE = 'IP_ENABLEMENT_STATE_APIGEE';
  /**
   * The client is entitled for Apigee but not enabled
   */
  public const IP_ENABLEMENT_STATE_IP_ENABLEMENT_STATE_APIGEE_ENTITLED = 'IP_ENABLEMENT_STATE_APIGEE_ENTITLED';
  protected $collection_key = 'provisionedRegions';
  /**
   * Required. Required: The client billing type that was requested
   *
   * @var string
   */
  public $billingType;
  /**
   * An enum value of what the enablement state is for the given project
   *
   * @var string
   */
  public $ipEnablementState;
  /**
   * A list of provisioned regions on the current project
   *
   * @var string[]
   */
  public $provisionedRegions;

  /**
   * Required. Required: The client billing type that was requested
   *
   * Accepted values: BILLING_TYPE_UNSPECIFIED, APIGEE_TRIALS,
   * APIGEE_SUBSCRIPTION, PAYG, SUBSCRIPTION, NO_BILLING
   *
   * @param self::BILLING_TYPE_* $billingType
   */
  public function setBillingType($billingType)
  {
    $this->billingType = $billingType;
  }
  /**
   * @return self::BILLING_TYPE_*
   */
  public function getBillingType()
  {
    return $this->billingType;
  }
  /**
   * An enum value of what the enablement state is for the given project
   *
   * Accepted values: IP_ENABLEMENT_STATE_UNSPECIFIED,
   * IP_ENABLEMENT_STATE_STANDALONE, IP_ENABLEMENT_STATE_APIGEE,
   * IP_ENABLEMENT_STATE_APIGEE_ENTITLED
   *
   * @param self::IP_ENABLEMENT_STATE_* $ipEnablementState
   */
  public function setIpEnablementState($ipEnablementState)
  {
    $this->ipEnablementState = $ipEnablementState;
  }
  /**
   * @return self::IP_ENABLEMENT_STATE_*
   */
  public function getIpEnablementState()
  {
    return $this->ipEnablementState;
  }
  /**
   * A list of provisioned regions on the current project
   *
   * @param string[] $provisionedRegions
   */
  public function setProvisionedRegions($provisionedRegions)
  {
    $this->provisionedRegions = $provisionedRegions;
  }
  /**
   * @return string[]
   */
  public function getProvisionedRegions()
  {
    return $this->provisionedRegions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaProjectProperties::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaProjectProperties');
