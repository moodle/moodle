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

namespace Google\Service\CloudSearch;

class ItemStructuredData extends \Google\Model
{
  /**
   * Hashing value provided by the API caller. This can be used with the
   * items.push method to calculate modified state. The maximum length is 2048
   * characters.
   *
   * @var string
   */
  public $hash;
  protected $objectType = StructuredDataObject::class;
  protected $objectDataType = '';

  /**
   * Hashing value provided by the API caller. This can be used with the
   * items.push method to calculate modified state. The maximum length is 2048
   * characters.
   *
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }
  /**
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }
  /**
   * The structured data object that should conform to a registered object
   * definition in the schema for the data source.
   *
   * @param StructuredDataObject $object
   */
  public function setObject(StructuredDataObject $object)
  {
    $this->object = $object;
  }
  /**
   * @return StructuredDataObject
   */
  public function getObject()
  {
    return $this->object;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemStructuredData::class, 'Google_Service_CloudSearch_ItemStructuredData');
