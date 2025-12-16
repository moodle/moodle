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

namespace Google\Service\DisplayVideo;

class UniversalAdId extends \Google\Model
{
  /**
   * The Universal Ad registry is unspecified or is unknown in this version.
   */
  public const REGISTRY_UNIVERSAL_AD_REGISTRY_UNSPECIFIED = 'UNIVERSAL_AD_REGISTRY_UNSPECIFIED';
  /**
   * Use a custom provider to provide the Universal Ad ID.
   */
  public const REGISTRY_UNIVERSAL_AD_REGISTRY_OTHER = 'UNIVERSAL_AD_REGISTRY_OTHER';
  /**
   * Use Ad-ID to provide the Universal Ad ID.
   */
  public const REGISTRY_UNIVERSAL_AD_REGISTRY_AD_ID = 'UNIVERSAL_AD_REGISTRY_AD_ID';
  /**
   * Use clearcast.co.uk to provide the Universal Ad ID.
   */
  public const REGISTRY_UNIVERSAL_AD_REGISTRY_CLEARCAST = 'UNIVERSAL_AD_REGISTRY_CLEARCAST';
  /**
   * Use Display & Video 360 to provide the Universal Ad ID.
   */
  public const REGISTRY_UNIVERSAL_AD_REGISTRY_DV360 = 'UNIVERSAL_AD_REGISTRY_DV360';
  /**
   * Use Campaign Manager 360 to provide the Universal Ad ID.
   */
  public const REGISTRY_UNIVERSAL_AD_REGISTRY_CM = 'UNIVERSAL_AD_REGISTRY_CM';
  /**
   * Optional. The unique creative identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. The registry provides unique creative identifiers.
   *
   * @var string
   */
  public $registry;

  /**
   * Optional. The unique creative identifier.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. The registry provides unique creative identifiers.
   *
   * Accepted values: UNIVERSAL_AD_REGISTRY_UNSPECIFIED,
   * UNIVERSAL_AD_REGISTRY_OTHER, UNIVERSAL_AD_REGISTRY_AD_ID,
   * UNIVERSAL_AD_REGISTRY_CLEARCAST, UNIVERSAL_AD_REGISTRY_DV360,
   * UNIVERSAL_AD_REGISTRY_CM
   *
   * @param self::REGISTRY_* $registry
   */
  public function setRegistry($registry)
  {
    $this->registry = $registry;
  }
  /**
   * @return self::REGISTRY_*
   */
  public function getRegistry()
  {
    return $this->registry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UniversalAdId::class, 'Google_Service_DisplayVideo_UniversalAdId');
