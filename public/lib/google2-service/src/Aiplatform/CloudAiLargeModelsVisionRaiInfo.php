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

class CloudAiLargeModelsVisionRaiInfo extends \Google\Collection
{
  protected $collection_key = 'scores';
  /**
   * List of blocked entities from the blocklist if it is detected.
   *
   * @var string[]
   */
  public $blockedEntities;
  protected $detectedLabelsType = CloudAiLargeModelsVisionRaiInfoDetectedLabels::class;
  protected $detectedLabelsDataType = 'array';
  /**
   * The model name used to indexing into the RaiFilterConfig map. Would either
   * be one of imagegeneration@002-006, imagen-3.0-... api endpoint names, or
   * internal names used for mapping to different filter configs (genselfie,
   * ai_watermark) than its api endpoint.
   *
   * @var string
   */
  public $modelName;
  /**
   * List of rai categories' information to return
   *
   * @var string[]
   */
  public $raiCategories;
  /**
   * List of rai scores mapping to the rai categories. Rounded to 1 decimal
   * place.
   *
   * @var float[]
   */
  public $scores;

  /**
   * List of blocked entities from the blocklist if it is detected.
   *
   * @param string[] $blockedEntities
   */
  public function setBlockedEntities($blockedEntities)
  {
    $this->blockedEntities = $blockedEntities;
  }
  /**
   * @return string[]
   */
  public function getBlockedEntities()
  {
    return $this->blockedEntities;
  }
  /**
   * The list of detected labels for different rai categories.
   *
   * @param CloudAiLargeModelsVisionRaiInfoDetectedLabels[] $detectedLabels
   */
  public function setDetectedLabels($detectedLabels)
  {
    $this->detectedLabels = $detectedLabels;
  }
  /**
   * @return CloudAiLargeModelsVisionRaiInfoDetectedLabels[]
   */
  public function getDetectedLabels()
  {
    return $this->detectedLabels;
  }
  /**
   * The model name used to indexing into the RaiFilterConfig map. Would either
   * be one of imagegeneration@002-006, imagen-3.0-... api endpoint names, or
   * internal names used for mapping to different filter configs (genselfie,
   * ai_watermark) than its api endpoint.
   *
   * @param string $modelName
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }
  /**
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }
  /**
   * List of rai categories' information to return
   *
   * @param string[] $raiCategories
   */
  public function setRaiCategories($raiCategories)
  {
    $this->raiCategories = $raiCategories;
  }
  /**
   * @return string[]
   */
  public function getRaiCategories()
  {
    return $this->raiCategories;
  }
  /**
   * List of rai scores mapping to the rai categories. Rounded to 1 decimal
   * place.
   *
   * @param float[] $scores
   */
  public function setScores($scores)
  {
    $this->scores = $scores;
  }
  /**
   * @return float[]
   */
  public function getScores()
  {
    return $this->scores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiLargeModelsVisionRaiInfo::class, 'Google_Service_Aiplatform_CloudAiLargeModelsVisionRaiInfo');
