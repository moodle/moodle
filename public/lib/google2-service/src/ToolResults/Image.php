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

namespace Google\Service\ToolResults;

class Image extends \Google\Model
{
  protected $errorType = Status::class;
  protected $errorDataType = '';
  protected $sourceImageType = ToolOutputReference::class;
  protected $sourceImageDataType = '';
  /**
   * The step to which the image is attached. Always set.
   *
   * @var string
   */
  public $stepId;
  protected $thumbnailType = Thumbnail::class;
  protected $thumbnailDataType = '';

  /**
   * An error explaining why the thumbnail could not be rendered.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * A reference to the full-size, original image. This is the same as the
   * tool_outputs entry for the image under its Step. Always set.
   *
   * @param ToolOutputReference $sourceImage
   */
  public function setSourceImage(ToolOutputReference $sourceImage)
  {
    $this->sourceImage = $sourceImage;
  }
  /**
   * @return ToolOutputReference
   */
  public function getSourceImage()
  {
    return $this->sourceImage;
  }
  /**
   * The step to which the image is attached. Always set.
   *
   * @param string $stepId
   */
  public function setStepId($stepId)
  {
    $this->stepId = $stepId;
  }
  /**
   * @return string
   */
  public function getStepId()
  {
    return $this->stepId;
  }
  /**
   * The thumbnail.
   *
   * @param Thumbnail $thumbnail
   */
  public function setThumbnail(Thumbnail $thumbnail)
  {
    $this->thumbnail = $thumbnail;
  }
  /**
   * @return Thumbnail
   */
  public function getThumbnail()
  {
    return $this->thumbnail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_ToolResults_Image');
