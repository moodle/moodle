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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaVideoDataItem extends \Google\Model
{
  /**
   * Required. Google Cloud Storage URI points to the original video in user's
   * bucket. The video is up to 50 GB in size and up to 3 hour in duration.
   *
   * @var string
   */
  public $gcsUri;
  /**
   * Output only. The mime type of the content of the video. Only the videos in
   * below listed mime types are supported. Supported mime_type: - video/mp4 -
   * video/avi - video/quicktime
   *
   * @var string
   */
  public $mimeType;

  /**
   * Required. Google Cloud Storage URI points to the original video in user's
   * bucket. The video is up to 50 GB in size and up to 3 hour in duration.
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
   * Output only. The mime type of the content of the video. Only the videos in
   * below listed mime types are supported. Supported mime_type: - video/mp4 -
   * video/avi - video/quicktime
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaVideoDataItem::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaVideoDataItem');
