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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpAppconnectorsV1ImageConfig extends \Google\Model
{
  /**
   * The stable image that the remote agent will fallback to if the target image
   * fails. Format would be a gcr image path, e.g.: gcr.io/PROJECT-ID/my-
   * image:tag1
   *
   * @var string
   */
  public $stableImage;
  /**
   * The initial image the remote agent will attempt to run for the control
   * plane. Format would be a gcr image path, e.g.: gcr.io/PROJECT-ID/my-
   * image:tag1
   *
   * @var string
   */
  public $targetImage;

  /**
   * The stable image that the remote agent will fallback to if the target image
   * fails. Format would be a gcr image path, e.g.: gcr.io/PROJECT-ID/my-
   * image:tag1
   *
   * @param string $stableImage
   */
  public function setStableImage($stableImage)
  {
    $this->stableImage = $stableImage;
  }
  /**
   * @return string
   */
  public function getStableImage()
  {
    return $this->stableImage;
  }
  /**
   * The initial image the remote agent will attempt to run for the control
   * plane. Format would be a gcr image path, e.g.: gcr.io/PROJECT-ID/my-
   * image:tag1
   *
   * @param string $targetImage
   */
  public function setTargetImage($targetImage)
  {
    $this->targetImage = $targetImage;
  }
  /**
   * @return string
   */
  public function getTargetImage()
  {
    return $this->targetImage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpAppconnectorsV1ImageConfig::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpAppconnectorsV1ImageConfig');
