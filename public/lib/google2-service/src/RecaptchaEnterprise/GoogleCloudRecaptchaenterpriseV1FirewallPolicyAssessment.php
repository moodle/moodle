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

class GoogleCloudRecaptchaenterpriseV1FirewallPolicyAssessment extends \Google\Model
{
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $firewallPolicyType = GoogleCloudRecaptchaenterpriseV1FirewallPolicy::class;
  protected $firewallPolicyDataType = '';

  /**
   * Output only. If the processing of a policy config fails, an error is
   * populated and the firewall_policy is left empty.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The policy that matched the request. If more than one policy
   * may match, this is the first match. If no policy matches the incoming
   * request, the policy field is left empty.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallPolicy $firewallPolicy
   */
  public function setFirewallPolicy(GoogleCloudRecaptchaenterpriseV1FirewallPolicy $firewallPolicy)
  {
    $this->firewallPolicy = $firewallPolicy;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallPolicy
   */
  public function getFirewallPolicy()
  {
    return $this->firewallPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1FirewallPolicyAssessment::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1FirewallPolicyAssessment');
