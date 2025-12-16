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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1CachedContent;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListCachedContentsResponse;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "cachedContents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $cachedContents = $aiplatformService->projects_locations_cachedContents;
 *  </code>
 */
class ProjectsLocationsCachedContents extends \Google\Service\Resource
{
  /**
   * Creates cached content, this call will initialize the cached content in the
   * data storage, and users need to pay for the cache data storage.
   * (cachedContents.create)
   *
   * @param string $parent Required. The parent resource where the cached content
   * will be created
   * @param GoogleCloudAiplatformV1CachedContent $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1CachedContent
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1CachedContent $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1CachedContent::class);
  }
  /**
   * Deletes cached content (cachedContents.delete)
   *
   * @param string $name Required. The resource name referring to the cached
   * content
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
   * Gets cached content configurations (cachedContents.get)
   *
   * @param string $name Required. The resource name referring to the cached
   * content
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1CachedContent
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1CachedContent::class);
  }
  /**
   * Lists cached contents in a project
   * (cachedContents.listProjectsLocationsCachedContents)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * cached contents.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of cached contents to
   * return. The service may return fewer than this value. If unspecified, some
   * default (under maximum) number of items will be returned. The maximum value
   * is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListCachedContents` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListCachedContents` must match
   * the call that provided the page token.
   * @return GoogleCloudAiplatformV1ListCachedContentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCachedContents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListCachedContentsResponse::class);
  }
  /**
   * Updates cached content configurations (cachedContents.patch)
   *
   * @param string $name Immutable. Identifier. The server-generated resource name
   * of the cached content Format:
   * projects/{project}/locations/{location}/cachedContents/{cached_content}
   * @param GoogleCloudAiplatformV1CachedContent $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudAiplatformV1CachedContent
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1CachedContent $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1CachedContent::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCachedContents::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsCachedContents');
