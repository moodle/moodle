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

namespace Google\Service\ArtifactRegistry\Resource;

use Google\Service\ArtifactRegistry\ArtifactregistryEmpty;
use Google\Service\ArtifactRegistry\ListTagsResponse;
use Google\Service\ArtifactRegistry\Tag;

/**
 * The "tags" collection of methods.
 * Typical usage is:
 *  <code>
 *   $artifactregistryService = new Google\Service\ArtifactRegistry(...);
 *   $tags = $artifactregistryService->projects_locations_repositories_packages_tags;
 *  </code>
 */
class ProjectsLocationsRepositoriesPackagesTags extends \Google\Service\Resource
{
  /**
   * Creates a tag. (tags.create)
   *
   * @param string $parent The name of the parent resource where the tag will be
   * created.
   * @param Tag $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string tagId The tag id to use for this repository.
   * @return Tag
   * @throws \Google\Service\Exception
   */
  public function create($parent, Tag $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Tag::class);
  }
  /**
   * Deletes a tag. (tags.delete)
   *
   * @param string $name The name of the tag to delete.
   * @param array $optParams Optional parameters.
   * @return ArtifactregistryEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ArtifactregistryEmpty::class);
  }
  /**
   * Gets a tag. (tags.get)
   *
   * @param string $name The name of the tag to retrieve.
   * @param array $optParams Optional parameters.
   * @return Tag
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Tag::class);
  }
  /**
   * Lists tags. (tags.listProjectsLocationsRepositoriesPackagesTags)
   *
   * @param string $parent The name of the parent package whose tags will be
   * listed. For example: `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression for filtering the results of the
   * request. Filter rules are case insensitive. The fields eligible for filtering
   * are: * `name` * `version` Examples of using a filter: To filter the results
   * of your request to tags with the name `my-tag` in package `my-package` in
   * repository `my-repo` in project "`y-project` in the us-central region, append
   * the following filter expression to your request: * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/packages/my-
   * package/tags/my-tag"` You can also use wildcards to match any number of
   * characters before or after the value: * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/packages/my-
   * package/tags/my*"` * `name="projects/my-project/locations/us-
   * central1/repositories/my-repo/packages/my-package/tagstag"` *
   * `name="projects/my-project/locations/us-central1/repositories/my-
   * repo/packages/my-package/tagstag*"` To filter the results of your request to
   * tags applied to the version `1.0` in package `my-package`, append the
   * following filter expression to your request: * `version="projects/my-
   * project/locations/us-central1/repositories/my-repo/packages/my-
   * package/versions/1.0"`
   * @opt_param int pageSize The maximum number of tags to return. Maximum page
   * size is 1,000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request, if any.
   * @return ListTagsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesPackagesTags($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTagsResponse::class);
  }
  /**
   * Updates a tag. (tags.patch)
   *
   * @param string $name The name of the tag, for example:
   * "projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/tags/tag1". If the package part
   * contains slashes, the slashes are escaped. The tag part can only have
   * characters in [a-zA-Z0-9\-._~:@], anything else must be URL encoded.
   * @param Tag $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The update mask applies to the resource. For the
   * `FieldMask` definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   * @return Tag
   * @throws \Google\Service\Exception
   */
  public function patch($name, Tag $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Tag::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesPackagesTags::class, 'Google_Service_ArtifactRegistry_Resource_ProjectsLocationsRepositoriesPackagesTags');
