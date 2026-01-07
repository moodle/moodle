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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\GoogleChecksReportV1alphaAnalyzeUploadRequest;
use Google\Service\ChecksService\Operation;

/**
 * The "media" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $media = $checksService->media;
 *  </code>
 */
class Media extends \Google\Service\Resource
{
  /**
   * Analyzes the uploaded app bundle and returns a google.longrunning.Operation
   * containing the generated Report. ## Example (upload only) Send a regular POST
   * request with the header `X-Goog-Upload-Protocol: raw`. ``` POST https://check
   * s.googleapis.com/upload/v1alpha/{parent=accounts/apps}/reports:analyzeUpload
   * HTTP/1.1 X-Goog-Upload-Protocol: raw Content-Length: Content-Type:
   * application/octet-stream ``` ## Example (upload with metadata) Send a
   * multipart POST request where the first body part contains the metadata JSON
   * and the second body part contains the binary upload. Include the header
   * `X-Goog-Upload-Protocol: multipart`. ``` POST https://checks.googleapis.com/u
   * pload/v1alpha/{parent=accounts/apps}/reports:analyzeUpload HTTP/1.1 X-Goog-
   * Upload-Protocol: multipart Content-Length: ? Content-Type: multipart/related;
   * boundary=BOUNDARY --BOUNDARY Content-Type: application/json
   * {"code_reference_id":"db5bcc20f94055fb5bc08cbb9b0e7a5530308786"} --BOUNDARY
   * --BOUNDARY-- ``` *Note:* Metadata-only requests are not supported.
   * (media.upload)
   *
   * @param string $parent Required. Resource name of the app. Example:
   * `accounts/123/apps/456`
   * @param GoogleChecksReportV1alphaAnalyzeUploadRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function upload($parent, GoogleChecksReportV1alphaAnalyzeUploadRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Media::class, 'Google_Service_ChecksService_Resource_Media');
