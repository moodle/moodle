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

namespace Google\Service\SecurityPosture;

class PostureTemplate extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The posture template follows the latest controls and standards.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The posture template uses outdated controls and standards. We recommend
   * that you use a newer revision of the posture template.
   */
  public const STATE_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'policySets';
  /**
   * Output only. The categories that the posture template belongs to, as
   * determined by the Security Posture API.
   *
   * @var string[]
   */
  public $categories;
  /**
   * Output only. A description of the posture template.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Identifier. The name of the posture template, in the format `o
   * rganizations/{organization}/locations/global/postureTemplates/{posture_temp
   * late}`.
   *
   * @var string
   */
  public $name;
  protected $policySetsType = PolicySet::class;
  protected $policySetsDataType = 'array';
  /**
   * Output only. A string that identifies the revision of the posture template.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Output only. The state of the posture template at the specified
   * `revision_id`.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The categories that the posture template belongs to, as
   * determined by the Security Posture API.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Output only. A description of the posture template.
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
   * Output only. Identifier. The name of the posture template, in the format `o
   * rganizations/{organization}/locations/global/postureTemplates/{posture_temp
   * late}`.
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
   * Output only. The PolicySet resources that the posture template includes.
   *
   * @param PolicySet[] $policySets
   */
  public function setPolicySets($policySets)
  {
    $this->policySets = $policySets;
  }
  /**
   * @return PolicySet[]
   */
  public function getPolicySets()
  {
    return $this->policySets;
  }
  /**
   * Output only. A string that identifies the revision of the posture template.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. The state of the posture template at the specified
   * `revision_id`.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DEPRECATED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostureTemplate::class, 'Google_Service_SecurityPosture_PostureTemplate');
