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

namespace Google\Service\CloudSupport\Resource;

use Google\Service\CloudSupport\Attachment;
use Google\Service\CloudSupport\CreateAttachmentRequest;
use Google\Service\CloudSupport\Media as MediaModel;

/**
 * The "media" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudsupportService = new Google\Service\CloudSupport(...);
 *   $media = $cloudsupportService->media;
 *  </code>
 */
class Media extends \Google\Service\Resource
{
  /**
   * Download a file attached to a case. When this endpoint is called, no
   * "response body" will be returned. Instead, the attachment's blob will be
   * returned. Note: HTTP requests must append "?alt=media" to the URL. EXAMPLES:
   * cURL: ```shell name="projects/some-
   * project/cases/43594844/attachments/0674M00000WijAnZAJ" curl \ --header
   * "Authorization: Bearer $(gcloud auth print-access-token)" \
   * "https://cloudsupport.googleapis.com/v2/$name:download?alt=media" ``` Python:
   * ```python import googleapiclient.discovery api_version = "v2"
   * supportApiService = googleapiclient.discovery.build(
   * serviceName="cloudsupport", version=api_version, discoveryServiceUrl=f"https:
   * //cloudsupport.googleapis.com/$discovery/rest?version={api_version}", )
   * request = supportApiService.media().download( name="projects/some-
   * project/cases/43595344/attachments/0684M00000Pw6pHQAR" ) request.uri =
   * request.uri.split("?")[0] + "?alt=media" print(request.execute()) ```
   * (media.download)
   *
   * @param string $name The name of the file attachment to download.
   * @param array $optParams Optional parameters.
   * @return MediaModel
   * @throws \Google\Service\Exception
   */
  public function download($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], MediaModel::class);
  }
  /**
   * Create a file attachment on a case or Cloud resource. The attachment must
   * have the following fields set: `filename`. EXAMPLES: cURL: ```shell echo
   * "This text is in a file I'm uploading using CSAPI." \ > "./example_file.txt"
   * case="projects/some-project/cases/43594844" curl \ --header "Authorization:
   * Bearer $(gcloud auth print-access-token)" \ --data-binary
   * @"./example_file.txt" \ "https://cloudsupport.googleapis.com/upload/v2beta/$c
   * ase/attachments?attachment.filename=uploaded_via_curl.txt" ``` Python:
   * ```python import googleapiclient.discovery api_version = "v2"
   * supportApiService = googleapiclient.discovery.build(
   * serviceName="cloudsupport", version=api_version, discoveryServiceUrl=f"https:
   * //cloudsupport.googleapis.com/$discovery/rest?version={api_version}", )
   * file_path = "./example_file.txt" with open(file_path, "w") as file:
   * file.write( "This text is inside a file I'm going to upload using the Cloud
   * Support API.", ) request = supportApiService.media().upload(
   * parent="projects/some-project/cases/43595344", media_body=file_path )
   * request.uri = request.uri.split("?")[0] +
   * "?attachment.filename=uploaded_via_python.txt" print(request.execute()) ```
   * (media.upload)
   *
   * @param string $parent Required. The name of the case or Cloud resource to
   * which the attachment should be attached.
   * @param CreateAttachmentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Attachment
   * @throws \Google\Service\Exception
   */
  public function upload($parent, CreateAttachmentRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], Attachment::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Media::class, 'Google_Service_CloudSupport_Resource_Media');
