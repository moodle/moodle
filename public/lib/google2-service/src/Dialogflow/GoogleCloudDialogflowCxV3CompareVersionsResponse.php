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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3CompareVersionsResponse extends \Google\Model
{
  /**
   * JSON representation of the base version content.
   *
   * @var string
   */
  public $baseVersionContentJson;
  /**
   * The timestamp when the two version compares.
   *
   * @var string
   */
  public $compareTime;
  /**
   * JSON representation of the target version content.
   *
   * @var string
   */
  public $targetVersionContentJson;

  /**
   * JSON representation of the base version content.
   *
   * @param string $baseVersionContentJson
   */
  public function setBaseVersionContentJson($baseVersionContentJson)
  {
    $this->baseVersionContentJson = $baseVersionContentJson;
  }
  /**
   * @return string
   */
  public function getBaseVersionContentJson()
  {
    return $this->baseVersionContentJson;
  }
  /**
   * The timestamp when the two version compares.
   *
   * @param string $compareTime
   */
  public function setCompareTime($compareTime)
  {
    $this->compareTime = $compareTime;
  }
  /**
   * @return string
   */
  public function getCompareTime()
  {
    return $this->compareTime;
  }
  /**
   * JSON representation of the target version content.
   *
   * @param string $targetVersionContentJson
   */
  public function setTargetVersionContentJson($targetVersionContentJson)
  {
    $this->targetVersionContentJson = $targetVersionContentJson;
  }
  /**
   * @return string
   */
  public function getTargetVersionContentJson()
  {
    return $this->targetVersionContentJson;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3CompareVersionsResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3CompareVersionsResponse');
