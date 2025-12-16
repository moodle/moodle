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

class GoogleCloudDataplexV1AspectTypeMetadataTemplateAnnotations extends \Google\Collection
{
  protected $collection_key = 'stringValues';
  /**
   * Optional. Marks a field as deprecated. You can include a deprecation
   * message.
   *
   * @var string
   */
  public $deprecated;
  /**
   * Optional. Description for a field.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Display name for a field.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Display order for a field. You can use this to reorder where a
   * field is rendered.
   *
   * @var int
   */
  public $displayOrder;
  /**
   * Optional. You can use String Type annotations to specify special meaning to
   * string fields. The following values are supported: richText: The field must
   * be interpreted as a rich text field. url: A fully qualified URL link.
   * resource: A service qualified resource reference.
   *
   * @var string
   */
  public $stringType;
  /**
   * Optional. Suggested hints for string fields. You can use them to suggest
   * values to users through console.
   *
   * @var string[]
   */
  public $stringValues;

  /**
   * Optional. Marks a field as deprecated. You can include a deprecation
   * message.
   *
   * @param string $deprecated
   */
  public function setDeprecated($deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return string
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * Optional. Description for a field.
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
   * Optional. Display name for a field.
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
   * Optional. Display order for a field. You can use this to reorder where a
   * field is rendered.
   *
   * @param int $displayOrder
   */
  public function setDisplayOrder($displayOrder)
  {
    $this->displayOrder = $displayOrder;
  }
  /**
   * @return int
   */
  public function getDisplayOrder()
  {
    return $this->displayOrder;
  }
  /**
   * Optional. You can use String Type annotations to specify special meaning to
   * string fields. The following values are supported: richText: The field must
   * be interpreted as a rich text field. url: A fully qualified URL link.
   * resource: A service qualified resource reference.
   *
   * @param string $stringType
   */
  public function setStringType($stringType)
  {
    $this->stringType = $stringType;
  }
  /**
   * @return string
   */
  public function getStringType()
  {
    return $this->stringType;
  }
  /**
   * Optional. Suggested hints for string fields. You can use them to suggest
   * values to users through console.
   *
   * @param string[] $stringValues
   */
  public function setStringValues($stringValues)
  {
    $this->stringValues = $stringValues;
  }
  /**
   * @return string[]
   */
  public function getStringValues()
  {
    return $this->stringValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1AspectTypeMetadataTemplateAnnotations::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AspectTypeMetadataTemplateAnnotations');
