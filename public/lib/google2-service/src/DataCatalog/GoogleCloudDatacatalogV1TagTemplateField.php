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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1TagTemplateField extends \Google\Model
{
  /**
   * The description for this field. Defaults to an empty string.
   *
   * @var string
   */
  public $description;
  /**
   * The display name for this field. Defaults to an empty string. The name must
   * contain only Unicode letters, numbers (0-9), underscores (_), dashes (-),
   * spaces ( ), and can't start or end with spaces. The maximum length is 200
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * If true, this field is required. Defaults to false.
   *
   * @var bool
   */
  public $isRequired;
  /**
   * Identifier. The resource name of the tag template field in URL format.
   * Example: `projects/{PROJECT_ID}/locations/{LOCATION}/tagTemplates/{TAG_TEMP
   * LATE}/fields/{FIELD}` Note: The tag template field itself might not be
   * stored in the location specified in its name. The name must contain only
   * letters (a-z, A-Z), numbers (0-9), or underscores (_), and must start with
   * a letter or underscore. The maximum length is 64 characters.
   *
   * @var string
   */
  public $name;
  /**
   * The order of this field with respect to other fields in this tag template.
   * For example, a higher value can indicate a more important field. The value
   * can be negative. Multiple fields can have the same order and field orders
   * within a tag don't have to be sequential.
   *
   * @var int
   */
  public $order;
  protected $typeType = GoogleCloudDatacatalogV1FieldType::class;
  protected $typeDataType = '';

  /**
   * The description for this field. Defaults to an empty string.
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
   * The display name for this field. Defaults to an empty string. The name must
   * contain only Unicode letters, numbers (0-9), underscores (_), dashes (-),
   * spaces ( ), and can't start or end with spaces. The maximum length is 200
   * characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * If true, this field is required. Defaults to false.
   *
   * @param bool $isRequired
   */
  public function setIsRequired($isRequired)
  {
    $this->isRequired = $isRequired;
  }
  /**
   * @return bool
   */
  public function getIsRequired()
  {
    return $this->isRequired;
  }
  /**
   * Identifier. The resource name of the tag template field in URL format.
   * Example: `projects/{PROJECT_ID}/locations/{LOCATION}/tagTemplates/{TAG_TEMP
   * LATE}/fields/{FIELD}` Note: The tag template field itself might not be
   * stored in the location specified in its name. The name must contain only
   * letters (a-z, A-Z), numbers (0-9), or underscores (_), and must start with
   * a letter or underscore. The maximum length is 64 characters.
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
  /**
   * The order of this field with respect to other fields in this tag template.
   * For example, a higher value can indicate a more important field. The value
   * can be negative. Multiple fields can have the same order and field orders
   * within a tag don't have to be sequential.
   *
   * @param int $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return int
   */
  public function getOrder()
  {
    return $this->order;
  }
  /**
   * Required. The type of value this tag field can contain.
   *
   * @param GoogleCloudDatacatalogV1FieldType $type
   */
  public function setType(GoogleCloudDatacatalogV1FieldType $type)
  {
    $this->type = $type;
  }
  /**
   * @return GoogleCloudDatacatalogV1FieldType
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1TagTemplateField::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1TagTemplateField');
