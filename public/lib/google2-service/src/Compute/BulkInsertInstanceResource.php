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

namespace Google\Service\Compute;

class BulkInsertInstanceResource extends \Google\Model
{
  /**
   * The maximum number of instances to create.
   *
   * @var string
   */
  public $count;
  protected $instancePropertiesType = InstanceProperties::class;
  protected $instancePropertiesDataType = '';
  protected $locationPolicyType = LocationPolicy::class;
  protected $locationPolicyDataType = '';
  /**
   * The minimum number of instances to create. If no min_count is specified
   * then count is used as the default value. Ifmin_count instances cannot be
   * created, then no instances will be created and instances already created
   * will be deleted.
   *
   * @var string
   */
  public $minCount;
  /**
   * The string pattern used for the names of the VMs. Either name_pattern or
   * per_instance_properties must be set. The pattern must contain one
   * continuous sequence of placeholder hash characters (#) with each character
   * corresponding to one digit of the generated instance name. Example: a
   * name_pattern of inst-#### generates instance names such asinst-0001 and
   * inst-0002. If existing instances in the same project and zone have names
   * that match the name pattern then the generated instance numbers start after
   * the biggest existing number. For example, if there exists an instance with
   * nameinst-0050, then instance names generated using the patterninst-####
   * begin with inst-0051. The name pattern placeholder #...# can contain up to
   * 18 characters.
   *
   * @var string
   */
  public $namePattern;
  protected $perInstancePropertiesType = BulkInsertInstanceResourcePerInstanceProperties::class;
  protected $perInstancePropertiesDataType = 'map';
  /**
   * Specifies the instance template from which to create instances. You may
   * combine sourceInstanceTemplate withinstanceProperties to override specific
   * values from an existing instance template. Bulk API follows the semantics
   * of JSON Merge Patch described by RFC 7396.
   *
   * It can be a full or partial URL. For example, the following are all valid
   * URLs to an instance template:                - https://www.googleapis.com/c
   * ompute/v1/projects/project/global/instanceTemplates/instanceTemplate
   * - projects/project/global/instanceTemplates/instanceTemplate       -
   * global/instanceTemplates/instanceTemplate
   *
   * This field is optional.
   *
   * @var string
   */
  public $sourceInstanceTemplate;

  /**
   * The maximum number of instances to create.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The instance properties defining the VM instances to be created. Required
   * if sourceInstanceTemplate is not provided.
   *
   * @param InstanceProperties $instanceProperties
   */
  public function setInstanceProperties(InstanceProperties $instanceProperties)
  {
    $this->instanceProperties = $instanceProperties;
  }
  /**
   * @return InstanceProperties
   */
  public function getInstanceProperties()
  {
    return $this->instanceProperties;
  }
  /**
   * Policy for choosing target zone. For more information, seeCreate VMs in
   * bulk.
   *
   * @param LocationPolicy $locationPolicy
   */
  public function setLocationPolicy(LocationPolicy $locationPolicy)
  {
    $this->locationPolicy = $locationPolicy;
  }
  /**
   * @return LocationPolicy
   */
  public function getLocationPolicy()
  {
    return $this->locationPolicy;
  }
  /**
   * The minimum number of instances to create. If no min_count is specified
   * then count is used as the default value. Ifmin_count instances cannot be
   * created, then no instances will be created and instances already created
   * will be deleted.
   *
   * @param string $minCount
   */
  public function setMinCount($minCount)
  {
    $this->minCount = $minCount;
  }
  /**
   * @return string
   */
  public function getMinCount()
  {
    return $this->minCount;
  }
  /**
   * The string pattern used for the names of the VMs. Either name_pattern or
   * per_instance_properties must be set. The pattern must contain one
   * continuous sequence of placeholder hash characters (#) with each character
   * corresponding to one digit of the generated instance name. Example: a
   * name_pattern of inst-#### generates instance names such asinst-0001 and
   * inst-0002. If existing instances in the same project and zone have names
   * that match the name pattern then the generated instance numbers start after
   * the biggest existing number. For example, if there exists an instance with
   * nameinst-0050, then instance names generated using the patterninst-####
   * begin with inst-0051. The name pattern placeholder #...# can contain up to
   * 18 characters.
   *
   * @param string $namePattern
   */
  public function setNamePattern($namePattern)
  {
    $this->namePattern = $namePattern;
  }
  /**
   * @return string
   */
  public function getNamePattern()
  {
    return $this->namePattern;
  }
  /**
   * Per-instance properties to be set on individual instances. Keys of this map
   * specify requested instance names. Can be empty if name_pattern is used.
   *
   * @param BulkInsertInstanceResourcePerInstanceProperties[] $perInstanceProperties
   */
  public function setPerInstanceProperties($perInstanceProperties)
  {
    $this->perInstanceProperties = $perInstanceProperties;
  }
  /**
   * @return BulkInsertInstanceResourcePerInstanceProperties[]
   */
  public function getPerInstanceProperties()
  {
    return $this->perInstanceProperties;
  }
  /**
   * Specifies the instance template from which to create instances. You may
   * combine sourceInstanceTemplate withinstanceProperties to override specific
   * values from an existing instance template. Bulk API follows the semantics
   * of JSON Merge Patch described by RFC 7396.
   *
   * It can be a full or partial URL. For example, the following are all valid
   * URLs to an instance template:                - https://www.googleapis.com/c
   * ompute/v1/projects/project/global/instanceTemplates/instanceTemplate
   * - projects/project/global/instanceTemplates/instanceTemplate       -
   * global/instanceTemplates/instanceTemplate
   *
   * This field is optional.
   *
   * @param string $sourceInstanceTemplate
   */
  public function setSourceInstanceTemplate($sourceInstanceTemplate)
  {
    $this->sourceInstanceTemplate = $sourceInstanceTemplate;
  }
  /**
   * @return string
   */
  public function getSourceInstanceTemplate()
  {
    return $this->sourceInstanceTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkInsertInstanceResource::class, 'Google_Service_Compute_BulkInsertInstanceResource');
