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

namespace Google\Service\AndroidPublisher;

class ModuleMetadata extends \Google\Collection
{
  /**
   * Unspecified delivery type.
   */
  public const DELIVERY_TYPE_UNKNOWN_DELIVERY_TYPE = 'UNKNOWN_DELIVERY_TYPE';
  /**
   * This module will always be downloaded as part of the initial install of the
   * app.
   */
  public const DELIVERY_TYPE_INSTALL_TIME = 'INSTALL_TIME';
  /**
   * This module is requested on-demand, which means it will not be part of the
   * initial install, and will only be sent when requested by the client.
   */
  public const DELIVERY_TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * This module will be downloaded immediately after initial install finishes.
   * The app can be opened before these modules are downloaded.
   */
  public const DELIVERY_TYPE_FAST_FOLLOW = 'FAST_FOLLOW';
  /**
   * Unknown feature module.
   */
  public const MODULE_TYPE_UNKNOWN_MODULE_TYPE = 'UNKNOWN_MODULE_TYPE';
  /**
   * Regular feature module.
   */
  public const MODULE_TYPE_FEATURE_MODULE = 'FEATURE_MODULE';
  protected $collection_key = 'dependencies';
  /**
   * Indicates the delivery type (e.g. on-demand) of the module.
   *
   * @var string
   */
  public $deliveryType;
  /**
   * Names of the modules that this module directly depends on. Each module
   * implicitly depends on the base module.
   *
   * @var string[]
   */
  public $dependencies;
  /**
   * Indicates the type of this feature module.
   *
   * @var string
   */
  public $moduleType;
  /**
   * Module name.
   *
   * @var string
   */
  public $name;
  protected $targetingType = ModuleTargeting::class;
  protected $targetingDataType = '';

  /**
   * Indicates the delivery type (e.g. on-demand) of the module.
   *
   * Accepted values: UNKNOWN_DELIVERY_TYPE, INSTALL_TIME, ON_DEMAND,
   * FAST_FOLLOW
   *
   * @param self::DELIVERY_TYPE_* $deliveryType
   */
  public function setDeliveryType($deliveryType)
  {
    $this->deliveryType = $deliveryType;
  }
  /**
   * @return self::DELIVERY_TYPE_*
   */
  public function getDeliveryType()
  {
    return $this->deliveryType;
  }
  /**
   * Names of the modules that this module directly depends on. Each module
   * implicitly depends on the base module.
   *
   * @param string[] $dependencies
   */
  public function setDependencies($dependencies)
  {
    $this->dependencies = $dependencies;
  }
  /**
   * @return string[]
   */
  public function getDependencies()
  {
    return $this->dependencies;
  }
  /**
   * Indicates the type of this feature module.
   *
   * Accepted values: UNKNOWN_MODULE_TYPE, FEATURE_MODULE
   *
   * @param self::MODULE_TYPE_* $moduleType
   */
  public function setModuleType($moduleType)
  {
    $this->moduleType = $moduleType;
  }
  /**
   * @return self::MODULE_TYPE_*
   */
  public function getModuleType()
  {
    return $this->moduleType;
  }
  /**
   * Module name.
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
   * The targeting that makes a conditional module installed. Relevant only for
   * Split APKs.
   *
   * @param ModuleTargeting $targeting
   */
  public function setTargeting(ModuleTargeting $targeting)
  {
    $this->targeting = $targeting;
  }
  /**
   * @return ModuleTargeting
   */
  public function getTargeting()
  {
    return $this->targeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModuleMetadata::class, 'Google_Service_AndroidPublisher_ModuleMetadata');
