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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3InlineSchema extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Represents any string value.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Represents any number value.
   */
  public const TYPE_NUMBER = 'NUMBER';
  /**
   * Represents a boolean value.
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Represents a repeated value.
   */
  public const TYPE_ARRAY = 'ARRAY';
  protected $itemsType = GoogleCloudDialogflowCxV3TypeSchema::class;
  protected $itemsDataType = '';
  /**
   * Data type of the schema.
   *
   * @var string
   */
  public $type;

  /**
   * Schema of the elements if this is an ARRAY type.
   *
   * @param GoogleCloudDialogflowCxV3TypeSchema $items
   */
  public function setItems(GoogleCloudDialogflowCxV3TypeSchema $items)
  {
    $this->items = $items;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TypeSchema
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Data type of the schema.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, STRING, NUMBER, BOOLEAN, ARRAY
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
class_alias(GoogleCloudDialogflowCxV3InlineSchema::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3InlineSchema');
