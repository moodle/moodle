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

namespace Google\Service\CloudHealthcare;

class Image extends \Google\Model
{
  /**
   * Input only. Points to a Cloud Storage URI containing the consent artifact
   * content. The URI must be in the following format:
   * `gs://{bucket_id}/{object_id}`. The Cloud Healthcare API service account
   * must have the `roles/storage.objectViewer` Cloud IAM role for this Cloud
   * Storage location. The consent artifact content at this URI is copied to a
   * Cloud Storage location managed by the Cloud Healthcare API. Responses to
   * fetching requests return the consent artifact content in raw_bytes.
   *
   * @var string
   */
  public $gcsUri;
  /**
   * Consent artifact content represented as a stream of bytes. This field is
   * populated when returned in GetConsentArtifact response, but not included in
   * CreateConsentArtifact and ListConsentArtifact response.
   *
   * @var string
   */
  public $rawBytes;

  /**
   * Input only. Points to a Cloud Storage URI containing the consent artifact
   * content. The URI must be in the following format:
   * `gs://{bucket_id}/{object_id}`. The Cloud Healthcare API service account
   * must have the `roles/storage.objectViewer` Cloud IAM role for this Cloud
   * Storage location. The consent artifact content at this URI is copied to a
   * Cloud Storage location managed by the Cloud Healthcare API. Responses to
   * fetching requests return the consent artifact content in raw_bytes.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
  /**
   * Consent artifact content represented as a stream of bytes. This field is
   * populated when returned in GetConsentArtifact response, but not included in
   * CreateConsentArtifact and ListConsentArtifact response.
   *
   * @param string $rawBytes
   */
  public function setRawBytes($rawBytes)
  {
    $this->rawBytes = $rawBytes;
  }
  /**
   * @return string
   */
  public function getRawBytes()
  {
    return $this->rawBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_CloudHealthcare_Image');
