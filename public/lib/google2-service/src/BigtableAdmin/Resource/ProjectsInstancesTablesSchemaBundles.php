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

namespace Google\Service\BigtableAdmin\Resource;

use Google\Service\BigtableAdmin\BigtableadminEmpty;
use Google\Service\BigtableAdmin\GetIamPolicyRequest;
use Google\Service\BigtableAdmin\ListSchemaBundlesResponse;
use Google\Service\BigtableAdmin\Operation;
use Google\Service\BigtableAdmin\Policy;
use Google\Service\BigtableAdmin\SchemaBundle;
use Google\Service\BigtableAdmin\SetIamPolicyRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsResponse;

/**
 * The "schemaBundles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigtableadminService = new Google\Service\BigtableAdmin(...);
 *   $schemaBundles = $bigtableadminService->projects_instances_tables_schemaBundles;
 *  </code>
 */
class ProjectsInstancesTablesSchemaBundles extends \Google\Service\Resource
{
  /**
   * Creates a new schema bundle in the specified table. (schemaBundles.create)
   *
   * @param string $parent Required. The parent resource where this schema bundle
   * will be created. Values are of the form
   * `projects/{project}/instances/{instance}/tables/{table}`.
   * @param SchemaBundle $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string schemaBundleId Required. The unique ID to use for the
   * schema bundle, which will become the final component of the schema bundle's
   * resource name.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, SchemaBundle $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a schema bundle in the specified table. (schemaBundles.delete)
   *
   * @param string $name Required. The unique name of the schema bundle to delete.
   * Values are of the form `projects/{project}/instances/{instance}/tables/{table
   * }/schemaBundles/{schema_bundle}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The etag of the schema bundle. If this is
   * provided, it must match the server's etag. The server returns an ABORTED
   * error on a mismatched etag.
   * @return BigtableadminEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], BigtableadminEmpty::class);
  }
  /**
   * Gets metadata information about the specified schema bundle.
   * (schemaBundles.get)
   *
   * @param string $name Required. The unique name of the schema bundle to
   * retrieve. Values are of the form `projects/{project}/instances/{instance}/tab
   * les/{table}/schemaBundles/{schema_bundle}`
   * @param array $optParams Optional parameters.
   * @return SchemaBundle
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SchemaBundle::class);
  }
  /**
   * Gets the access control policy for a Bigtable resource. Returns an empty
   * policy if the resource exists but does not have a policy set.
   * (schemaBundles.getIamPolicy)
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
   * Lists all schema bundles associated with the specified table.
   * (schemaBundles.listProjectsInstancesTablesSchemaBundles)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * schema bundles. Values are of the form
   * `projects/{project}/instances/{instance}/tables/{table}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of schema bundles to return. If
   * the value is positive, the server may return at most this value. If
   * unspecified, the server will return the maximum allowed page size.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSchemaBundles` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListSchemaBundles` must match
   * the call that provided the page token.
   * @opt_param string view Optional. The resource_view to be applied to the
   * returned SchemaBundles' fields. Defaults to NAME_ONLY.
   * @return ListSchemaBundlesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesTablesSchemaBundles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSchemaBundlesResponse::class);
  }
  /**
   * Updates a schema bundle in the specified table. (schemaBundles.patch)
   *
   * @param string $name Identifier. The unique name identifying this schema
   * bundle. Values are of the form `projects/{project}/instances/{instance}/table
   * s/{table}/schemaBundles/{schema_bundle}`
   * @param SchemaBundle $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool ignoreWarnings Optional. If set, ignore the safety checks
   * when updating the Schema Bundle. The safety checks are: - The new Schema
   * Bundle is backwards compatible with the existing Schema Bundle.
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, SchemaBundle $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on a Bigtable resource. Replaces any existing
   * policy. (schemaBundles.setIamPolicy)
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
   * Returns permissions that the caller has on the specified Bigtable resource.
   * (schemaBundles.testIamPermissions)
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsInstancesTablesSchemaBundles::class, 'Google_Service_BigtableAdmin_Resource_ProjectsInstancesTablesSchemaBundles');
