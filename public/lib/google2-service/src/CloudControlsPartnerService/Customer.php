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

namespace Google\Service\CloudControlsPartnerService;

class Customer extends \Google\Model
{
  protected $customerOnboardingStateType = CustomerOnboardingState::class;
  protected $customerOnboardingStateDataType = '';
  /**
   * Required. Display name for the customer
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Indicates whether a customer is fully onboarded
   *
   * @var bool
   */
  public $isOnboarded;
  /**
   * Identifier. Format:
   * `organizations/{organization}/locations/{location}/customers/{customer}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The customer organization domain, extracted from CRM
   * Organization’s display_name field. e.g. "google.com"
   *
   * @var string
   */
  public $organizationDomain;

  /**
   * Output only. Container for customer onboarding steps
   *
   * @param CustomerOnboardingState $customerOnboardingState
   */
  public function setCustomerOnboardingState(CustomerOnboardingState $customerOnboardingState)
  {
    $this->customerOnboardingState = $customerOnboardingState;
  }
  /**
   * @return CustomerOnboardingState
   */
  public function getCustomerOnboardingState()
  {
    return $this->customerOnboardingState;
  }
  /**
   * Required. Display name for the customer
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Indicates whether a customer is fully onboarded
   *
   * @param bool $isOnboarded
   */
  public function setIsOnboarded($isOnboarded)
  {
    $this->isOnboarded = $isOnboarded;
  }
  /**
   * @return bool
   */
  public function getIsOnboarded()
  {
    return $this->isOnboarded;
  }
  /**
   * Identifier. Format:
   * `organizations/{organization}/locations/{location}/customers/{customer}`
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
   * Output only. The customer organization domain, extracted from CRM
   * Organization’s display_name field. e.g. "google.com"
   *
   * @param string $organizationDomain
   */
  public function setOrganizationDomain($organizationDomain)
  {
    $this->organizationDomain = $organizationDomain;
  }
  /**
   * @return string
   */
  public function getOrganizationDomain()
  {
    return $this->organizationDomain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Customer::class, 'Google_Service_CloudControlsPartnerService_Customer');
