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

namespace Google\Service\Vision;

class ImageSource extends \Google\Model
{
  /**
   * **Use `image_uri` instead.** The Google Cloud Storage URI of the form
   * `gs://bucket_name/object_name`. Object versioning is not supported. See
   * [Google Cloud Storage Request
   * URIs](https://cloud.google.com/storage/docs/reference-uris) for more info.
   *
   * @var string
   */
  public $gcsImageUri;
  /**
   * The URI of the source image. Can be either: 1. A Google Cloud Storage URI
   * of the form `gs://bucket_name/object_name`. Object versioning is not
   * supported. See [Google Cloud Storage Request
   * URIs](https://cloud.google.com/storage/docs/reference-uris) for more info.
   * 2. A publicly-accessible image HTTP/HTTPS URL. When fetching images from
   * HTTP/HTTPS URLs, Google cannot guarantee that the request will be
   * completed. Your request may fail if the specified host denies the request
   * (e.g. due to request throttling or DOS prevention), or if Google throttles
   * requests to the site for abuse prevention. You should not depend on
   * externally-hosted images for production applications. When both
   * `gcs_image_uri` and `image_uri` are specified, `image_uri` takes
   * precedence.
   *
   * @var string
   */
  public $imageUri;

  /**
   * **Use `image_uri` instead.** The Google Cloud Storage URI of the form
   * `gs://bucket_name/object_name`. Object versioning is not supported. See
   * [Google Cloud Storage Request
   * URIs](https://cloud.google.com/storage/docs/reference-uris) for more info.
   *
   * @param string $gcsImageUri
   */
  public function setGcsImageUri($gcsImageUri)
  {
    $this->gcsImageUri = $gcsImageUri;
  }
  /**
   * @return string
   */
  public function getGcsImageUri()
  {
    return $this->gcsImageUri;
  }
  /**
   * The URI of the source image. Can be either: 1. A Google Cloud Storage URI
   * of the form `gs://bucket_name/object_name`. Object versioning is not
   * supported. See [Google Cloud Storage Request
   * URIs](https://cloud.google.com/storage/docs/reference-uris) for more info.
   * 2. A publicly-accessible image HTTP/HTTPS URL. When fetching images from
   * HTTP/HTTPS URLs, Google cannot guarantee that the request will be
   * completed. Your request may fail if the specified host denies the request
   * (e.g. due to request throttling or DOS prevention), or if Google throttles
   * requests to the site for abuse prevention. You should not depend on
   * externally-hosted images for production applications. When both
   * `gcs_image_uri` and `image_uri` are specified, `image_uri` takes
   * precedence.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageSource::class, 'Google_Service_Vision_ImageSource');
