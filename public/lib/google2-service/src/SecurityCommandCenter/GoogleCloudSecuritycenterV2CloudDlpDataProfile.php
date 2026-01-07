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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2CloudDlpDataProfile extends \Google\Collection
{
  /**
   * Unspecified parent type.
   */
  public const PARENT_TYPE_PARENT_TYPE_UNSPECIFIED = 'PARENT_TYPE_UNSPECIFIED';
  /**
   * Organization-level configurations.
   */
  public const PARENT_TYPE_ORGANIZATION = 'ORGANIZATION';
  /**
   * Project-level configurations.
   */
  public const PARENT_TYPE_PROJECT = 'PROJECT';
  protected $collection_key = 'infoTypes';
  /**
   * Name of the data profile, for example,
   * `projects/123/locations/europe/tableProfiles/8383929`.
   *
   * @var string
   */
  public $dataProfile;
  protected $infoTypesType = GoogleCloudSecuritycenterV2InfoType::class;
  protected $infoTypesDataType = 'array';
  /**
   * The resource hierarchy level at which the data profile was generated.
   *
   * @var string
   */
  public $parentType;

  /**
   * Name of the data profile, for example,
   * `projects/123/locations/europe/tableProfiles/8383929`.
   *
   * @param string $dataProfile
   */
  public function setDataProfile($dataProfile)
  {
    $this->dataProfile = $dataProfile;
  }
  /**
   * @return string
   */
  public function getDataProfile()
  {
    return $this->dataProfile;
  }
  /**
   * Type of information detected by SDP. Info type includes name, version and
   * sensitivity of the detected information type.
   *
   * @param GoogleCloudSecuritycenterV2InfoType[] $infoTypes
   */
  public function setInfoTypes($infoTypes)
  {
    $this->infoTypes = $infoTypes;
  }
  /**
   * @return GoogleCloudSecuritycenterV2InfoType[]
   */
  public function getInfoTypes()
  {
    return $this->infoTypes;
  }
  /**
   * The resource hierarchy level at which the data profile was generated.
   *
   * Accepted values: PARENT_TYPE_UNSPECIFIED, ORGANIZATION, PROJECT
   *
   * @param self::PARENT_TYPE_* $parentType
   */
  public function setParentType($parentType)
  {
    $this->parentType = $parentType;
  }
  /**
   * @return self::PARENT_TYPE_*
   */
  public function getParentType()
  {
    return $this->parentType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2CloudDlpDataProfile::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2CloudDlpDataProfile');
