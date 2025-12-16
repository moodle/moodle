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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListRagCorporaResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1RagCorpus;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "ragCorpora" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $ragCorpora = $aiplatformService->projects_locations_ragCorpora;
 *  </code>
 */
class ProjectsLocationsRagCorpora extends \Google\Service\Resource
{
  /**
   * Creates a RagCorpus. (ragCorpora.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the RagCorpus in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1RagCorpus $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1RagCorpus $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a RagCorpus. (ragCorpora.delete)
   *
   * @param string $name Required. The name of the RagCorpus resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/ragCorpora/{rag_corpus}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, any RagFiles in this
   * RagCorpus will also be deleted. Otherwise, the request will only work if the
   * RagCorpus has no RagFiles.
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
   * Gets a RagCorpus. (ragCorpora.get)
   *
   * @param string $name Required. The name of the RagCorpus resource. Format:
   * `projects/{project}/locations/{location}/ragCorpora/{rag_corpus}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1RagCorpus
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1RagCorpus::class);
  }
  /**
   * Lists RagCorpora in a Location. (ragCorpora.listProjectsLocationsRagCorpora)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the RagCorpora. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token. Typically
   * obtained via ListRagCorporaResponse.next_page_token of the previous
   * VertexRagDataService.ListRagCorpora call.
   * @return GoogleCloudAiplatformV1ListRagCorporaResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRagCorpora($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListRagCorporaResponse::class);
  }
  /**
   * Updates a RagCorpus. (ragCorpora.patch)
   *
   * @param string $name Output only. The resource name of the RagCorpus.
   * @param GoogleCloudAiplatformV1RagCorpus $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1RagCorpus $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRagCorpora::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsRagCorpora');
