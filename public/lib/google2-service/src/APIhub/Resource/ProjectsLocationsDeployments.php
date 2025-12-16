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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\ApihubEmpty;
use Google\Service\APIhub\GoogleCloudApihubV1Deployment;
use Google\Service\APIhub\GoogleCloudApihubV1ListDeploymentsResponse;

/**
 * The "deployments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $deployments = $apihubService->projects_locations_deployments;
 *  </code>
 */
class ProjectsLocationsDeployments extends \Google\Service\Resource
{
  /**
   * Create a deployment resource in the API hub. Once a deployment resource is
   * created, it can be associated with API versions. (deployments.create)
   *
   * @param string $parent Required. The parent resource for the deployment
   * resource. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1Deployment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string deploymentId Optional. The ID to use for the deployment
   * resource, which will become the final component of the deployment's resource
   * name. This field is optional. * If provided, the same will be used. The
   * service will throw an error if the specified id is already used by another
   * deployment resource in the API hub. * If not provided, a system generated id
   * will be used. This value should be 4-500 characters, and valid characters are
   * /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1Deployment
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Deployment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Deployment::class);
  }
  /**
   * Delete a deployment resource in the API hub. (deployments.delete)
   *
   * @param string $name Required. The name of the deployment resource to delete.
   * Format: `projects/{project}/locations/{location}/deployments/{deployment}`
   * @param array $optParams Optional parameters.
   * @return ApihubEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ApihubEmpty::class);
  }
  /**
   * Get details about a deployment and the API versions linked to it.
   * (deployments.get)
   *
   * @param string $name Required. The name of the deployment resource to
   * retrieve. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Deployment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Deployment::class);
  }
  /**
   * List deployment resources in the API hub.
   * (deployments.listProjectsLocationsDeployments)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * deployment resources. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * Deployments. A filter expression consists of a field name, a comparison
   * operator, and a value for filtering. The value must be a string. The
   * comparison operator must be one of: `<`, `>` or `=`. Filters are not case
   * sensitive. The following fields in the `Deployments` are eligible for
   * filtering: * `display_name` - The display name of the Deployment. Allowed
   * comparison operators: `=`. * `create_time` - The time at which the Deployment
   * was created. The value should be in the
   * (RFC3339)[https://tools.ietf.org/html/rfc3339] format. Allowed comparison
   * operators: `>` and `<`. * `resource_uri` - A URI to the deployment resource.
   * Allowed comparison operators: `=`. * `api_versions` - The API versions linked
   * to this deployment. Allowed comparison operators: `:`. * `source_project` -
   * The project/organization at source for the deployment. Allowed comparison
   * operators: `=`. * `source_environment` - The environment at source for the
   * deployment. Allowed comparison operators: `=`. *
   * `deployment_type.enum_values.values.id` - The allowed value id of the
   * deployment_type attribute associated with the Deployment. Allowed comparison
   * operators: `:`. * `deployment_type.enum_values.values.display_name` - The
   * allowed value display name of the deployment_type attribute associated with
   * the Deployment. Allowed comparison operators: `:`. *
   * `slo.string_values.values` -The allowed string value of the slo attribute
   * associated with the deployment. Allowed comparison operators: `:`. *
   * `environment.enum_values.values.id` - The allowed value id of the environment
   * attribute associated with the deployment. Allowed comparison operators: `:`.
   * * `environment.enum_values.values.display_name` - The allowed value display
   * name of the environment attribute associated with the deployment. Allowed
   * comparison operators: `:`. * `attributes.projects/test-project-
   * id/locations/test-location-id/ attributes/user-defined-attribute-
   * id.enum_values.values.id` - The allowed value id of the user defined enum
   * attribute associated with the Resource. Allowed comparison operator is `:`.
   * Here user-defined-attribute-enum-id is a placeholder that can be replaced
   * with any user defined enum attribute name. * `attributes.projects/test-
   * project-id/locations/test-location-id/ attributes/user-defined-attribute-
   * id.enum_values.values.display_name` - The allowed value display name of the
   * user defined enum attribute associated with the Resource. Allowed comparison
   * operator is `:`. Here user-defined-attribute-enum-display-name is a
   * placeholder that can be replaced with any user defined enum attribute enum
   * name. * `attributes.projects/test-project-id/locations/test-location-id/
   * attributes/user-defined-attribute-id.string_values.values` - The allowed
   * value of the user defined string attribute associated with the Resource.
   * Allowed comparison operator is `:`. Here user-defined-attribute-string is a
   * placeholder that can be replaced with any user defined string attribute name.
   * * `attributes.projects/test-project-id/locations/test-location-id/
   * attributes/user-defined-attribute-id.json_values.values` - The allowed value
   * of the user defined JSON attribute associated with the Resource. Allowed
   * comparison operator is `:`. Here user-defined-attribute-json is a placeholder
   * that can be replaced with any user defined JSON attribute name. A filter
   * function is also supported in the filter string. The filter function is
   * `id(name)`. The `id(name)` function returns the id of the resource name. For
   * example, `id(name) = \"deployment-1\"` is equivalent to `name =
   * \"projects/test-project-id/locations/test-location-
   * id/deployments/deployment-1\"` provided the parent is `projects/test-project-
   * id/locations/test-location-id`. Expressions are combined with either `AND`
   * logic operator or `OR` logical operator but not both of them together i.e.
   * only one of the `AND` or `OR` operator can be used throughout the filter
   * string and both the operators cannot be used together. No other logical
   * operators are supported. At most three filter fields are allowed in the
   * filter string and if provided more than that then `INVALID_ARGUMENT` error is
   * returned by the API. Here are a few examples: *
   * `environment.enum_values.values.id: staging-id` - The allowed value id of the
   * environment attribute associated with the Deployment is _staging-id_. *
   * `environment.enum_values.values.display_name: \"Staging Deployment\"` - The
   * allowed value display name of the environment attribute associated with the
   * Deployment is `Staging Deployment`. * `environment.enum_values.values.id:
   * production-id AND create_time < \"2021-08-15T14:50:00Z\" AND create_time >
   * \"2021-08-10T12:00:00Z\"` - The allowed value id of the environment attribute
   * associated with the Deployment is _production-id_ and Deployment was created
   * before _2021-08-15 14:50:00 UTC_ and after _2021-08-10 12:00:00 UTC_. *
   * `environment.enum_values.values.id: production-id OR
   * slo.string_values.values: \"99.99%\"` - The allowed value id of the
   * environment attribute Deployment is _production-id_ or string value of the
   * slo attribute is _99.99%_. * `environment.enum_values.values.id: staging-id
   * AND attributes.projects/test-project-id/locations/test-location-id/
   * attributes/17650f90-4a29-4971-b3c0-d5532da3764b.string_values.values: test` -
   * The filter string specifies that the allowed value id of the environment
   * attribute associated with the Deployment is _staging-id_ and the value of the
   * user defined attribute of type string is _test_.
   * @opt_param int pageSize Optional. The maximum number of deployment resources
   * to return. The service may return fewer than this value. If unspecified, at
   * most 50 deployments will be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDeployments` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to
   * `ListDeployments` must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListDeploymentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeployments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListDeploymentsResponse::class);
  }
  /**
   * Update a deployment resource in the API hub. The following fields in the
   * deployment resource can be updated: * display_name * description *
   * documentation * deployment_type * resource_uri * endpoints * slo *
   * environment * attributes * source_project * source_environment *
   * management_url * source_uri The update_mask should be used to specify the
   * fields being updated. (deployments.patch)
   *
   * @param string $name Identifier. The name of the deployment. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   * @param GoogleCloudApihubV1Deployment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1Deployment
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1Deployment $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1Deployment::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeployments::class, 'Google_Service_APIhub_Resource_ProjectsLocationsDeployments');
