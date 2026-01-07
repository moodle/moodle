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

namespace Google\Service\CloudAsset;

class GoogleCloudAssetV1GcsDestination extends \Google\Model
{
  /**
   * Required. The URI of the Cloud Storage object. It's the same URI that is
   * used by gsutil. Example: "gs://bucket_name/object_name". See [Viewing and
   * Editing Object Metadata](https://cloud.google.com/storage/docs/viewing-
   * editing-metadata) for more information. If the specified Cloud Storage
   * object already exists and there is no
   * [hold](https://cloud.google.com/storage/docs/object-holds), it will be
   * overwritten with the analysis result.
   *
   * @var string
   */
  public $uri;

  /**
   * Required. The URI of the Cloud Storage object. It's the same URI that is
   * used by gsutil. Example: "gs://bucket_name/object_name". See [Viewing and
   * Editing Object Metadata](https://cloud.google.com/storage/docs/viewing-
   * editing-metadata) for more information. If the specified Cloud Storage
   * object already exists and there is no
   * [hold](https://cloud.google.com/storage/docs/object-holds), it will be
   * overwritten with the analysis result.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1GcsDestination::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1GcsDestination');
