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

class GoogleCloudApigeeV1KeyValueMap extends \Google\Model
{
  /**
   * Required. Flag that specifies whether entry values will be encrypted. This
   * field is retained for backward compatibility and the value of encrypted
   * will always be `true`. Apigee X and hybrid do not support unencrypted key
   * value maps.
   *
   * @var bool
   */
  public $encrypted;
  /**
   * Optional. Flag that specifies whether entry values will be masked when
   * returned.
   *
   * @var bool
   */
  public $maskedValues;
  /**
   * Required. ID of the key value map.
   *
   * @var string
   */
  public $name;

  /**
   * Required. Flag that specifies whether entry values will be encrypted. This
   * field is retained for backward compatibility and the value of encrypted
   * will always be `true`. Apigee X and hybrid do not support unencrypted key
   * value maps.
   *
   * @param bool $encrypted
   */
  public function setEncrypted($encrypted)
  {
    $this->encrypted = $encrypted;
  }
  /**
   * @return bool
   */
  public function getEncrypted()
  {
    return $this->encrypted;
  }
  /**
   * Optional. Flag that specifies whether entry values will be masked when
   * returned.
   *
   * @param bool $maskedValues
   */
  public function setMaskedValues($maskedValues)
  {
    $this->maskedValues = $maskedValues;
  }
  /**
   * @return bool
   */
  public function getMaskedValues()
  {
    return $this->maskedValues;
  }
  /**
   * Required. ID of the key value map.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1KeyValueMap::class, 'Google_Service_Apigee_GoogleCloudApigeeV1KeyValueMap');
