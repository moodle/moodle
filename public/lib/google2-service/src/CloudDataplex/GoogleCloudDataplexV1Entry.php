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

class GoogleCloudDataplexV1Entry extends \Google\Model
{
  protected $aspectsType = GoogleCloudDataplexV1Aspect::class;
  protected $aspectsDataType = 'map';
  /**
   * Output only. The time when the entry was created in Dataplex Universal
   * Catalog.
   *
   * @var string
   */
  public $createTime;
  protected $entrySourceType = GoogleCloudDataplexV1EntrySource::class;
  protected $entrySourceDataType = '';
  /**
   * Required. Immutable. The relative resource name of the entry type that was
   * used to create this entry, in the format projects/{project_id_or_number}/lo
   * cations/{location_id}/entryTypes/{entry_type_id}.
   *
   * @var string
   */
  public $entryType;
  /**
   * Optional. A name for the entry that can be referenced by an external
   * system. For more information, see Fully qualified names
   * (https://cloud.google.com/data-catalog/docs/fully-qualified-names). The
   * maximum size of the field is 4000 characters.
   *
   * @var string
   */
  public $fullyQualifiedName;
  /**
   * Identifier. The relative resource name of the entry, in the format projects
   * /{project_id_or_number}/locations/{location_id}/entryGroups/{entry_group_id
   * }/entries/{entry_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Immutable. The resource name of the parent entry, in the format p
   * rojects/{project_id_or_number}/locations/{location_id}/entryGroups/{entry_g
   * roup_id}/entries/{entry_id}.
   *
   * @var string
   */
  public $parentEntry;
  /**
   * Output only. The time when the entry was last updated in Dataplex Universal
   * Catalog.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The aspects that are attached to the entry. Depending on how the
   * aspect is attached to the entry, the format of the aspect key can be one of
   * the following: If the aspect is attached directly to the entry:
   * {project_id_or_number}.{location_id}.{aspect_type_id} If the aspect is
   * attached to an entry's path:
   * {project_id_or_number}.{location_id}.{aspect_type_id}@{path}
   *
   * @param GoogleCloudDataplexV1Aspect[] $aspects
   */
  public function setAspects($aspects)
  {
    $this->aspects = $aspects;
  }
  /**
   * @return GoogleCloudDataplexV1Aspect[]
   */
  public function getAspects()
  {
    return $this->aspects;
  }
  /**
   * Output only. The time when the entry was created in Dataplex Universal
   * Catalog.
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
   * Optional. Information related to the source system of the data resource
   * that is represented by the entry.
   *
   * @param GoogleCloudDataplexV1EntrySource $entrySource
   */
  public function setEntrySource(GoogleCloudDataplexV1EntrySource $entrySource)
  {
    $this->entrySource = $entrySource;
  }
  /**
   * @return GoogleCloudDataplexV1EntrySource
   */
  public function getEntrySource()
  {
    return $this->entrySource;
  }
  /**
   * Required. Immutable. The relative resource name of the entry type that was
   * used to create this entry, in the format projects/{project_id_or_number}/lo
   * cations/{location_id}/entryTypes/{entry_type_id}.
   *
   * @param string $entryType
   */
  public function setEntryType($entryType)
  {
    $this->entryType = $entryType;
  }
  /**
   * @return string
   */
  public function getEntryType()
  {
    return $this->entryType;
  }
  /**
   * Optional. A name for the entry that can be referenced by an external
   * system. For more information, see Fully qualified names
   * (https://cloud.google.com/data-catalog/docs/fully-qualified-names). The
   * maximum size of the field is 4000 characters.
   *
   * @param string $fullyQualifiedName
   */
  public function setFullyQualifiedName($fullyQualifiedName)
  {
    $this->fullyQualifiedName = $fullyQualifiedName;
  }
  /**
   * @return string
   */
  public function getFullyQualifiedName()
  {
    return $this->fullyQualifiedName;
  }
  /**
   * Identifier. The relative resource name of the entry, in the format projects
   * /{project_id_or_number}/locations/{location_id}/entryGroups/{entry_group_id
   * }/entries/{entry_id}.
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
   * Optional. Immutable. The resource name of the parent entry, in the format p
   * rojects/{project_id_or_number}/locations/{location_id}/entryGroups/{entry_g
   * roup_id}/entries/{entry_id}.
   *
   * @param string $parentEntry
   */
  public function setParentEntry($parentEntry)
  {
    $this->parentEntry = $parentEntry;
  }
  /**
   * @return string
   */
  public function getParentEntry()
  {
    return $this->parentEntry;
  }
  /**
   * Output only. The time when the entry was last updated in Dataplex Universal
   * Catalog.
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
class_alias(GoogleCloudDataplexV1Entry::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Entry');
