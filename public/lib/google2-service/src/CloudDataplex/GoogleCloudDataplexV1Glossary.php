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

class GoogleCloudDataplexV1Glossary extends \Google\Model
{
  /**
   * Output only. The number of GlossaryCategories in the Glossary.
   *
   * @var int
   */
  public $categoryCount;
  /**
   * Output only. The time at which the Glossary was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The user-mutable description of the Glossary.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name of the Glossary. This is user-mutable.
   * This will be same as the GlossaryId, if not specified.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Needed for resource freshness validation. This checksum is
   * computed by the server based on the value of other fields, and may be sent
   * on update and delete requests to ensure the client has an up-to-date value
   * before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels for the Glossary.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The resource name of the Glossary. Format: project
   * s/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_id}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The number of GlossaryTerms in the Glossary.
   *
   * @var int
   */
  public $termCount;
  /**
   * Output only. System generated unique id for the Glossary. This ID will be
   * different if the Glossary is deleted and re-created with the same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which the Glossary was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The number of GlossaryCategories in the Glossary.
   *
   * @param int $categoryCount
   */
  public function setCategoryCount($categoryCount)
  {
    $this->categoryCount = $categoryCount;
  }
  /**
   * @return int
   */
  public function getCategoryCount()
  {
    return $this->categoryCount;
  }
  /**
   * Output only. The time at which the Glossary was created.
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
   * Optional. The user-mutable description of the Glossary.
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
   * Optional. User friendly display name of the Glossary. This is user-mutable.
   * This will be same as the GlossaryId, if not specified.
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
   * Optional. Needed for resource freshness validation. This checksum is
   * computed by the server based on the value of other fields, and may be sent
   * on update and delete requests to ensure the client has an up-to-date value
   * before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. User-defined labels for the Glossary.
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
   * Output only. Identifier. The resource name of the Glossary. Format: project
   * s/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_id}
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
   * Output only. The number of GlossaryTerms in the Glossary.
   *
   * @param int $termCount
   */
  public function setTermCount($termCount)
  {
    $this->termCount = $termCount;
  }
  /**
   * @return int
   */
  public function getTermCount()
  {
    return $this->termCount;
  }
  /**
   * Output only. System generated unique id for the Glossary. This ID will be
   * different if the Glossary is deleted and re-created with the same name.
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
   * Output only. The time at which the Glossary was last updated.
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
class_alias(GoogleCloudDataplexV1Glossary::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Glossary');
