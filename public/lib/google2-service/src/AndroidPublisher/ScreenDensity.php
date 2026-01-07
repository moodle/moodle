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

class ScreenDensity extends \Google\Model
{
  /**
   * Unspecified screen density.
   */
  public const DENSITY_ALIAS_DENSITY_UNSPECIFIED = 'DENSITY_UNSPECIFIED';
  /**
   * NODPI screen density.
   */
  public const DENSITY_ALIAS_NODPI = 'NODPI';
  /**
   * LDPI screen density.
   */
  public const DENSITY_ALIAS_LDPI = 'LDPI';
  /**
   * MDPI screen density.
   */
  public const DENSITY_ALIAS_MDPI = 'MDPI';
  /**
   * TVDPI screen density.
   */
  public const DENSITY_ALIAS_TVDPI = 'TVDPI';
  /**
   * HDPI screen density.
   */
  public const DENSITY_ALIAS_HDPI = 'HDPI';
  /**
   * XHDPI screen density.
   */
  public const DENSITY_ALIAS_XHDPI = 'XHDPI';
  /**
   * XXHDPI screen density.
   */
  public const DENSITY_ALIAS_XXHDPI = 'XXHDPI';
  /**
   * XXXHDPI screen density.
   */
  public const DENSITY_ALIAS_XXXHDPI = 'XXXHDPI';
  /**
   * Alias for a screen density.
   *
   * @var string
   */
  public $densityAlias;
  /**
   * Value for density dpi.
   *
   * @var int
   */
  public $densityDpi;

  /**
   * Alias for a screen density.
   *
   * Accepted values: DENSITY_UNSPECIFIED, NODPI, LDPI, MDPI, TVDPI, HDPI,
   * XHDPI, XXHDPI, XXXHDPI
   *
   * @param self::DENSITY_ALIAS_* $densityAlias
   */
  public function setDensityAlias($densityAlias)
  {
    $this->densityAlias = $densityAlias;
  }
  /**
   * @return self::DENSITY_ALIAS_*
   */
  public function getDensityAlias()
  {
    return $this->densityAlias;
  }
  /**
   * Value for density dpi.
   *
   * @param int $densityDpi
   */
  public function setDensityDpi($densityDpi)
  {
    $this->densityDpi = $densityDpi;
  }
  /**
   * @return int
   */
  public function getDensityDpi()
  {
    return $this->densityDpi;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScreenDensity::class, 'Google_Service_AndroidPublisher_ScreenDensity');
