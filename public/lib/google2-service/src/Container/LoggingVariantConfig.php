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

namespace Google\Service\Container;

class LoggingVariantConfig extends \Google\Model
{
  /**
   * Default value. This shouldn't be used.
   */
  public const VARIANT_VARIANT_UNSPECIFIED = 'VARIANT_UNSPECIFIED';
  /**
   * default logging variant.
   */
  public const VARIANT_DEFAULT = 'DEFAULT';
  /**
   * maximum logging throughput variant.
   */
  public const VARIANT_MAX_THROUGHPUT = 'MAX_THROUGHPUT';
  /**
   * Logging variant deployed on nodes.
   *
   * @var string
   */
  public $variant;

  /**
   * Logging variant deployed on nodes.
   *
   * Accepted values: VARIANT_UNSPECIFIED, DEFAULT, MAX_THROUGHPUT
   *
   * @param self::VARIANT_* $variant
   */
  public function setVariant($variant)
  {
    $this->variant = $variant;
  }
  /**
   * @return self::VARIANT_*
   */
  public function getVariant()
  {
    return $this->variant;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoggingVariantConfig::class, 'Google_Service_Container_LoggingVariantConfig');
