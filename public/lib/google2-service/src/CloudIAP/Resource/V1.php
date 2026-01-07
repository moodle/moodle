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

namespace Google\Service\CloudIAP\Resource;

use Google\Service\CloudIAP\GetIamPolicyRequest;
use Google\Service\CloudIAP\IapSettings;
use Google\Service\CloudIAP\Policy;
use Google\Service\CloudIAP\SetIamPolicyRequest;
use Google\Service\CloudIAP\TestIamPermissionsRequest;
use Google\Service\CloudIAP\TestIamPermissionsResponse;
use Google\Service\CloudIAP\ValidateIapAttributeExpressionResponse;

/**
 * The "v1" collection of methods.
 * Typical usage is:
 *  <code>
 *   $iapService = new Google\Service\CloudIAP(...);
 *   $v1 = $iapService->v1;
 *  </code>
 */
class V1 extends \Google\Service\Resource
{
  /**
   * Gets the access control policy for an Identity-Aware Proxy protected
   * resource. More information about managing access via IAP can be found at:
   * https://cloud.google.com/iap/docs/managing-access#managing_access_via_the_api
   * (v1.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, GetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Gets the IAP settings on a particular IAP protected resource.
   * (v1.getIapSettings)
   *
   * @param string $name Required. The resource name for which to retrieve the
   * settings. Authorization: Requires the `getSettings` permission for the
   * associated resource.
   * @param array $optParams Optional parameters.
   * @return IapSettings
   * @throws \Google\Service\Exception
   */
  public function getIapSettings($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getIapSettings', [$params], IapSettings::class);
  }
  /**
   * Sets the access control policy for an Identity-Aware Proxy protected
   * resource. Replaces any existing policy. More information about managing
   * access via IAP can be found at: https://cloud.google.com/iap/docs/managing-
   * access#managing_access_via_the_api (v1.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns permissions that a caller has on the Identity-Aware Proxy protected
   * resource. More information about managing access via IAP can be found at:
   * https://cloud.google.com/iap/docs/managing-access#managing_access_via_the_api
   * (v1.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
  /**
   * Updates the IAP settings on a particular IAP protected resource. It replaces
   * all fields unless the `update_mask` is set. (v1.updateIapSettings)
   *
   * @param string $name Required. The resource name of the IAP protected
   * resource.
   * @param IapSettings $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The field mask specifying which IAP settings
   * should be updated. If omitted, then all of the settings are updated. See
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask. Note: All IAP reauth
   * settings must always be set together, using the field mask:
   * `iapSettings.accessSettings.reauthSettings`.
   * @return IapSettings
   * @throws \Google\Service\Exception
   */
  public function updateIapSettings($name, IapSettings $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateIapSettings', [$params], IapSettings::class);
  }
  /**
   * Validates that a given CEL expression conforms to IAP restrictions.
   * (v1.validateAttributeExpression)
   *
   * @param string $name Required. The resource name of the IAP protected
   * resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string expression Required. User input string expression. Should
   * be of the form `attributes.saml_attributes.filter(attribute, attribute.name
   * in ['{attribute_name}', '{attribute_name}'])`
   * @return ValidateIapAttributeExpressionResponse
   * @throws \Google\Service\Exception
   */
  public function validateAttributeExpression($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('validateAttributeExpression', [$params], ValidateIapAttributeExpressionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1::class, 'Google_Service_CloudIAP_Resource_V1');
