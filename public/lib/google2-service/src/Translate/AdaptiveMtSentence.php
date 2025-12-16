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

class AdaptiveMtSentence extends \Google\Model
{
  /**
   * Output only. Timestamp when this sentence was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The resource name of the file, in form of `projects/{project-
   * number-or-id}/locations/{location_id}/adaptiveMtDatasets/{dataset}/adaptive
   * MtFiles/{file}/adaptiveMtSentences/{sentence}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The source sentence.
   *
   * @var string
   */
  public $sourceSentence;
  /**
   * Required. The target sentence.
   *
   * @var string
   */
  public $targetSentence;
  /**
   * Output only. Timestamp when this sentence was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this sentence was created.
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
   * Required. The resource name of the file, in form of `projects/{project-
   * number-or-id}/locations/{location_id}/adaptiveMtDatasets/{dataset}/adaptive
   * MtFiles/{file}/adaptiveMtSentences/{sentence}`
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
   * Required. The source sentence.
   *
   * @param string $sourceSentence
   */
  public function setSourceSentence($sourceSentence)
  {
    $this->sourceSentence = $sourceSentence;
  }
  /**
   * @return string
   */
  public function getSourceSentence()
  {
    return $this->sourceSentence;
  }
  /**
   * Required. The target sentence.
   *
   * @param string $targetSentence
   */
  public function setTargetSentence($targetSentence)
  {
    $this->targetSentence = $targetSentence;
  }
  /**
   * @return string
   */
  public function getTargetSentence()
  {
    return $this->targetSentence;
  }
  /**
   * Output only. Timestamp when this sentence was last updated.
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
class_alias(AdaptiveMtSentence::class, 'Google_Service_Translate_AdaptiveMtSentence');
