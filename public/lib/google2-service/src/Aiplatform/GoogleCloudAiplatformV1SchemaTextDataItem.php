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

class GoogleCloudAiplatformV1SchemaTextDataItem extends \Google\Model
{
  /**
   * Output only. Google Cloud Storage URI points to a copy of the original text
   * in the Vertex-managed bucket in the user's project. The text file is up to
   * 10MB in size.
   *
   * @var string
   */
  public $gcsUri;

  /**
   * Output only. Google Cloud Storage URI points to a copy of the original text
   * in the Vertex-managed bucket in the user's project. The text file is up to
   * 10MB in size.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTextDataItem::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTextDataItem');
