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

namespace Google\Service\Spanner;

class StructType extends \Google\Collection
{
  protected $collection_key = 'fields';
  protected $fieldsType = Field::class;
  protected $fieldsDataType = 'array';

  /**
   * The list of fields that make up this struct. Order is significant, because
   * values of this struct type are represented as lists, where the order of
   * field values matches the order of fields in the StructType. In turn, the
   * order of fields matches the order of columns in a read request, or the
   * order of fields in the `SELECT` clause of a query.
   *
   * @param Field[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return Field[]
   */
  public function getFields()
  {
    return $this->fields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StructType::class, 'Google_Service_Spanner_StructType');
