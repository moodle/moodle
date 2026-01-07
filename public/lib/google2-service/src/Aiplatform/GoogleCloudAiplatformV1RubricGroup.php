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

class GoogleCloudAiplatformV1RubricGroup extends \Google\Collection
{
  protected $collection_key = 'rubrics';
  /**
   * Human-readable name for the group. This should be unique within a given
   * context if used for display or selection. Example: "Instruction Following
   * V1", "Content Quality - Summarization Task".
   *
   * @var string
   */
  public $displayName;
  /**
   * Unique identifier for the group.
   *
   * @var string
   */
  public $groupId;
  protected $rubricsType = GoogleCloudAiplatformV1Rubric::class;
  protected $rubricsDataType = 'array';

  /**
   * Human-readable name for the group. This should be unique within a given
   * context if used for display or selection. Example: "Instruction Following
   * V1", "Content Quality - Summarization Task".
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
   * Unique identifier for the group.
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * Rubrics that are part of this group.
   *
   * @param GoogleCloudAiplatformV1Rubric[] $rubrics
   */
  public function setRubrics($rubrics)
  {
    $this->rubrics = $rubrics;
  }
  /**
   * @return GoogleCloudAiplatformV1Rubric[]
   */
  public function getRubrics()
  {
    return $this->rubrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RubricGroup::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RubricGroup');
