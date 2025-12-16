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

namespace Google\Service\Config;

class ResourceChange extends \Google\Collection
{
  /**
   * The default value.
   */
  public const INTENT_INTENT_UNSPECIFIED = 'INTENT_UNSPECIFIED';
  /**
   * The resource will be created.
   */
  public const INTENT_CREATE = 'CREATE';
  /**
   * The resource will be updated.
   */
  public const INTENT_UPDATE = 'UPDATE';
  /**
   * The resource will be deleted.
   */
  public const INTENT_DELETE = 'DELETE';
  /**
   * The resource will be recreated.
   */
  public const INTENT_RECREATE = 'RECREATE';
  /**
   * The resource will be untouched.
   */
  public const INTENT_UNCHANGED = 'UNCHANGED';
  protected $collection_key = 'propertyChanges';
  /**
   * Output only. The intent of the resource change.
   *
   * @var string
   */
  public $intent;
  /**
   * Identifier. The name of the resource change. Format: 'projects/{project_id}
   * /locations/{location}/previews/{preview}/resourceChanges/{resource_change}'
   * .
   *
   * @var string
   */
  public $name;
  protected $propertyChangesType = PropertyChange::class;
  protected $propertyChangesDataType = 'array';
  protected $terraformInfoType = ResourceChangeTerraformInfo::class;
  protected $terraformInfoDataType = '';

  /**
   * Output only. The intent of the resource change.
   *
   * Accepted values: INTENT_UNSPECIFIED, CREATE, UPDATE, DELETE, RECREATE,
   * UNCHANGED
   *
   * @param self::INTENT_* $intent
   */
  public function setIntent($intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return self::INTENT_*
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Identifier. The name of the resource change. Format: 'projects/{project_id}
   * /locations/{location}/previews/{preview}/resourceChanges/{resource_change}'
   * .
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
   * Output only. The property changes of the resource change.
   *
   * @param PropertyChange[] $propertyChanges
   */
  public function setPropertyChanges($propertyChanges)
  {
    $this->propertyChanges = $propertyChanges;
  }
  /**
   * @return PropertyChange[]
   */
  public function getPropertyChanges()
  {
    return $this->propertyChanges;
  }
  /**
   * Output only. Terraform info of the resource change.
   *
   * @param ResourceChangeTerraformInfo $terraformInfo
   */
  public function setTerraformInfo(ResourceChangeTerraformInfo $terraformInfo)
  {
    $this->terraformInfo = $terraformInfo;
  }
  /**
   * @return ResourceChangeTerraformInfo
   */
  public function getTerraformInfo()
  {
    return $this->terraformInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceChange::class, 'Google_Service_Config_ResourceChange');
