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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1GlossaryTerm extends \Google\Model
{
  /**
   * Output only. The time at which the GlossaryTerm was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The user-mutable description of the GlossaryTerm.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name of the GlossaryTerm. This is user-
   * mutable. This will be same as the GlossaryTermId, if not specified.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. User-defined labels for the GlossaryTerm.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The resource name of the GlossaryTerm. Format: pro
   * jects/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_i
   * d}/terms/{term_id}
   *
   * @var string
   */
  public $name;
  /**
   * Required. The immediate parent of the GlossaryTerm in the resource-
   * hierarchy. It can either be a Glossary or a GlossaryCategory. Format: proje
   * cts/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_id}
   * OR projects/{project_id_or_number}/locations/{location_id}/glossaries/{glos
   * sary_id}/categories/{category_id}
   *
   * @var string
   */
  public $parent;
  /**
   * Output only. System generated unique id for the GlossaryTerm. This ID will
   * be different if the GlossaryTerm is deleted and re-created with the same
   * name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which the GlossaryTerm was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the GlossaryTerm was created.
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
   * Optional. The user-mutable description of the GlossaryTerm.
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
   * Optional. User friendly display name of the GlossaryTerm. This is user-
   * mutable. This will be same as the GlossaryTermId, if not specified.
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
   * Optional. User-defined labels for the GlossaryTerm.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Identifier. The resource name of the GlossaryTerm. Format: pro
   * jects/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_i
   * d}/terms/{term_id}
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
   * Required. The immediate parent of the GlossaryTerm in the resource-
   * hierarchy. It can either be a Glossary or a GlossaryCategory. Format: proje
   * cts/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_id}
   * OR projects/{project_id_or_number}/locations/{location_id}/glossaries/{glos
   * sary_id}/categories/{category_id}
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Output only. System generated unique id for the GlossaryTerm. This ID will
   * be different if the GlossaryTerm is deleted and re-created with the same
   * name.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time at which the GlossaryTerm was last updated.
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
class_alias(GoogleCloudDataplexV1GlossaryTerm::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1GlossaryTerm');
