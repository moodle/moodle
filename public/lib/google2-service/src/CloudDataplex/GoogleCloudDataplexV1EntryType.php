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

class GoogleCloudDataplexV1EntryType extends \Google\Collection
{
  protected $collection_key = 'typeAliases';
  protected $authorizationType = GoogleCloudDataplexV1EntryTypeAuthorization::class;
  protected $authorizationDataType = '';
  /**
   * Output only. The time when the EntryType was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the EntryType.
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
   * Optional. This checksum is computed by the service, and might be sent on
   * update and delete requests to ensure the client has an up-to-date value
   * before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels for the EntryType.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the EntryType, of the form: proj
   * ects/{project_number}/locations/{location_id}/entryTypes/{entry_type_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The platform that Entries of this type belongs to.
   *
   * @var string
   */
  public $platform;
  protected $requiredAspectsType = GoogleCloudDataplexV1EntryTypeAspectInfo::class;
  protected $requiredAspectsDataType = 'array';
  /**
   * Optional. The system that Entries of this type belongs to. Examples include
   * CloudSQL, MariaDB etc
   *
   * @var string
   */
  public $system;
  /**
   * Optional. Indicates the classes this Entry Type belongs to, for example,
   * TABLE, DATABASE, MODEL.
   *
   * @var string[]
   */
  public $typeAliases;
  /**
   * Output only. System generated globally unique ID for the EntryType. This ID
   * will be different if the EntryType is deleted and re-created with the same
   * name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the EntryType was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Immutable. Authorization defined for this type.
   *
   * @param GoogleCloudDataplexV1EntryTypeAuthorization $authorization
   */
  public function setAuthorization(GoogleCloudDataplexV1EntryTypeAuthorization $authorization)
  {
    $this->authorization = $authorization;
  }
  /**
   * @return GoogleCloudDataplexV1EntryTypeAuthorization
   */
  public function getAuthorization()
  {
    return $this->authorization;
  }
  /**
   * Output only. The time when the EntryType was created.
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
   * Optional. Description of the EntryType.
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
   * Optional. This checksum is computed by the service, and might be sent on
   * update and delete requests to ensure the client has an up-to-date value
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
   * Optional. User-defined labels for the EntryType.
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
   * Output only. The relative resource name of the EntryType, of the form: proj
   * ects/{project_number}/locations/{location_id}/entryTypes/{entry_type_id}.
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
   * Optional. The platform that Entries of this type belongs to.
   *
   * @param string $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return string
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * AspectInfo for the entry type.
   *
   * @param GoogleCloudDataplexV1EntryTypeAspectInfo[] $requiredAspects
   */
  public function setRequiredAspects($requiredAspects)
  {
    $this->requiredAspects = $requiredAspects;
  }
  /**
   * @return GoogleCloudDataplexV1EntryTypeAspectInfo[]
   */
  public function getRequiredAspects()
  {
    return $this->requiredAspects;
  }
  /**
   * Optional. The system that Entries of this type belongs to. Examples include
   * CloudSQL, MariaDB etc
   *
   * @param string $system
   */
  public function setSystem($system)
  {
    $this->system = $system;
  }
  /**
   * @return string
   */
  public function getSystem()
  {
    return $this->system;
  }
  /**
   * Optional. Indicates the classes this Entry Type belongs to, for example,
   * TABLE, DATABASE, MODEL.
   *
   * @param string[] $typeAliases
   */
  public function setTypeAliases($typeAliases)
  {
    $this->typeAliases = $typeAliases;
  }
  /**
   * @return string[]
   */
  public function getTypeAliases()
  {
    return $this->typeAliases;
  }
  /**
   * Output only. System generated globally unique ID for the EntryType. This ID
   * will be different if the EntryType is deleted and re-created with the same
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
   * Output only. The time when the EntryType was last updated.
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
class_alias(GoogleCloudDataplexV1EntryType::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EntryType');
