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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1ListFirewallPoliciesResponse extends \Google\Collection
{
  protected $collection_key = 'firewallPolicies';
  protected $firewallPoliciesType = GoogleCloudRecaptchaenterpriseV1FirewallPolicy::class;
  protected $firewallPoliciesDataType = 'array';
  /**
   * Token to retrieve the next page of results. It is set to empty if no
   * policies remain in results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Policy details.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallPolicy[] $firewallPolicies
   */
  public function setFirewallPolicies($firewallPolicies)
  {
    $this->firewallPolicies = $firewallPolicies;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallPolicy[]
   */
  public function getFirewallPolicies()
  {
    return $this->firewallPolicies;
  }
  /**
   * Token to retrieve the next page of results. It is set to empty if no
   * policies remain in results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1ListFirewallPoliciesResponse::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1ListFirewallPoliciesResponse');
