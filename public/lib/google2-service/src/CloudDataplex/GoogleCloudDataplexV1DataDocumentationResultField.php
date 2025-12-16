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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataDocumentationResultField extends \Google\Collection
{
  protected $collection_key = 'fields';
  /**
   * Output only. Generated description for columns and fields.
   *
   * @var string
   */
  public $description;
  protected $fieldsType = GoogleCloudDataplexV1DataDocumentationResultField::class;
  protected $fieldsDataType = 'array';
  /**
   * Output only. The name of the column.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Generated description for columns and fields.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Nested fields.
   *
   * @param GoogleCloudDataplexV1DataDocumentationResultField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleCloudDataplexV1DataDocumentationResultField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Output only. The name of the column.
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
class_alias(GoogleCloudDataplexV1DataDocumentationResultField::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDocumentationResultField');
