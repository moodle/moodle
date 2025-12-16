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

class AssetModuleMetadata extends \Google\Model
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
   * Indicates the delivery type for persistent install.
   *
   * @var string
   */
  public $deliveryType;
  /**
   * Module name.
   *
   * @var string
   */
  public $name;

  /**
   * Indicates the delivery type for persistent install.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetModuleMetadata::class, 'Google_Service_AndroidPublisher_AssetModuleMetadata');
