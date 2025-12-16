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

namespace Google\Service\SecureSourceManager\Resource;

use Google\Service\SecureSourceManager\FetchBlobResponse;
use Google\Service\SecureSourceManager\FetchTreeResponse;
use Google\Service\SecureSourceManager\ListRepositoriesResponse;
use Google\Service\SecureSourceManager\Operation;
use Google\Service\SecureSourceManager\Policy;
use Google\Service\SecureSourceManager\Repository;
use Google\Service\SecureSourceManager\SetIamPolicyRequest;
use Google\Service\SecureSourceManager\TestIamPermissionsRequest;
use Google\Service\SecureSourceManager\TestIamPermissionsResponse;

/**
 * The "repositories" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securesourcemanagerService = new Google\Service\SecureSourceManager(...);
 *   $repositories = $securesourcemanagerService->projects_locations_repositories;
 *  </code>
 */
class ProjectsLocationsRepositories extends \Google\Service\Resource
{
  /**
   * Creates a new repository in a given project and location. The
   * Repository.Instance field is required in the request body for requests using
   * the securesourcemanager.googleapis.com endpoint. (repositories.create)
   *
   * @param string $parent Required. The project in which to create the
   * repository. Values are of the form
   * `projects/{project_number}/locations/{location_id}`
   * @param Repository $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string repositoryId Required. The ID to use for the repository,
   * which will become the final component of the repository's resource name. This
   * value should be 4-63 characters, and valid characters are /a-z-/.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Repository $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a Repository. (repositories.delete)
   *
   * @param string $name Required. Name of the repository to delete. The format is
   * `projects/{project_number}/locations/{location_id}/repositories/{repository_i
   * d}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the repository is
   * not found, the request will succeed but no action will be taken on the
   * server.
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
   * Fetches a blob from a repository. (repositories.fetchBlob)
   *
   * @param string $repository Required. The format is `projects/{project_number}/
   * locations/{location_id}/repositories/{repository_id}`. Specifies the
   * repository containing the blob.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string sha Required. The SHA-1 hash of the blob to retrieve.
   * @return FetchBlobResponse
   * @throws \Google\Service\Exception
   */
  public function fetchBlob($repository, $optParams = [])
  {
    $params = ['repository' => $repository];
    $params = array_merge($params, $optParams);
    return $this->call('fetchBlob', [$params], FetchBlobResponse::class);
  }
  /**
   * Fetches a tree from a repository. (repositories.fetchTree)
   *
   * @param string $repository Required. The format is `projects/{project_number}/
   * locations/{location_id}/repositories/{repository_id}`. Specifies the
   * repository to fetch the tree from.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, at most 10,000 items will be
   * returned.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @opt_param bool recursive Optional. If true, include all subfolders and their
   * files in the response. If false, only the immediate children are returned.
   * @opt_param string ref Optional. `ref` can be a SHA-1 hash, a branch name, or
   * a tag. Specifies which tree to fetch. If not specified, the default branch
   * will be used.
   * @return FetchTreeResponse
   * @throws \Google\Service\Exception
   */
  public function fetchTree($repository, $optParams = [])
  {
    $params = ['repository' => $repository];
    $params = array_merge($params, $optParams);
    return $this->call('fetchTree', [$params], FetchTreeResponse::class);
  }
  /**
   * Gets metadata of a repository. (repositories.get)
   *
   * @param string $name Required. Name of the repository to retrieve. The format
   * is `projects/{project_number}/locations/{location_id}/repositories/{repositor
   * y_id}`.
   * @param array $optParams Optional parameters.
   * @return Repository
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Repository::class);
  }
  /**
   * Get IAM policy for a repository. (repositories.getIamPolicy)
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
   * Lists Repositories in a given project and location. The instance field is
   * required in the query parameter for requests using the
   * securesourcemanager.googleapis.com endpoint.
   * (repositories.listProjectsLocationsRepositories)
   *
   * @param string $parent Required. Parent value for ListRepositoriesRequest.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter results.
   * @opt_param string instance Optional. The name of the instance in which the
   * repository is hosted, formatted as
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`.
   * When listing repositories via securesourcemanager.googleapis.com, this field
   * is required. When listing repositories via *.sourcemanager.dev, this field is
   * ignored.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListRepositoriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositories($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRepositoriesResponse::class);
  }
  /**
   * Updates the metadata of a repository. (repositories.patch)
   *
   * @param string $name Optional. A unique identifier for a repository. The name
   * should be of the format:
   * `projects/{project}/locations/{location_id}/repositories/{repository_id}`
   * @param Repository $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the repository resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. False by default. If set to true, the
   * request is validated and the user is provided with an expected result, but no
   * actual change is made.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Repository $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Set IAM policy on a repository. (repositories.setIamPolicy)
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
   * Test IAM permissions on a repository. IAM permission checks are not required
   * on this method. (repositories.testIamPermissions)
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
class_alias(ProjectsLocationsRepositories::class, 'Google_Service_SecureSourceManager_Resource_ProjectsLocationsRepositories');
