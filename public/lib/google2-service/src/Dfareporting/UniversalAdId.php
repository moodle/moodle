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

namespace Google\Service\Dfareporting;

class UniversalAdId extends \Google\Model
{
  public const REGISTRY_OTHER = 'OTHER';
  public const REGISTRY_AD_ID_OFFICIAL = 'AD_ID_OFFICIAL';
  public const REGISTRY_CLEARCAST = 'CLEARCAST';
  public const REGISTRY_DCM = 'DCM';
  public const REGISTRY_ARPP = 'ARPP';
  public const REGISTRY_CUSV = 'CUSV';
  /**
   * Registry used for the Ad ID value.
   *
   * @var string
   */
  public $registry;
  /**
   * ID value for this creative. Only alphanumeric characters and the following
   * symbols are valid: "_/\-". Maximum length is 64 characters. Read only when
   * registry is DCM.
   *
   * @var string
   */
  public $value;

  /**
   * Registry used for the Ad ID value.
   *
   * Accepted values: OTHER, AD_ID_OFFICIAL, CLEARCAST, DCM, ARPP, CUSV
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
  /**
   * ID value for this creative. Only alphanumeric characters and the following
   * symbols are valid: "_/\-". Maximum length is 64 characters. Read only when
   * registry is DCM.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UniversalAdId::class, 'Google_Service_Dfareporting_UniversalAdId');
