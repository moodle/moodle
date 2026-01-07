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

namespace Google\Service\FirebaseDynamicLinks;

class Suffix extends \Google\Model
{
  /**
   * The suffix option is not specified, performs as UNGUESSABLE .
   */
  public const OPTION_OPTION_UNSPECIFIED = 'OPTION_UNSPECIFIED';
  /**
   * Short Dynamic Link suffix is a base62 [0-9A-Za-z] encoded string of a
   * random generated 96 bit random number, which has a length of 17 chars. For
   * example, "nlAR8U4SlKRZw1cb2". It prevents other people from guessing and
   * crawling short Dynamic Links that contain personal identifiable
   * information.
   */
  public const OPTION_UNGUESSABLE = 'UNGUESSABLE';
  /**
   * Short Dynamic Link suffix is a base62 [0-9A-Za-z] string starting with a
   * length of 4 chars. the length will increase when all the space is occupied.
   */
  public const OPTION_SHORT = 'SHORT';
  /**
   * Custom DDL suffix is a client specified string, for example,
   * "buy2get1free". NOTE: custom suffix should only be available to managed
   * short link creation
   */
  public const OPTION_CUSTOM = 'CUSTOM';
  /**
   * Only applies to Option.CUSTOM.
   *
   * @var string
   */
  public $customSuffix;
  /**
   * Suffix option.
   *
   * @var string
   */
  public $option;

  /**
   * Only applies to Option.CUSTOM.
   *
   * @param string $customSuffix
   */
  public function setCustomSuffix($customSuffix)
  {
    $this->customSuffix = $customSuffix;
  }
  /**
   * @return string
   */
  public function getCustomSuffix()
  {
    return $this->customSuffix;
  }
  /**
   * Suffix option.
   *
   * Accepted values: OPTION_UNSPECIFIED, UNGUESSABLE, SHORT, CUSTOM
   *
   * @param self::OPTION_* $option
   */
  public function setOption($option)
  {
    $this->option = $option;
  }
  /**
   * @return self::OPTION_*
   */
  public function getOption()
  {
    return $this->option;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Suffix::class, 'Google_Service_FirebaseDynamicLinks_Suffix');
