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

class GoogleCloudApigeeV1SchemaSchemaProperty extends \Google\Model
{
  /**
   * Time the field was created in RFC3339 string form. For example:
   * `2016-02-26T10:23:09.592Z`.
   *
   * @var string
   */
  public $createTime;
  /**
   * Flag that specifies whether the field is standard in the dataset or a
   * custom field created by the customer. `true` indicates that it is a custom
   * field.
   *
   * @var string
   */
  public $custom;
  /**
   * Data type of the field.
   *
   * @var string
   */
  public $type;

  /**
   * Time the field was created in RFC3339 string form. For example:
   * `2016-02-26T10:23:09.592Z`.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Flag that specifies whether the field is standard in the dataset or a
   * custom field created by the customer. `true` indicates that it is a custom
   * field.
   *
   * @param string $custom
   */
  public function setCustom($custom)
  {
    $this->custom = $custom;
  }
  /**
   * @return string
   */
  public function getCustom()
  {
    return $this->custom;
  }
  /**
   * Data type of the field.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SchemaSchemaProperty::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SchemaSchemaProperty');
