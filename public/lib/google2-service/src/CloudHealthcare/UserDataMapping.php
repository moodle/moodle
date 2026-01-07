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

namespace Google\Service\CloudHealthcare;

class UserDataMapping extends \Google\Collection
{
  protected $collection_key = 'resourceAttributes';
  /**
   * Output only. Indicates the time when this mapping was archived.
   *
   * @var string
   */
  public $archiveTime;
  /**
   * Output only. Indicates whether this mapping is archived.
   *
   * @var bool
   */
  public $archived;
  /**
   * Required. A unique identifier for the mapped resource.
   *
   * @var string
   */
  public $dataId;
  /**
   * Resource name of the User data mapping, of the form `projects/{project_id}/
   * locations/{location_id}/datasets/{dataset_id}/consentStores/{consent_store_
   * id}/userDataMappings/{user_data_mapping_id}`.
   *
   * @var string
   */
  public $name;
  protected $resourceAttributesType = Attribute::class;
  protected $resourceAttributesDataType = 'array';
  /**
   * Required. User's UUID provided by the client.
   *
   * @var string
   */
  public $userId;

  /**
   * Output only. Indicates the time when this mapping was archived.
   *
   * @param string $archiveTime
   */
  public function setArchiveTime($archiveTime)
  {
    $this->archiveTime = $archiveTime;
  }
  /**
   * @return string
   */
  public function getArchiveTime()
  {
    return $this->archiveTime;
  }
  /**
   * Output only. Indicates whether this mapping is archived.
   *
   * @param bool $archived
   */
  public function setArchived($archived)
  {
    $this->archived = $archived;
  }
  /**
   * @return bool
   */
  public function getArchived()
  {
    return $this->archived;
  }
  /**
   * Required. A unique identifier for the mapped resource.
   *
   * @param string $dataId
   */
  public function setDataId($dataId)
  {
    $this->dataId = $dataId;
  }
  /**
   * @return string
   */
  public function getDataId()
  {
    return $this->dataId;
  }
  /**
   * Resource name of the User data mapping, of the form `projects/{project_id}/
   * locations/{location_id}/datasets/{dataset_id}/consentStores/{consent_store_
   * id}/userDataMappings/{user_data_mapping_id}`.
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
   * Attributes of the resource. Only explicitly set attributes are displayed
   * here. Attribute definitions with defaults set implicitly apply to these
   * User data mappings. Attributes listed here must be single valued, that is,
   * exactly one value is specified for the field "values" in each Attribute.
   *
   * @param Attribute[] $resourceAttributes
   */
  public function setResourceAttributes($resourceAttributes)
  {
    $this->resourceAttributes = $resourceAttributes;
  }
  /**
   * @return Attribute[]
   */
  public function getResourceAttributes()
  {
    return $this->resourceAttributes;
  }
  /**
   * Required. User's UUID provided by the client.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserDataMapping::class, 'Google_Service_CloudHealthcare_UserDataMapping');
