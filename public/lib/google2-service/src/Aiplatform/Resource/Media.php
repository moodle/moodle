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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1UploadRagFileRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1UploadRagFileResponse;

/**
 * The "media" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $media = $aiplatformService->media;
 *  </code>
 */
class Media extends \Google\Service\Resource
{
  /**
   * Upload a file into a RagCorpus. (media.upload)
   *
   * @param string $parent Required. The name of the RagCorpus resource into which
   * to upload the file. Format:
   * `projects/{project}/locations/{location}/ragCorpora/{rag_corpus}`
   * @param GoogleCloudAiplatformV1UploadRagFileRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1UploadRagFileResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, GoogleCloudAiplatformV1UploadRagFileRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], GoogleCloudAiplatformV1UploadRagFileResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Media::class, 'Google_Service_Aiplatform_Resource_Media');
