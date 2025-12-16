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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListNotebookRuntimeTemplatesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1NotebookRuntimeTemplate;
use Google\Service\Aiplatform\GoogleIamV1Policy;
use Google\Service\Aiplatform\GoogleIamV1SetIamPolicyRequest;
use Google\Service\Aiplatform\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "notebookRuntimeTemplates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $notebookRuntimeTemplates = $aiplatformService->projects_locations_notebookRuntimeTemplates;
 *  </code>
 */
class ProjectsLocationsNotebookRuntimeTemplates extends \Google\Service\Resource
{
  /**
   * Creates a NotebookRuntimeTemplate. (notebookRuntimeTemplates.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the NotebookRuntimeTemplate. Format:
   * `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1NotebookRuntimeTemplate $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string notebookRuntimeTemplateId Optional. User specified ID for
   * the notebook runtime template.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1NotebookRuntimeTemplate $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a NotebookRuntimeTemplate. (notebookRuntimeTemplates.delete)
   *
   * @param string $name Required. The name of the NotebookRuntimeTemplate
   * resource to be deleted. Format: `projects/{project}/locations/{location}/note
   * bookRuntimeTemplates/{notebook_runtime_template}`
   * @param array $optParams Optional parameters.
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
   * Gets a NotebookRuntimeTemplate. (notebookRuntimeTemplates.get)
   *
   * @param string $name Required. The name of the NotebookRuntimeTemplate
   * resource. Format: `projects/{project}/locations/{location}/notebookRuntimeTem
   * plates/{notebook_runtime_template}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1NotebookRuntimeTemplate
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1NotebookRuntimeTemplate::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (notebookRuntimeTemplates.getIamPolicy)
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
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Lists NotebookRuntimeTemplates in a Location.
   * (notebookRuntimeTemplates.listProjectsLocationsNotebookRuntimeTemplates)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the NotebookRuntimeTemplates. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. For field names both snake_case and camelCase are supported. *
   * `notebookRuntimeTemplate` supports = and !=. `notebookRuntimeTemplate`
   * represents the NotebookRuntimeTemplate ID, i.e. the last segment of the
   * NotebookRuntimeTemplate's resource name. * `display_name` supports = and != *
   * `labels` supports general map functions that is: * `labels.key=value` -
   * key:value equality * `labels.key:* or labels:key - key existence * A key
   * including a space must be quoted. `labels."a key"`. * `notebookRuntimeType`
   * supports = and !=. notebookRuntimeType enum: [USER_DEFINED, ONE_CLICK]. *
   * `machineType` supports = and !=. * `acceleratorType` supports = and !=. Some
   * examples: * `notebookRuntimeTemplate=notebookRuntimeTemplate123` *
   * `displayName="myDisplayName"` * `labels.myKey="myValue"` *
   * `notebookRuntimeType=USER_DEFINED` * `machineType=e2-standard-4` *
   * `acceleratorType=NVIDIA_TESLA_T4`
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `display_name` * `create_time` * `update_time` Example:
   * `display_name, create_time desc`.
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token. Typically
   * obtained via ListNotebookRuntimeTemplatesResponse.next_page_token of the
   * previous NotebookService.ListNotebookRuntimeTemplates call.
   * @opt_param string readMask Optional. Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListNotebookRuntimeTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNotebookRuntimeTemplates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListNotebookRuntimeTemplatesResponse::class);
  }
  /**
   * Updates a NotebookRuntimeTemplate. (notebookRuntimeTemplates.patch)
   *
   * @param string $name The resource name of the NotebookRuntimeTemplate.
   * @param GoogleCloudAiplatformV1NotebookRuntimeTemplate $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. For the `FieldMask` definition, see google.protobuf.FieldMask.
   * Input format: `{paths: "${updated_field}"}` Updatable fields: *
   * `encryption_spec.kms_key_name` * `display_name` *
   * `software_config.post_startup_script_config.post_startup_script` *
   * `software_config.post_startup_script_config.post_startup_script_url` *
   * `software_config.post_startup_script_config.post_startup_script_behavior` *
   * `software_config.env` * `software_config.colab_image.release_name`
   * @return GoogleCloudAiplatformV1NotebookRuntimeTemplate
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1NotebookRuntimeTemplate $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1NotebookRuntimeTemplate::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (notebookRuntimeTemplates.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GoogleIamV1SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, GoogleIamV1SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (notebookRuntimeTemplates.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string permissions The set of permissions to check for the
   * `resource`. Permissions with wildcards (such as `*` or `storage.*`) are not
   * allowed. For more information see [IAM
   * Overview](https://cloud.google.com/iam/docs/overview#permissions).
   * @return GoogleIamV1TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], GoogleIamV1TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNotebookRuntimeTemplates::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsNotebookRuntimeTemplates');
