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

class CloudAiLargeModelsVisionRaiInfoDetectedLabelsEntity extends \Google\Model
{
  protected $boundingBoxType = CloudAiLargeModelsVisionRaiInfoDetectedLabelsBoundingBox::class;
  protected $boundingBoxDataType = '';
  /**
   * Description of the label
   *
   * @var string
   */
  public $description;
  /**
   * The intersection ratio between the detection bounding box and the mask.
   *
   * @var float
   */
  public $iouScore;
  /**
   * MID of the label
   *
   * @var string
   */
  public $mid;
  /**
   * Confidence score of the label
   *
   * @var float
   */
  public $score;

  /**
   * Bounding box of the label
   *
   * @param CloudAiLargeModelsVisionRaiInfoDetectedLabelsBoundingBox $boundingBox
   */
  public function setBoundingBox(CloudAiLargeModelsVisionRaiInfoDetectedLabelsBoundingBox $boundingBox)
  {
    $this->boundingBox = $boundingBox;
  }
  /**
   * @return CloudAiLargeModelsVisionRaiInfoDetectedLabelsBoundingBox
   */
  public function getBoundingBox()
  {
    return $this->boundingBox;
  }
  /**
   * Description of the label
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The intersection ratio between the detection bounding box and the mask.
   *
   * @param float $iouScore
   */
  public function setIouScore($iouScore)
  {
    $this->iouScore = $iouScore;
  }
  /**
   * @return float
   */
  public function getIouScore()
  {
    return $this->iouScore;
  }
  /**
   * MID of the label
   *
   * @param string $mid
   */
  public function setMid($mid)
  {
    $this->mid = $mid;
  }
  /**
   * @return string
   */
  public function getMid()
  {
    return $this->mid;
  }
  /**
   * Confidence score of the label
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiLargeModelsVisionRaiInfoDetectedLabelsEntity::class, 'Google_Service_Aiplatform_CloudAiLargeModelsVisionRaiInfoDetectedLabelsEntity');
