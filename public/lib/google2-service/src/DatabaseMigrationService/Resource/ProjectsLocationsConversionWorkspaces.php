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

namespace Google\Service\DatabaseMigrationService\Resource;

use Google\Service\DatabaseMigrationService\ApplyConversionWorkspaceRequest;
use Google\Service\DatabaseMigrationService\CommitConversionWorkspaceRequest;
use Google\Service\DatabaseMigrationService\ConversionWorkspace;
use Google\Service\DatabaseMigrationService\ConvertConversionWorkspaceRequest;
use Google\Service\DatabaseMigrationService\DescribeConversionWorkspaceRevisionsResponse;
use Google\Service\DatabaseMigrationService\DescribeDatabaseEntitiesResponse;
use Google\Service\DatabaseMigrationService\ListConversionWorkspacesResponse;
use Google\Service\DatabaseMigrationService\Operation;
use Google\Service\DatabaseMigrationService\Policy;
use Google\Service\DatabaseMigrationService\RollbackConversionWorkspaceRequest;
use Google\Service\DatabaseMigrationService\SearchBackgroundJobsResponse;
use Google\Service\DatabaseMigrationService\SeedConversionWorkspaceRequest;
use Google\Service\DatabaseMigrationService\SetIamPolicyRequest;
use Google\Service\DatabaseMigrationService\TestIamPermissionsRequest;
use Google\Service\DatabaseMigrationService\TestIamPermissionsResponse;

/**
 * The "conversionWorkspaces" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datamigrationService = new Google\Service\DatabaseMigrationService(...);
 *   $conversionWorkspaces = $datamigrationService->projects_locations_conversionWorkspaces;
 *  </code>
 */
class ProjectsLocationsConversionWorkspaces extends \Google\Service\Resource
{
  /**
   * Applies draft tree onto a specific destination database.
   * (conversionWorkspaces.apply)
   *
   * @param string $name Required. The name of the conversion workspace resource
   * for which to apply the draft tree. Must be in the form of: projects/{project}
   * /locations/{location}/conversionWorkspaces/{conversion_workspace}.
   * @param ApplyConversionWorkspaceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function apply($name, ApplyConversionWorkspaceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('apply', [$params], Operation::class);
  }
  /**
   * Marks all the data in the conversion workspace as committed.
   * (conversionWorkspaces.commit)
   *
   * @param string $name Required. Name of the conversion workspace resource to
   * commit.
   * @param CommitConversionWorkspaceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function commit($name, CommitConversionWorkspaceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('commit', [$params], Operation::class);
  }
  /**
   * Creates a draft tree schema for the destination database.
   * (conversionWorkspaces.convert)
   *
   * @param string $name Name of the conversion workspace resource to convert in
   * the form of: projects/{project}/locations/{location}/conversionWorkspaces/{co
   * nversion_workspace}.
   * @param ConvertConversionWorkspaceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function convert($name, ConvertConversionWorkspaceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('convert', [$params], Operation::class);
  }
  /**
   * Creates a new conversion workspace in a given project and location.
   * (conversionWorkspaces.create)
   *
   * @param string $parent Required. The parent which owns this collection of
   * conversion workspaces.
   * @param ConversionWorkspace $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string conversionWorkspaceId Required. The ID of the conversion
   * workspace to create.
   * @opt_param string requestId A unique ID used to identify the request. If the
   * server receives two requests with the same ID, then the second request is
   * ignored. It is recommended to always set this value to a UUID. The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ConversionWorkspace $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single conversion workspace. (conversionWorkspaces.delete)
   *
   * @param string $name Required. Name of the conversion workspace resource to
   * delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Force delete the conversion workspace, even if there's
   * a running migration that is using the workspace.
   * @opt_param string requestId A unique ID used to identify the request. If the
   * server receives two requests with the same ID, then the second request is
   * ignored. It is recommended to always set this value to a UUID. The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
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
   * Retrieves a list of committed revisions of a specific conversion workspace.
   * (conversionWorkspaces.describeConversionWorkspaceRevisions)
   *
   * @param string $conversionWorkspace Required. Name of the conversion workspace
   * resource whose revisions are listed. Must be in the form of: projects/{projec
   * t}/locations/{location}/conversionWorkspaces/{conversion_workspace}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string commitId Optional. Optional filter to request a specific
   * commit ID.
   * @return DescribeConversionWorkspaceRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function describeConversionWorkspaceRevisions($conversionWorkspace, $optParams = [])
  {
    $params = ['conversionWorkspace' => $conversionWorkspace];
    $params = array_merge($params, $optParams);
    return $this->call('describeConversionWorkspaceRevisions', [$params], DescribeConversionWorkspaceRevisionsResponse::class);
  }
  /**
   * Describes the database entities tree for a specific conversion workspace and
   * a specific tree type. Database entities are not resources like conversion
   * workspaces or mapping rules, and they can't be created, updated or deleted.
   * Instead, they are simple data objects describing the structure of the client
   * database. (conversionWorkspaces.describeDatabaseEntities)
   *
   * @param string $conversionWorkspace Required. Name of the conversion workspace
   * resource whose database entities are described. Must be in the form of: proje
   * cts/{project}/locations/{location}/conversionWorkspaces/{conversion_workspace
   * }.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string commitId Optional. Request a specific commit ID. If not
   * specified, the entities from the latest commit are returned.
   * @opt_param string filter Optional. Filter the returned entities based on
   * AIP-160 standard.
   * @opt_param int pageSize Optional. The maximum number of entities to return.
   * The service may return fewer entities than the value specifies.
   * @opt_param string pageToken Optional. The nextPageToken value received in the
   * previous call to conversionWorkspace.describeDatabaseEntities, used in the
   * subsequent request to retrieve the next page of results. On first call this
   * should be left blank. When paginating, all other parameters provided to
   * conversionWorkspace.describeDatabaseEntities must match the call that
   * provided the page token.
   * @opt_param string tree Required. The tree to fetch.
   * @opt_param bool uncommitted Optional. Whether to retrieve the latest
   * committed version of the entities or the latest version. This field is
   * ignored if a specific commit_id is specified.
   * @opt_param string view Optional. Results view based on AIP-157
   * @return DescribeDatabaseEntitiesResponse
   * @throws \Google\Service\Exception
   */
  public function describeDatabaseEntities($conversionWorkspace, $optParams = [])
  {
    $params = ['conversionWorkspace' => $conversionWorkspace];
    $params = array_merge($params, $optParams);
    return $this->call('describeDatabaseEntities', [$params], DescribeDatabaseEntitiesResponse::class);
  }
  /**
   * Gets details of a single conversion workspace. (conversionWorkspaces.get)
   *
   * @param string $name Required. Name of the conversion workspace resource to
   * get.
   * @param array $optParams Optional parameters.
   * @return ConversionWorkspace
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ConversionWorkspace::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (conversionWorkspaces.getIamPolicy)
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
   * Lists conversion workspaces in a given project and location.
   * (conversionWorkspaces.listProjectsLocationsConversionWorkspaces)
   *
   * @param string $parent Required. The parent which owns this collection of
   * conversion workspaces.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters conversion
   * workspaces listed in the response. The expression must specify the field
   * name, a comparison operator, and the value that you want to use for
   * filtering. The value must be a string, a number, or a boolean. The comparison
   * operator must be either =, !=, >, or <. For example, list conversion
   * workspaces created this year by specifying **createTime %gt;
   * 2020-01-01T00:00:00.000000000Z.** You can also filter nested fields. For
   * example, you could specify **source.version = "12.c.1"** to select all
   * conversion workspaces with source database version equal to 12.c.1.
   * @opt_param int pageSize The maximum number of conversion workspaces to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 sets are returned.
   * @opt_param string pageToken The nextPageToken value received in the previous
   * call to conversionWorkspaces.list, used in the subsequent request to retrieve
   * the next page of results. On first call this should be left blank. When
   * paginating, all other parameters provided to conversionWorkspaces.list must
   * match the call that provided the page token.
   * @return ListConversionWorkspacesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConversionWorkspaces($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConversionWorkspacesResponse::class);
  }
  /**
   * Updates the parameters of a single conversion workspace.
   * (conversionWorkspaces.patch)
   *
   * @param string $name Full name of the workspace resource, in the form of: proj
   * ects/{project}/locations/{location}/conversionWorkspaces/{conversion_workspac
   * e}.
   * @param ConversionWorkspace $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId A unique ID used to identify the request. If the
   * server receives two requests with the same ID, then the second request is
   * ignored. It is recommended to always set this value to a UUID. The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten by the update in the conversion workspace resource.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, ConversionWorkspace $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Rolls back a conversion workspace to the last committed snapshot.
   * (conversionWorkspaces.rollback)
   *
   * @param string $name Required. Name of the conversion workspace resource to
   * roll back to.
   * @param RollbackConversionWorkspaceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function rollback($name, RollbackConversionWorkspaceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rollback', [$params], Operation::class);
  }
  /**
   * Searches/lists the background jobs for a specific conversion workspace. The
   * background jobs are not resources like conversion workspaces or mapping
   * rules, and they can't be created, updated or deleted. Instead, they are a way
   * to expose the data plane jobs log.
   * (conversionWorkspaces.searchBackgroundJobs)
   *
   * @param string $conversionWorkspace Required. Name of the conversion workspace
   * resource whose jobs are listed, in the form of: projects/{project}/locations/
   * {location}/conversionWorkspaces/{conversion_workspace}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string completedUntilTime Optional. If provided, only returns jobs
   * that completed until (not including) the given timestamp.
   * @opt_param int maxSize Optional. The maximum number of jobs to return. The
   * service may return fewer than this value. If unspecified, at most 100 jobs
   * are returned. The maximum value is 100; values above 100 are coerced to 100.
   * @opt_param bool returnMostRecentPerJobType Optional. Whether or not to return
   * just the most recent job per job type,
   * @return SearchBackgroundJobsResponse
   * @throws \Google\Service\Exception
   */
  public function searchBackgroundJobs($conversionWorkspace, $optParams = [])
  {
    $params = ['conversionWorkspace' => $conversionWorkspace];
    $params = array_merge($params, $optParams);
    return $this->call('searchBackgroundJobs', [$params], SearchBackgroundJobsResponse::class);
  }
  /**
   * Imports a snapshot of the source database into the conversion workspace.
   * (conversionWorkspaces.seed)
   *
   * @param string $name Name of the conversion workspace resource to seed with
   * new database structure, in the form of: projects/{project}/locations/{locatio
   * n}/conversionWorkspaces/{conversion_workspace}.
   * @param SeedConversionWorkspaceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function seed($name, SeedConversionWorkspaceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('seed', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (conversionWorkspaces.setIamPolicy)
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
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (conversionWorkspaces.testIamPermissions)
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
class_alias(ProjectsLocationsConversionWorkspaces::class, 'Google_Service_DatabaseMigrationService_Resource_ProjectsLocationsConversionWorkspaces');
