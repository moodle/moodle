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

class GoogleCloudDataplexV1DataTaxonomy extends \Google\Model
{
  /**
   * Output only. The number of attributes in the DataTaxonomy.
   *
   * @var int
   */
  public $attributeCount;
  /**
   * Output only. The number of classes in the DataTaxonomy.
   *
   * @var int
   */
  public $classCount;
  /**
   * Output only. The time when the DataTaxonomy was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the DataTaxonomy.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels for the DataTaxonomy.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the DataTaxonomy, of the form: p
   * rojects/{project_number}/locations/{location_id}/dataTaxonomies/{data_taxon
   * omy_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. System generated globally unique ID for the dataTaxonomy. This
   * ID will be different if the DataTaxonomy is deleted and re-created with the
   * same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the DataTaxonomy was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The number of attributes in the DataTaxonomy.
   *
   * @param int $attributeCount
   */
  public function setAttributeCount($attributeCount)
  {
    $this->attributeCount = $attributeCount;
  }
  /**
   * @return int
   */
  public function getAttributeCount()
  {
    return $this->attributeCount;
  }
  /**
   * Output only. The number of classes in the DataTaxonomy.
   *
   * @param int $classCount
   */
  public function setClassCount($classCount)
  {
    $this->classCount = $classCount;
  }
  /**
   * @return int
   */
  public function getClassCount()
  {
    return $this->classCount;
  }
  /**
   * Output only. The time when the DataTaxonomy was created.
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
   * Optional. Description of the DataTaxonomy.
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
   * Optional. User friendly display name.
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
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
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
   * Optional. User-defined labels for the DataTaxonomy.
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
   * Output only. The relative resource name of the DataTaxonomy, of the form: p
   * rojects/{project_number}/locations/{location_id}/dataTaxonomies/{data_taxon
   * omy_id}.
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
   * Output only. System generated globally unique ID for the dataTaxonomy. This
   * ID will be different if the DataTaxonomy is deleted and re-created with the
   * same name.
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
   * Output only. The time when the DataTaxonomy was last updated.
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
class_alias(GoogleCloudDataplexV1DataTaxonomy::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataTaxonomy');
