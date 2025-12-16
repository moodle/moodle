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

class GoogleCloudAiplatformV1GroundingMetadataSourceFlaggingUri extends \Google\Model
{
  /**
   * The URI that can be used to flag the content.
   *
   * @var string
   */
  public $flagContentUri;
  /**
   * The ID of the place or review.
   *
   * @var string
   */
  public $sourceId;

  /**
   * The URI that can be used to flag the content.
   *
   * @param string $flagContentUri
   */
  public function setFlagContentUri($flagContentUri)
  {
    $this->flagContentUri = $flagContentUri;
  }
  /**
   * @return string
   */
  public function getFlagContentUri()
  {
    return $this->flagContentUri;
  }
  /**
   * The ID of the place or review.
   *
   * @param string $sourceId
   */
  public function setSourceId($sourceId)
  {
    $this->sourceId = $sourceId;
  }
  /**
   * @return string
   */
  public function getSourceId()
  {
    return $this->sourceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingMetadataSourceFlaggingUri::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingMetadataSourceFlaggingUri');
