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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainQaQuestionTag extends \Google\Collection
{
  protected $collection_key = 'qaQuestionIds';
  /**
   * Output only. The time at which the question tag was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. A user-specified display name for the tag.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. Resource name for the QaQuestionTag Format
   * projects/{project}/locations/{location}/qaQuestionTags/{qa_question_tag} In
   * the above format, the last segment, i.e., qa_question_tag, is a server-
   * generated ID corresponding to the tag resource.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The list of Scorecard Question IDs that the tag applies to. Each
   * QaQuestionId is represented as a full resource name containing the Question
   * ID. Lastly, Since a tag may not necessarily be referenced by any Scorecard
   * Questions, we treat this field as optional.
   *
   * @var string[]
   */
  public $qaQuestionIds;
  /**
   * Output only. The most recent time at which the question tag was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the question tag was created.
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
   * Required. A user-specified display name for the tag.
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
   * Identifier. Resource name for the QaQuestionTag Format
   * projects/{project}/locations/{location}/qaQuestionTags/{qa_question_tag} In
   * the above format, the last segment, i.e., qa_question_tag, is a server-
   * generated ID corresponding to the tag resource.
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
   * Optional. The list of Scorecard Question IDs that the tag applies to. Each
   * QaQuestionId is represented as a full resource name containing the Question
   * ID. Lastly, Since a tag may not necessarily be referenced by any Scorecard
   * Questions, we treat this field as optional.
   *
   * @param string[] $qaQuestionIds
   */
  public function setQaQuestionIds($qaQuestionIds)
  {
    $this->qaQuestionIds = $qaQuestionIds;
  }
  /**
   * @return string[]
   */
  public function getQaQuestionIds()
  {
    return $this->qaQuestionIds;
  }
  /**
   * Output only. The most recent time at which the question tag was updated.
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
class_alias(GoogleCloudContactcenterinsightsV1mainQaQuestionTag::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainQaQuestionTag');
