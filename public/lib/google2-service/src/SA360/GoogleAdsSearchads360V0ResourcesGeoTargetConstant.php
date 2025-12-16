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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesGeoTargetConstant extends \Google\Model
{
  /**
   * No value has been specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The geo target constant is valid.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The geo target constant is obsolete and will be removed.
   */
  public const STATUS_REMOVAL_PLANNED = 'REMOVAL_PLANNED';
  /**
   * Output only. The fully qualified English name, consisting of the target's
   * name and that of its parent and country.
   *
   * @var string
   */
  public $canonicalName;
  /**
   * Output only. The ISO-3166-1 alpha-2 country code that is associated with
   * the target.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Output only. The ID of the geo target constant.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Geo target constant English name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the parent geo target constant. Geo
   * target constant resource names have the form:
   * `geoTargetConstants/{parent_geo_target_constant_id}`
   *
   * @var string
   */
  public $parentGeoTarget;
  /**
   * Output only. The resource name of the geo target constant. Geo target
   * constant resource names have the form:
   * `geoTargetConstants/{geo_target_constant_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. Geo target constant status.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. Geo target constant target type.
   *
   * @var string
   */
  public $targetType;

  /**
   * Output only. The fully qualified English name, consisting of the target's
   * name and that of its parent and country.
   *
   * @param string $canonicalName
   */
  public function setCanonicalName($canonicalName)
  {
    $this->canonicalName = $canonicalName;
  }
  /**
   * @return string
   */
  public function getCanonicalName()
  {
    return $this->canonicalName;
  }
  /**
   * Output only. The ISO-3166-1 alpha-2 country code that is associated with
   * the target.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Output only. The ID of the geo target constant.
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
   * Output only. Geo target constant English name.
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
   * Output only. The resource name of the parent geo target constant. Geo
   * target constant resource names have the form:
   * `geoTargetConstants/{parent_geo_target_constant_id}`
   *
   * @param string $parentGeoTarget
   */
  public function setParentGeoTarget($parentGeoTarget)
  {
    $this->parentGeoTarget = $parentGeoTarget;
  }
  /**
   * @return string
   */
  public function getParentGeoTarget()
  {
    return $this->parentGeoTarget;
  }
  /**
   * Output only. The resource name of the geo target constant. Geo target
   * constant resource names have the form:
   * `geoTargetConstants/{geo_target_constant_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. Geo target constant status.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVAL_PLANNED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Geo target constant target type.
   *
   * @param string $targetType
   */
  public function setTargetType($targetType)
  {
    $this->targetType = $targetType;
  }
  /**
   * @return string
   */
  public function getTargetType()
  {
    return $this->targetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesGeoTargetConstant::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesGeoTargetConstant');
