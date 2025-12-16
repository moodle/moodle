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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Alias extends \Google\Model
{
  /**
   * Alias type is not specified.
   */
  public const TYPE_ALIAS_TYPE_UNSPECIFIED = 'ALIAS_TYPE_UNSPECIFIED';
  /**
   * Certificate.
   */
  public const TYPE_CERT = 'CERT';
  /**
   * Key/certificate pair.
   */
  public const TYPE_KEY_CERT = 'KEY_CERT';
  /**
   * Resource ID for this alias. Values must match the regular expression
   * `[^/]{1,255}`.
   *
   * @var string
   */
  public $alias;
  protected $certsInfoType = GoogleCloudApigeeV1Certificate::class;
  protected $certsInfoDataType = '';
  /**
   * Type of alias.
   *
   * @var string
   */
  public $type;

  /**
   * Resource ID for this alias. Values must match the regular expression
   * `[^/]{1,255}`.
   *
   * @param string $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return string
   */
  public function getAlias()
  {
    return $this->alias;
  }
  /**
   * Chain of certificates under this alias.
   *
   * @param GoogleCloudApigeeV1Certificate $certsInfo
   */
  public function setCertsInfo(GoogleCloudApigeeV1Certificate $certsInfo)
  {
    $this->certsInfo = $certsInfo;
  }
  /**
   * @return GoogleCloudApigeeV1Certificate
   */
  public function getCertsInfo()
  {
    return $this->certsInfo;
  }
  /**
   * Type of alias.
   *
   * Accepted values: ALIAS_TYPE_UNSPECIFIED, CERT, KEY_CERT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Alias::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Alias');
