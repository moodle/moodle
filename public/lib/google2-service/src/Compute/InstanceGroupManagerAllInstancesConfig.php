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

class InstanceGroupManagerAllInstancesConfig extends \Google\Model
{
  protected $propertiesType = InstancePropertiesPatch::class;
  protected $propertiesDataType = '';

  /**
   * Properties to set on all instances in the group.
   *
   * You can add or modify properties using theinstanceGroupManagers.patch
   * orregionInstanceGroupManagers.patch. After settingallInstancesConfig on the
   * group, you must update the group's instances to apply the configuration. To
   * apply the configuration, set the group's updatePolicy.type field to use
   * proactive updates or use the applyUpdatesToInstances method.
   *
   * @param InstancePropertiesPatch $properties
   */
  public function setProperties(InstancePropertiesPatch $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return InstancePropertiesPatch
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerAllInstancesConfig::class, 'Google_Service_Compute_InstanceGroupManagerAllInstancesConfig');
