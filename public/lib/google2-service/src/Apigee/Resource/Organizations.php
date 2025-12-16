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

use Google\Service\Apigee\GoogleCloudApigeeV1ControlPlaneAccess;
use Google\Service\Apigee\GoogleCloudApigeeV1GetSyncAuthorizationRequest;
use Google\Service\Apigee\GoogleCloudApigeeV1IngressConfig;
use Google\Service\Apigee\GoogleCloudApigeeV1ListOrganizationsResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1Organization;
use Google\Service\Apigee\GoogleCloudApigeeV1OrganizationProjectMapping;
use Google\Service\Apigee\GoogleCloudApigeeV1RuntimeConfig;
use Google\Service\Apigee\GoogleCloudApigeeV1SecuritySettings;
use Google\Service\Apigee\GoogleCloudApigeeV1SetAddonsRequest;
use Google\Service\Apigee\GoogleCloudApigeeV1SyncAuthorization;
use Google\Service\Apigee\GoogleLongrunningOperation;

/**
 * The "organizations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $organizations = $apigeeService->organizations;
 *  </code>
 */
class Organizations extends \Google\Service\Resource
{
  /**
   * Creates an Apigee organization. See [Create an Apigee
   * organization](https://cloud.google.com/apigee/docs/api-platform/get-
   * started/create-org). (organizations.create)
   *
   * @param GoogleCloudApigeeV1Organization $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Name of the Google Cloud project in which
   * to associate the Apigee organization. Pass the information as a query
   * parameter using the following structure in your request: `projects/`
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create(GoogleCloudApigeeV1Organization $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Delete an Apigee organization. For organizations with BillingType EVALUATION,
   * an immediate deletion is performed. For paid organizations (Subscription or
   * Pay-as-you-go), a soft-deletion is performed. The organization can be
   * restored within the soft-deletion period, which is specified using the
   * `retention` field in the request or by filing a support ticket with Apigee.
   * During the data retention period specified in the request, the Apigee
   * organization cannot be recreated in the same Google Cloud project.
   * **IMPORTANT: The default data retention setting for this operation is 7 days.
   * To permanently delete the organization in 24 hours, set the retention
   * parameter to `MINIMUM`.** (organizations.delete)
   *
   * @param string $name Required. Name of the organization. Use the following
   * structure in your request: `organizations/{org}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string retention Optional. This setting is applicable only for
   * organizations that are soft-deleted (i.e., BillingType is not EVALUATION). It
   * controls how long Organization data will be retained after the initial delete
   * operation completes. During this period, the Organization may be restored to
   * its last known state. After this period, the Organization will no longer be
   * able to be restored. **Note: During the data retention period specified using
   * this field, the Apigee organization cannot be recreated in the same Google
   * Cloud project.**
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets the profile for an Apigee organization. See [Understanding
   * organizations](https://cloud.google.com/apigee/docs/api-
   * platform/fundamentals/organization-structure). (organizations.get)
   *
   * @param string $name Required. Apigee organization name in the following
   * format: `organizations/{org}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1Organization
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1Organization::class);
  }
  /**
   * Lists the service accounts allowed to access Apigee control plane directly
   * for limited functionality. **Note**: Available to Apigee hybrid only.
   * (organizations.getControlPlaneAccess)
   *
   * @param string $name Required. Resource name of the Control Plane Access. Use
   * the following structure in your request:
   * `organizations/{org}/controlPlaneAccess`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ControlPlaneAccess
   * @throws \Google\Service\Exception
   */
  public function getControlPlaneAccess($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getControlPlaneAccess', [$params], GoogleCloudApigeeV1ControlPlaneAccess::class);
  }
  /**
   * Gets the deployed ingress configuration for an organization.
   * (organizations.getDeployedIngressConfig)
   *
   * @param string $name Required. Name of the deployed configuration for the
   * organization in the following format:
   * 'organizations/{org}/deployedIngressConfig'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view When set to FULL, additional details about the
   * specific deployments receiving traffic will be included in the IngressConfig
   * response's RoutingRules.
   * @return GoogleCloudApigeeV1IngressConfig
   * @throws \Google\Service\Exception
   */
  public function getDeployedIngressConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDeployedIngressConfig', [$params], GoogleCloudApigeeV1IngressConfig::class);
  }
  /**
   * Gets the project ID and region for an Apigee organization.
   * (organizations.getProjectMapping)
   *
   * @param string $name Required. Apigee organization name in the following
   * format: `organizations/{org}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1OrganizationProjectMapping
   * @throws \Google\Service\Exception
   */
  public function getProjectMapping($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getProjectMapping', [$params], GoogleCloudApigeeV1OrganizationProjectMapping::class);
  }
  /**
   * Get runtime config for an organization. (organizations.getRuntimeConfig)
   *
   * @param string $name Required. Name of the runtime config for the organization
   * in the following format: 'organizations/{org}/runtimeConfig'.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1RuntimeConfig
   * @throws \Google\Service\Exception
   */
  public function getRuntimeConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getRuntimeConfig', [$params], GoogleCloudApigeeV1RuntimeConfig::class);
  }
  /**
   * GetSecuritySettings gets the security settings for API Security.
   * (organizations.getSecuritySettings)
   *
   * @param string $name Required. The name of the SecuritySettings to retrieve.
   * This will always be: 'organizations/{org}/securitySettings'.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SecuritySettings
   * @throws \Google\Service\Exception
   */
  public function getSecuritySettings($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSecuritySettings', [$params], GoogleCloudApigeeV1SecuritySettings::class);
  }
  /**
   * Lists the service accounts with the permissions required to allow the
   * Synchronizer to download environment data from the control plane. An ETag is
   * returned in the response to `getSyncAuthorization`. Pass that ETag when
   * calling [setSyncAuthorization](setSyncAuthorization) to ensure that you are
   * updating the correct version. If you don't pass the ETag in the call to
   * `setSyncAuthorization`, then the existing authorization is overwritten
   * indiscriminately. For more information, see [Configure the Synchronizer](http
   * s://cloud.google.com/apigee/docs/hybrid/latest/synchronizer-access).
   * **Note**: Available to Apigee hybrid only.
   * (organizations.getSyncAuthorization)
   *
   * @param string $name Required. Name of the Apigee organization. Use the
   * following structure in your request: `organizations/{org}`
   * @param GoogleCloudApigeeV1GetSyncAuthorizationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SyncAuthorization
   * @throws \Google\Service\Exception
   */
  public function getSyncAuthorization($name, GoogleCloudApigeeV1GetSyncAuthorizationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getSyncAuthorization', [$params], GoogleCloudApigeeV1SyncAuthorization::class);
  }
  /**
   * Lists the Apigee organizations and associated Google Cloud projects that you
   * have permission to access. See [Understanding
   * organizations](https://cloud.google.com/apigee/docs/api-
   * platform/fundamentals/organization-structure).
   * (organizations.listOrganizations)
   *
   * @param string $parent Required. Use the following structure in your request:
   * `organizations`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ListOrganizationsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListOrganizationsResponse::class);
  }
  /**
   * Configures the add-ons for the Apigee organization. The existing add-on
   * configuration will be fully replaced. (organizations.setAddons)
   *
   * @param string $org Required. Name of the organization. Use the following
   * structure in your request: `organizations/{org}`
   * @param GoogleCloudApigeeV1SetAddonsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function setAddons($org, GoogleCloudApigeeV1SetAddonsRequest $postBody, $optParams = [])
  {
    $params = ['org' => $org, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setAddons', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the permissions required to allow the Synchronizer to download
   * environment data from the control plane. You must call this API to enable
   * proper functioning of hybrid. Pass the ETag when calling
   * `setSyncAuthorization` to ensure that you are updating the correct version.
   * To get an ETag, call [getSyncAuthorization](getSyncAuthorization). If you
   * don't pass the ETag in the call to `setSyncAuthorization`, then the existing
   * authorization is overwritten indiscriminately. For more information, see
   * [Configure the Synchronizer](https://cloud.google.com/apigee/docs/hybrid/late
   * st/synchronizer-access). **Note**: Available to Apigee hybrid only.
   * (organizations.setSyncAuthorization)
   *
   * @param string $name Required. Name of the Apigee organization. Use the
   * following structure in your request: `organizations/{org}`
   * @param GoogleCloudApigeeV1SyncAuthorization $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SyncAuthorization
   * @throws \Google\Service\Exception
   */
  public function setSyncAuthorization($name, GoogleCloudApigeeV1SyncAuthorization $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setSyncAuthorization', [$params], GoogleCloudApigeeV1SyncAuthorization::class);
  }
  /**
   * Updates the properties for an Apigee organization. No other fields in the
   * organization profile will be updated. (organizations.update)
   *
   * @param string $name Required. Apigee organization name in the following
   * format: `organizations/{org}`
   * @param GoogleCloudApigeeV1Organization $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1Organization
   * @throws \Google\Service\Exception
   */
  public function update($name, GoogleCloudApigeeV1Organization $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleCloudApigeeV1Organization::class);
  }
  /**
   * Updates the permissions required to allow Apigee runtime-plane components
   * access to the control plane. Currently, the permissions required are to: 1.
   * Allow runtime components to publish analytics data to the control plane.
   * **Note**: Available to Apigee hybrid only.
   * (organizations.updateControlPlaneAccess)
   *
   * @param string $name Identifier. The resource name of the ControlPlaneAccess.
   * Format: "organizations/{org}/controlPlaneAccess"
   * @param GoogleCloudApigeeV1ControlPlaneAccess $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask List of fields to be updated. Fields that can be
   * updated: synchronizer_identities, publisher_identities.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function updateControlPlaneAccess($name, GoogleCloudApigeeV1ControlPlaneAccess $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateControlPlaneAccess', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * UpdateSecuritySettings updates the current security settings for API
   * Security. (organizations.updateSecuritySettings)
   *
   * @param string $name Identifier. Full resource name is always
   * `organizations/{org}/securitySettings`.
   * @param GoogleCloudApigeeV1SecuritySettings $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update. Allowed
   * fields are: - ml_retraining_feedback_enabled
   * @return GoogleCloudApigeeV1SecuritySettings
   * @throws \Google\Service\Exception
   */
  public function updateSecuritySettings($name, GoogleCloudApigeeV1SecuritySettings $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateSecuritySettings', [$params], GoogleCloudApigeeV1SecuritySettings::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Organizations::class, 'Google_Service_Apigee_Resource_Organizations');
