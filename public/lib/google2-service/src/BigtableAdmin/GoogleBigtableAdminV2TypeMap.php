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

namespace Google\Service\BigtableAdmin;

class GoogleBigtableAdminV2TypeMap extends \Google\Model
{
  protected $keyTypeType = Type::class;
  protected $keyTypeDataType = '';
  protected $valueTypeType = Type::class;
  protected $valueTypeDataType = '';

  /**
   * The type of a map key. Only `Bytes`, `String`, and `Int64` are allowed as
   * key types.
   *
   * @param Type $keyType
   */
  public function setKeyType(Type $keyType)
  {
    $this->keyType = $keyType;
  }
  /**
   * @return Type
   */
  public function getKeyType()
  {
    return $this->keyType;
  }
  /**
   * The type of the values in a map.
   *
   * @param Type $valueType
   */
  public function setValueType(Type $valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return Type
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeMap::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeMap');
