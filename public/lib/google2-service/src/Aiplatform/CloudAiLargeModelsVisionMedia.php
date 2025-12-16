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

class CloudAiLargeModelsVisionMedia extends \Google\Model
{
  protected $imageType = CloudAiLargeModelsVisionImage::class;
  protected $imageDataType = '';
  protected $videoType = CloudAiLargeModelsVisionVideo::class;
  protected $videoDataType = '';

  /**
   * Image.
   *
   * @param CloudAiLargeModelsVisionImage $image
   */
  public function setImage(CloudAiLargeModelsVisionImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return CloudAiLargeModelsVisionImage
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Video
   *
   * @param CloudAiLargeModelsVisionVideo $video
   */
  public function setVideo(CloudAiLargeModelsVisionVideo $video)
  {
    $this->video = $video;
  }
  /**
   * @return CloudAiLargeModelsVisionVideo
   */
  public function getVideo()
  {
    return $this->video;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiLargeModelsVisionMedia::class, 'Google_Service_Aiplatform_CloudAiLargeModelsVisionMedia');
