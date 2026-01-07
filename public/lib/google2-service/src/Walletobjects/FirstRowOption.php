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

namespace Google\Service\Walletobjects;

class FirstRowOption extends \Google\Model
{
  public const TRANSIT_OPTION_TRANSIT_OPTION_UNSPECIFIED = 'TRANSIT_OPTION_UNSPECIFIED';
  public const TRANSIT_OPTION_ORIGIN_AND_DESTINATION_NAMES = 'ORIGIN_AND_DESTINATION_NAMES';
  /**
   * Legacy alias for `ORIGIN_AND_DESTINATION_NAMES`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_OPTION_originAndDestinationNames = 'originAndDestinationNames';
  public const TRANSIT_OPTION_ORIGIN_AND_DESTINATION_CODES = 'ORIGIN_AND_DESTINATION_CODES';
  /**
   * Legacy alias for `ORIGIN_AND_DESTINATION_CODES`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_OPTION_originAndDestinationCodes = 'originAndDestinationCodes';
  public const TRANSIT_OPTION_ORIGIN_NAME = 'ORIGIN_NAME';
  /**
   * Legacy alias for `ORIGIN_NAME`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_OPTION_originName = 'originName';
  protected $fieldOptionType = FieldSelector::class;
  protected $fieldOptionDataType = '';
  /**
   * @var string
   */
  public $transitOption;

  /**
   * A reference to the field to be displayed in the first row.
   *
   * @param FieldSelector $fieldOption
   */
  public function setFieldOption(FieldSelector $fieldOption)
  {
    $this->fieldOption = $fieldOption;
  }
  /**
   * @return FieldSelector
   */
  public function getFieldOption()
  {
    return $this->fieldOption;
  }
  /**
   * @param self::TRANSIT_OPTION_* $transitOption
   */
  public function setTransitOption($transitOption)
  {
    $this->transitOption = $transitOption;
  }
  /**
   * @return self::TRANSIT_OPTION_*
   */
  public function getTransitOption()
  {
    return $this->transitOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirstRowOption::class, 'Google_Service_Walletobjects_FirstRowOption');
