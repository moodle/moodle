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

class GoogleCloudAiplatformV1VideoMetadata extends \Google\Model
{
  /**
   * Optional. The end offset of the video.
   *
   * @var string
   */
  public $endOffset;
  /**
   * Optional. The frame rate of the video sent to the model. If not specified,
   * the default value is 1.0. The valid range is (0.0, 24.0].
   *
   * @var 
   */
  public $fps;
  /**
   * Optional. The start offset of the video.
   *
   * @var string
   */
  public $startOffset;

  /**
   * Optional. The end offset of the video.
   *
   * @param string $endOffset
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return string
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  public function setFps($fps)
  {
    $this->fps = $fps;
  }
  public function getFps()
  {
    return $this->fps;
  }
  /**
   * Optional. The start offset of the video.
   *
   * @param string $startOffset
   */
  public function setStartOffset($startOffset)
  {
    $this->startOffset = $startOffset;
  }
  /**
   * @return string
   */
  public function getStartOffset()
  {
    return $this->startOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1VideoMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1VideoMetadata');
