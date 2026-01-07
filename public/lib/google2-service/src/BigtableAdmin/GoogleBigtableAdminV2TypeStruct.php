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

class GoogleBigtableAdminV2TypeStruct extends \Google\Collection
{
  protected $collection_key = 'fields';
  protected $encodingType = GoogleBigtableAdminV2TypeStructEncoding::class;
  protected $encodingDataType = '';
  protected $fieldsType = GoogleBigtableAdminV2TypeStructField::class;
  protected $fieldsDataType = 'array';

  /**
   * The encoding to use when converting to or from lower level types.
   *
   * @param GoogleBigtableAdminV2TypeStructEncoding $encoding
   */
  public function setEncoding(GoogleBigtableAdminV2TypeStructEncoding $encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return GoogleBigtableAdminV2TypeStructEncoding
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * The names and types of the fields in this struct.
   *
   * @param GoogleBigtableAdminV2TypeStructField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleBigtableAdminV2TypeStructField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeStruct::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeStruct');
