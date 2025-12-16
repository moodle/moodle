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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1DisableSecurityActionRequest;
use Google\Service\Apigee\GoogleCloudApigeeV1EnableSecurityActionRequest;
use Google\Service\Apigee\GoogleCloudApigeeV1ListSecurityActionsResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1SecurityAction;
use Google\Service\Apigee\GoogleProtobufEmpty;

/**
 * The "securityActions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $securityActions = $apigeeService->organizations_environments_securityActions;
 *  </code>
 */
class OrganizationsEnvironmentsSecurityActions extends \Google\Service\Resource
{
  /**
   * CreateSecurityAction creates a SecurityAction. (securityActions.create)
   *
   * @param string $parent Required. The organization and environment that this
   * SecurityAction applies to. Format: organizations/{org}/environments/{env}
   * @param GoogleCloudApigeeV1SecurityAction $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string securityActionId Required. The ID to use for the
   * SecurityAction, which will become the final component of the action's
   * resource name. This value should be 0-61 characters, and valid format is
   * (^[a-z]([a-z0-9-]{​0,61}[a-z0-9])?$).
   * @return GoogleCloudApigeeV1SecurityAction
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1SecurityAction $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1SecurityAction::class);
  }
  /**
   * Delete a SecurityAction. (securityActions.delete)
   *
   * @param string $name Required. The name of the security action to delete.
   * Format:
   * `organizations/{org}/environment/{env}/securityActions/{security_action}`
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
   * Disable a SecurityAction. The `state` of the SecurityAction after disabling
   * is `DISABLED`. `DisableSecurityAction` can be called on SecurityActions in
   * the state `ENABLED`; SecurityActions in a different state (including
   * `DISABLED`) return an error. (securityActions.disable)
   *
   * @param string $name Required. The name of the SecurityAction to disable.
   * Format:
   * organizations/{org}/environments/{env}/securityActions/{security_action}
   * @param GoogleCloudApigeeV1DisableSecurityActionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SecurityAction
   * @throws \Google\Service\Exception
   */
  public function disable($name, GoogleCloudApigeeV1DisableSecurityActionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], GoogleCloudApigeeV1SecurityAction::class);
  }
  /**
   * Enable a SecurityAction. The `state` of the SecurityAction after enabling is
   * `ENABLED`. `EnableSecurityAction` can be called on SecurityActions in the
   * state `DISABLED`; SecurityActions in a different state (including `ENABLED)
   * return an error. (securityActions.enable)
   *
   * @param string $name Required. The name of the SecurityAction to enable.
   * Format:
   * organizations/{org}/environments/{env}/securityActions/{security_action}
   * @param GoogleCloudApigeeV1EnableSecurityActionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SecurityAction
   * @throws \Google\Service\Exception
   */
  public function enable($name, GoogleCloudApigeeV1EnableSecurityActionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enable', [$params], GoogleCloudApigeeV1SecurityAction::class);
  }
  /**
   * Get a SecurityAction by name. (securityActions.get)
   *
   * @param string $name Required. The fully qualified name of the SecurityAction
   * to retrieve. Format:
   * organizations/{org}/environments/{env}/securityActions/{security_action}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SecurityAction
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1SecurityAction::class);
  }
  /**
   * Returns a list of SecurityActions. This returns both enabled and disabled
   * actions. (securityActions.listOrganizationsEnvironmentsSecurityActions)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * SecurityActions. Format: organizations/{org}/environments/{env}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The filter expression to filter List results.
   * https://google.aip.dev/160. Allows for filtering over: state and api_proxies.
   * E.g.: state = ACTIVE AND apiProxies:foo. Filtering by action is not supported
   * https://github.com/aip-dev/google.aip.dev/issues/624
   * @opt_param int pageSize The maximum number of SecurityActions to return. If
   * unspecified, at most 50 SecurityActions will be returned. The maximum value
   * is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSecurityActions` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListSecurityActions` must
   * match the call that provided the page token.
   * @return GoogleCloudApigeeV1ListSecurityActionsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsEnvironmentsSecurityActions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListSecurityActionsResponse::class);
  }
  /**
   * Update a SecurityAction. (securityActions.patch)
   *
   * @param string $name Immutable. This field is ignored during creation as per
   * AIP-133. Please set the `security_action_id` field in the
   * CreateSecurityActionRequest when creating a new SecurityAction. Format:
   * organizations/{org}/environments/{env}/securityActions/{security_action}
   * @param GoogleCloudApigeeV1SecurityAction $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update. Valid
   * fields to update are `description`, `state`, `allow`, `deny`, and `flag`,
   * `expire_time`, and `ttl`, `api_proxies`, and `condition_config`.
   * @return GoogleCloudApigeeV1SecurityAction
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApigeeV1SecurityAction $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApigeeV1SecurityAction::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsEnvironmentsSecurityActions::class, 'Google_Service_Apigee_Resource_OrganizationsEnvironmentsSecurityActions');
