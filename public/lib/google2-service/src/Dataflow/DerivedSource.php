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

namespace Google\Service\Dataflow;

class DerivedSource extends \Google\Model
{
  /**
   * The source derivation is unknown, or unspecified.
   */
  public const DERIVATION_MODE_SOURCE_DERIVATION_MODE_UNKNOWN = 'SOURCE_DERIVATION_MODE_UNKNOWN';
  /**
   * Produce a completely independent Source with no base.
   */
  public const DERIVATION_MODE_SOURCE_DERIVATION_MODE_INDEPENDENT = 'SOURCE_DERIVATION_MODE_INDEPENDENT';
  /**
   * Produce a Source based on the Source being split.
   */
  public const DERIVATION_MODE_SOURCE_DERIVATION_MODE_CHILD_OF_CURRENT = 'SOURCE_DERIVATION_MODE_CHILD_OF_CURRENT';
  /**
   * Produce a Source based on the base of the Source being split.
   */
  public const DERIVATION_MODE_SOURCE_DERIVATION_MODE_SIBLING_OF_CURRENT = 'SOURCE_DERIVATION_MODE_SIBLING_OF_CURRENT';
  /**
   * What source to base the produced source on (if any).
   *
   * @var string
   */
  public $derivationMode;
  protected $sourceType = Source::class;
  protected $sourceDataType = '';

  /**
   * What source to base the produced source on (if any).
   *
   * Accepted values: SOURCE_DERIVATION_MODE_UNKNOWN,
   * SOURCE_DERIVATION_MODE_INDEPENDENT,
   * SOURCE_DERIVATION_MODE_CHILD_OF_CURRENT,
   * SOURCE_DERIVATION_MODE_SIBLING_OF_CURRENT
   *
   * @param self::DERIVATION_MODE_* $derivationMode
   */
  public function setDerivationMode($derivationMode)
  {
    $this->derivationMode = $derivationMode;
  }
  /**
   * @return self::DERIVATION_MODE_*
   */
  public function getDerivationMode()
  {
    return $this->derivationMode;
  }
  /**
   * Specification of the source.
   *
   * @param Source $source
   */
  public function setSource(Source $source)
  {
    $this->source = $source;
  }
  /**
   * @return Source
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DerivedSource::class, 'Google_Service_Dataflow_DerivedSource');
