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

namespace Google\Service\AIPlatformNotebooks\Resource;

use Google\Service\AIPlatformNotebooks\CheckAuthorizationRequest;
use Google\Service\AIPlatformNotebooks\CheckAuthorizationResponse;
use Google\Service\AIPlatformNotebooks\CheckInstanceUpgradabilityResponse;
use Google\Service\AIPlatformNotebooks\Config;
use Google\Service\AIPlatformNotebooks\DiagnoseInstanceRequest;
use Google\Service\AIPlatformNotebooks\GenerateAccessTokenRequest;
use Google\Service\AIPlatformNotebooks\GenerateAccessTokenResponse;
use Google\Service\AIPlatformNotebooks\Instance;
use Google\Service\AIPlatformNotebooks\ListInstancesResponse;
use Google\Service\AIPlatformNotebooks\Operation;
use Google\Service\AIPlatformNotebooks\Policy;
use Google\Service\AIPlatformNotebooks\ReportInstanceInfoSystemRequest;
use Google\Service\AIPlatformNotebooks\ResetInstanceRequest;
use Google\Service\AIPlatformNotebooks\ResizeDiskRequest;
use Google\Service\AIPlatformNotebooks\RestoreInstanceRequest;
use Google\Service\AIPlatformNotebooks\RollbackInstanceRequest;
use Google\Service\AIPlatformNotebooks\SetIamPolicyRequest;
use Google\Service\AIPlatformNotebooks\StartInstanceRequest;
use Google\Service\AIPlatformNotebooks\StopInstanceRequest;
use Google\Service\AIPlatformNotebooks\TestIamPermissionsRequest;
use Google\Service\AIPlatformNotebooks\TestIamPermissionsResponse;
use Google\Service\AIPlatformNotebooks\UpgradeInstanceRequest;
use Google\Service\AIPlatformNotebooks\UpgradeInstanceSystemRequest;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $notebooksService = new Google\Service\AIPlatformNotebooks(...);
 *   $instances = $notebooksService->projects_locations_instances;
 *  </code>
 */
class ProjectsLocationsInstances extends \Google\Service\Resource
{
  /**
   * Initiated by Cloud Console for Oauth consent flow for Workbench Instances. Do
   * not use this method directly. Design doc: go/wbi-euc:auth-dd
   * (instances.checkAuthorization)
   *
   * @param string $name Required. The name of the Notebook Instance resource.
   * Format: `projects/{project}/locations/{location}/instances/{instance}`
   * @param CheckAuthorizationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CheckAuthorizationResponse
   * @throws \Google\Service\Exception
   */
  public function checkAuthorization($name, CheckAuthorizationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('checkAuthorization', [$params], CheckAuthorizationResponse::class);
  }
  /**
   * Checks whether a notebook instance is upgradable.
   * (instances.checkUpgradability)
   *
   * @param string $notebookInstance Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param array $optParams Optional parameters.
   * @return CheckInstanceUpgradabilityResponse
   * @throws \Google\Service\Exception
   */
  public function checkUpgradability($notebookInstance, $optParams = [])
  {
    $params = ['notebookInstance' => $notebookInstance];
    $params = array_merge($params, $optParams);
    return $this->call('checkUpgradability', [$params], CheckInstanceUpgradabilityResponse::class);
  }
  /**
   * Creates a new Instance in a given project and location. (instances.create)
   *
   * @param string $parent Required. Format:
   * `parent=projects/{project_id}/locations/{location}`
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instanceId Required. User-defined unique ID of this
   * instance.
   * @opt_param string requestId Optional. Idempotent request UUID.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Instance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Instance. (instances.delete)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. Idempotent request UUID.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Creates a Diagnostic File and runs Diagnostic Tool given an Instance.
   * (instances.diagnose)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param DiagnoseInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function diagnose($name, DiagnoseInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('diagnose', [$params], Operation::class);
  }
  /**
   * Called by VM to return an EUC for the instance owner. Do not use this method
   * directly. Design doc: go/wbi-euc:dd (instances.generateAccessToken)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance_id}`
   * @param GenerateAccessTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GenerateAccessTokenResponse
   * @throws \Google\Service\Exception
   */
  public function generateAccessToken($name, GenerateAccessTokenRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateAccessToken', [$params], GenerateAccessTokenResponse::class);
  }
  /**
   * Gets details of a single Instance. (instances.get)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param array $optParams Optional parameters.
   * @return Instance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Instance::class);
  }
  /**
   * Returns various configuration parameters. (instances.getConfig)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}`
   * @param array $optParams Optional parameters.
   * @return Config
   * @throws \Google\Service\Exception
   */
  public function getConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getConfig', [$params], Config::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (instances.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists instances in a given project and location.
   * (instances.listProjectsLocationsInstances)
   *
   * @param string $parent Required. Format:
   * `parent=projects/{project_id}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. List filter.
   * @opt_param string orderBy Optional. Sort results. Supported values are
   * "name", "name desc" or "" (unsorted).
   * @opt_param int pageSize Optional. Maximum return size of the list call.
   * @opt_param string pageToken Optional. A previous returned page token that can
   * be used to continue listing from the last result.
   * @return ListInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInstances($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancesResponse::class);
  }
  /**
   * UpdateInstance updates an Instance. (instances.patch)
   *
   * @param string $name Output only. Identifier. The name of this notebook
   * instance. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. Idempotent request UUID.
   * @opt_param string updateMask Required. Mask used to update an instance.
   * Updatable fields: * `labels` * `gce_setup.min_cpu_platform` *
   * `gce_setup.metadata` * `gce_setup.machine_type` *
   * `gce_setup.accelerator_configs` * `gce_setup.accelerator_configs.type` *
   * `gce_setup.accelerator_configs.core_count` * `gce_setup.gpu_driver_config` *
   * `gce_setup.gpu_driver_config.enable_gpu_driver` *
   * `gce_setup.gpu_driver_config.custom_gpu_driver_path` *
   * `gce_setup.shielded_instance_config` *
   * `gce_setup.shielded_instance_config.enable_secure_boot` *
   * `gce_setup.shielded_instance_config.enable_vtpm` *
   * `gce_setup.shielded_instance_config.enable_integrity_monitoring` *
   * `gce_setup.reservation_affinity` *
   * `gce_setup.reservation_affinity.consume_reservation_type` *
   * `gce_setup.reservation_affinity.key` *
   * `gce_setup.reservation_affinity.values` * `gce_setup.tags` *
   * `gce_setup.container_image` * `gce_setup.container_image.repository` *
   * `gce_setup.container_image.tag` * `gce_setup.disable_public_ip` *
   * `disable_proxy_access`
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Instance $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Allows notebook instances to report their latest instance information to the
   * Notebooks API server. The server will merge the reported information to the
   * instance metadata store. Do not use this method directly.
   * (instances.reportInfoSystem)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param ReportInstanceInfoSystemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function reportInfoSystem($name, ReportInstanceInfoSystemRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reportInfoSystem', [$params], Operation::class);
  }
  /**
   * Resets a notebook instance. (instances.reset)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param ResetInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function reset($name, ResetInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reset', [$params], Operation::class);
  }
  /**
   * Resize a notebook instance disk to a higher capacity. (instances.resizeDisk)
   *
   * @param string $notebookInstance Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param ResizeDiskRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resizeDisk($notebookInstance, ResizeDiskRequest $postBody, $optParams = [])
  {
    $params = ['notebookInstance' => $notebookInstance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resizeDisk', [$params], Operation::class);
  }
  /**
   * RestoreInstance restores an Instance from a BackupSource. (instances.restore)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param RestoreInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restore($name, RestoreInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], Operation::class);
  }
  /**
   * Rollbacks a notebook instance to the previous version. (instances.rollback)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param RollbackInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function rollback($name, RollbackInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rollback', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (instances.setIamPolicy)
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
   * Starts a notebook instance. (instances.start)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param StartInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function start($name, StartInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('start', [$params], Operation::class);
  }
  /**
   * Stops a notebook instance. (instances.stop)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param StopInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function stop($name, StopInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stop', [$params], Operation::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (instances.testIamPermissions)
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
   * Upgrades a notebook instance to the latest version. (instances.upgrade)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param UpgradeInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function upgrade($name, UpgradeInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upgrade', [$params], Operation::class);
  }
  /**
   * Allows notebook instances to upgrade themselves. Do not use this method
   * directly. (instances.upgradeSystem)
   *
   * @param string $name Required. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param UpgradeInstanceSystemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function upgradeSystem($name, UpgradeInstanceSystemRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upgradeSystem', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInstances::class, 'Google_Service_AIPlatformNotebooks_Resource_ProjectsLocationsInstances');
