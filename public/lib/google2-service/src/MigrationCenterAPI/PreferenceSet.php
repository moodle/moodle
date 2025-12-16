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

namespace Google\Service\MigrationCenterAPI;

class PreferenceSet extends \Google\Model
{
  /**
   * Output only. The timestamp when the preference set was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of the preference set.
   *
   * @var string
   */
  public $description;
  /**
   * User-friendly display name. Maximum length is 63 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Name of the preference set.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp when the preference set was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $virtualMachinePreferencesType = VirtualMachinePreferences::class;
  protected $virtualMachinePreferencesDataType = '';

  /**
   * Output only. The timestamp when the preference set was created.
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
   * A description of the preference set.
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
   * User-friendly display name. Maximum length is 63 characters.
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
   * Output only. Name of the preference set.
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
   * Output only. The timestamp when the preference set was last updated.
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
  /**
   * Optional. A set of preferences that applies to all virtual machines in the
   * context.
   *
   * @param VirtualMachinePreferences $virtualMachinePreferences
   */
  public function setVirtualMachinePreferences(VirtualMachinePreferences $virtualMachinePreferences)
  {
    $this->virtualMachinePreferences = $virtualMachinePreferences;
  }
  /**
   * @return VirtualMachinePreferences
   */
  public function getVirtualMachinePreferences()
  {
    return $this->virtualMachinePreferences;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreferenceSet::class, 'Google_Service_MigrationCenterAPI_PreferenceSet');
