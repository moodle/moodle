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

class GoogleCloudAiplatformV1UrlContextMetadata extends \Google\Collection
{
  protected $collection_key = 'urlMetadata';
  protected $urlMetadataType = GoogleCloudAiplatformV1UrlMetadata::class;
  protected $urlMetadataDataType = 'array';

  /**
   * Output only. A list of URL metadata, with one entry for each URL retrieved
   * by the tool.
   *
   * @param GoogleCloudAiplatformV1UrlMetadata[] $urlMetadata
   */
  public function setUrlMetadata($urlMetadata)
  {
    $this->urlMetadata = $urlMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1UrlMetadata[]
   */
  public function getUrlMetadata()
  {
    return $this->urlMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UrlContextMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UrlContextMetadata');
