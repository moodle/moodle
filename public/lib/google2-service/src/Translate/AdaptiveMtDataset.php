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

namespace Google\Service\Translate;

class AdaptiveMtDataset extends \Google\Model
{
  /**
   * Output only. Timestamp when this dataset was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The name of the dataset to show in the interface. The name can be up to 32
   * characters long and can consist only of ASCII Latin letters A-Z and a-z,
   * underscores (_), and ASCII digits 0-9.
   *
   * @var string
   */
  public $displayName;
  /**
   * The number of examples in the dataset.
   *
   * @var int
   */
  public $exampleCount;
  /**
   * Required. The resource name of the dataset, in form of `projects/{project-
   * number-or-id}/locations/{location_id}/adaptiveMtDatasets/{dataset_id}`
   *
   * @var string
   */
  public $name;
  /**
   * The BCP-47 language code of the source language.
   *
   * @var string
   */
  public $sourceLanguageCode;
  /**
   * The BCP-47 language code of the target language.
   *
   * @var string
   */
  public $targetLanguageCode;
  /**
   * Output only. Timestamp when this dataset was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this dataset was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The name of the dataset to show in the interface. The name can be up to 32
   * characters long and can consist only of ASCII Latin letters A-Z and a-z,
   * underscores (_), and ASCII digits 0-9.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The number of examples in the dataset.
   *
   * @param int $exampleCount
   */
  public function setExampleCount($exampleCount)
  {
    $this->exampleCount = $exampleCount;
  }
  /**
   * @return int
   */
  public function getExampleCount()
  {
    return $this->exampleCount;
  }
  /**
   * Required. The resource name of the dataset, in form of `projects/{project-
   * number-or-id}/locations/{location_id}/adaptiveMtDatasets/{dataset_id}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The BCP-47 language code of the source language.
   *
   * @param string $sourceLanguageCode
   */
  public function setSourceLanguageCode($sourceLanguageCode)
  {
    $this->sourceLanguageCode = $sourceLanguageCode;
  }
  /**
   * @return string
   */
  public function getSourceLanguageCode()
  {
    return $this->sourceLanguageCode;
  }
  /**
   * The BCP-47 language code of the target language.
   *
   * @param string $targetLanguageCode
   */
  public function setTargetLanguageCode($targetLanguageCode)
  {
    $this->targetLanguageCode = $targetLanguageCode;
  }
  /**
   * @return string
   */
  public function getTargetLanguageCode()
  {
    return $this->targetLanguageCode;
  }
  /**
   * Output only. Timestamp when this dataset was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdaptiveMtDataset::class, 'Google_Service_Translate_AdaptiveMtDataset');
