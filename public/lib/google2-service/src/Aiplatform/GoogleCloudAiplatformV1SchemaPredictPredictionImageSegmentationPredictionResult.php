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

class GoogleCloudAiplatformV1SchemaPredictPredictionImageSegmentationPredictionResult extends \Google\Model
{
  /**
   * A PNG image where each pixel in the mask represents the category in which
   * the pixel in the original image was predicted to belong to. The size of
   * this image will be the same as the original image. The mapping between the
   * AnntoationSpec and the color can be found in model's metadata. The model
   * will choose the most likely category and if none of the categories reach
   * the confidence threshold, the pixel will be marked as background.
   *
   * @var string
   */
  public $categoryMask;
  /**
   * A one channel image which is encoded as an 8bit lossless PNG. The size of
   * the image will be the same as the original image. For a specific pixel,
   * darker color means less confidence in correctness of the cateogry in the
   * categoryMask for the corresponding pixel. Black means no confidence and
   * white means complete confidence.
   *
   * @var string
   */
  public $confidenceMask;

  /**
   * A PNG image where each pixel in the mask represents the category in which
   * the pixel in the original image was predicted to belong to. The size of
   * this image will be the same as the original image. The mapping between the
   * AnntoationSpec and the color can be found in model's metadata. The model
   * will choose the most likely category and if none of the categories reach
   * the confidence threshold, the pixel will be marked as background.
   *
   * @param string $categoryMask
   */
  public function setCategoryMask($categoryMask)
  {
    $this->categoryMask = $categoryMask;
  }
  /**
   * @return string
   */
  public function getCategoryMask()
  {
    return $this->categoryMask;
  }
  /**
   * A one channel image which is encoded as an 8bit lossless PNG. The size of
   * the image will be the same as the original image. For a specific pixel,
   * darker color means less confidence in correctness of the cateogry in the
   * categoryMask for the corresponding pixel. Black means no confidence and
   * white means complete confidence.
   *
   * @param string $confidenceMask
   */
  public function setConfidenceMask($confidenceMask)
  {
    $this->confidenceMask = $confidenceMask;
  }
  /**
   * @return string
   */
  public function getConfidenceMask()
  {
    return $this->confidenceMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictPredictionImageSegmentationPredictionResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictPredictionImageSegmentationPredictionResult');
