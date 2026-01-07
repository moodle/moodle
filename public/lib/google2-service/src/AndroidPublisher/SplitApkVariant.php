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

class SplitApkVariant extends \Google\Collection
{
  protected $collection_key = 'apkSet';
  protected $apkSetType = ApkSet::class;
  protected $apkSetDataType = 'array';
  protected $targetingType = VariantTargeting::class;
  protected $targetingDataType = '';
  /**
   * Number of the variant, starting at 0 (unless overridden). A device will
   * receive APKs from the first variant that matches the device configuration,
   * with higher variant numbers having priority over lower variant numbers.
   *
   * @var int
   */
  public $variantNumber;

  /**
   * Set of APKs, one set per module.
   *
   * @param ApkSet[] $apkSet
   */
  public function setApkSet($apkSet)
  {
    $this->apkSet = $apkSet;
  }
  /**
   * @return ApkSet[]
   */
  public function getApkSet()
  {
    return $this->apkSet;
  }
  /**
   * Variant-level targeting.
   *
   * @param VariantTargeting $targeting
   */
  public function setTargeting(VariantTargeting $targeting)
  {
    $this->targeting = $targeting;
  }
  /**
   * @return VariantTargeting
   */
  public function getTargeting()
  {
    return $this->targeting;
  }
  /**
   * Number of the variant, starting at 0 (unless overridden). A device will
   * receive APKs from the first variant that matches the device configuration,
   * with higher variant numbers having priority over lower variant numbers.
   *
   * @param int $variantNumber
   */
  public function setVariantNumber($variantNumber)
  {
    $this->variantNumber = $variantNumber;
  }
  /**
   * @return int
   */
  public function getVariantNumber()
  {
    return $this->variantNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SplitApkVariant::class, 'Google_Service_AndroidPublisher_SplitApkVariant');
