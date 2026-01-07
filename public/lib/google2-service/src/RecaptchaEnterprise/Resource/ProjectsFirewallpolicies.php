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

namespace Google\Service\RecaptchaEnterprise\Resource;

use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1FirewallPolicy;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1ListFirewallPoliciesResponse;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1ReorderFirewallPoliciesRequest;
use Google\Service\RecaptchaEnterprise\GoogleCloudRecaptchaenterpriseV1ReorderFirewallPoliciesResponse;
use Google\Service\RecaptchaEnterprise\GoogleProtobufEmpty;

/**
 * The "firewallpolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $recaptchaenterpriseService = new Google\Service\RecaptchaEnterprise(...);
 *   $firewallpolicies = $recaptchaenterpriseService->projects_firewallpolicies;
 *  </code>
 */
class ProjectsFirewallpolicies extends \Google\Service\Resource
{
  /**
   * Creates a new FirewallPolicy, specifying conditions at which reCAPTCHA
   * Enterprise actions can be executed. A project may have a maximum of 1000
   * policies. (firewallpolicies.create)
   *
   * @param string $parent Required. The name of the project this policy applies
   * to, in the format `projects/{project}`.
   * @param GoogleCloudRecaptchaenterpriseV1FirewallPolicy $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1FirewallPolicy
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudRecaptchaenterpriseV1FirewallPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudRecaptchaenterpriseV1FirewallPolicy::class);
  }
  /**
   * Deletes the specified firewall policy. (firewallpolicies.delete)
   *
   * @param string $name Required. The name of the policy to be deleted, in the
   * format `projects/{project}/firewallpolicies/{firewallpolicy}`.
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Returns the specified firewall policy. (firewallpolicies.get)
   *
   * @param string $name Required. The name of the requested policy, in the format
   * `projects/{project}/firewallpolicies/{firewallpolicy}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1FirewallPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudRecaptchaenterpriseV1FirewallPolicy::class);
  }
  /**
   * Returns the list of all firewall policies that belong to a project.
   * (firewallpolicies.listProjectsFirewallpolicies)
   *
   * @param string $parent Required. The name of the project to list the policies
   * for, in the format `projects/{project}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of policies to return.
   * Default is 10. Max limit is 1000.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous. ListFirewallPoliciesRequest, if any.
   * @return GoogleCloudRecaptchaenterpriseV1ListFirewallPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsFirewallpolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudRecaptchaenterpriseV1ListFirewallPoliciesResponse::class);
  }
  /**
   * Updates the specified firewall policy. (firewallpolicies.patch)
   *
   * @param string $name Identifier. The resource name for the FirewallPolicy in
   * the format `projects/{project}/firewallpolicies/{firewallpolicy}`.
   * @param GoogleCloudRecaptchaenterpriseV1FirewallPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The mask to control which fields of
   * the policy get updated. If the mask is not present, all fields are updated.
   * @return GoogleCloudRecaptchaenterpriseV1FirewallPolicy
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudRecaptchaenterpriseV1FirewallPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudRecaptchaenterpriseV1FirewallPolicy::class);
  }
  /**
   * Reorders all firewall policies. (firewallpolicies.reorder)
   *
   * @param string $parent Required. The name of the project to list the policies
   * for, in the format `projects/{project}`.
   * @param GoogleCloudRecaptchaenterpriseV1ReorderFirewallPoliciesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRecaptchaenterpriseV1ReorderFirewallPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function reorder($parent, GoogleCloudRecaptchaenterpriseV1ReorderFirewallPoliciesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reorder', [$params], GoogleCloudRecaptchaenterpriseV1ReorderFirewallPoliciesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsFirewallpolicies::class, 'Google_Service_RecaptchaEnterprise_Resource_ProjectsFirewallpolicies');
