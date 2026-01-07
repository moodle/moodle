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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentSchemaEntityTypeProperty extends \Google\Model
{
  /**
   * Unspecified method. It defaults to `EXTRACT`.
   */
  public const METHOD_METHOD_UNSPECIFIED = 'METHOD_UNSPECIFIED';
  /**
   * The entity's value is directly extracted as-is from the document text.
   */
  public const METHOD_EXTRACT = 'EXTRACT';
  /**
   * The entity's value is derived through inference and is not necessarily an
   * exact text extraction from the document.
   */
  public const METHOD_DERIVE = 'DERIVE';
  /**
   * Unspecified occurrence type.
   */
  public const OCCURRENCE_TYPE_OCCURRENCE_TYPE_UNSPECIFIED = 'OCCURRENCE_TYPE_UNSPECIFIED';
  /**
   * There will be zero or one instance of this entity type. The same entity
   * instance may be mentioned multiple times.
   */
  public const OCCURRENCE_TYPE_OPTIONAL_ONCE = 'OPTIONAL_ONCE';
  /**
   * The entity type will appear zero or multiple times.
   */
  public const OCCURRENCE_TYPE_OPTIONAL_MULTIPLE = 'OPTIONAL_MULTIPLE';
  /**
   * The entity type will only appear exactly once. The same entity instance may
   * be mentioned multiple times.
   */
  public const OCCURRENCE_TYPE_REQUIRED_ONCE = 'REQUIRED_ONCE';
  /**
   * The entity type will appear once or more times.
   */
  public const OCCURRENCE_TYPE_REQUIRED_MULTIPLE = 'REQUIRED_MULTIPLE';
  /**
   * User defined name for the property.
   *
   * @var string
   */
  public $displayName;
  /**
   * Specifies how the entity's value is obtained.
   *
   * @var string
   */
  public $method;
  /**
   * The name of the property. Follows the same guidelines as the EntityType
   * name.
   *
   * @var string
   */
  public $name;
  /**
   * Occurrence type limits the number of instances an entity type appears in
   * the document.
   *
   * @var string
   */
  public $occurrenceType;
  /**
   * A reference to the value type of the property. This type is subject to the
   * same conventions as the `Entity.base_types` field.
   *
   * @var string
   */
  public $valueType;

  /**
   * User defined name for the property.
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
   * Specifies how the entity's value is obtained.
   *
   * Accepted values: METHOD_UNSPECIFIED, EXTRACT, DERIVE
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The name of the property. Follows the same guidelines as the EntityType
   * name.
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
   * Occurrence type limits the number of instances an entity type appears in
   * the document.
   *
   * Accepted values: OCCURRENCE_TYPE_UNSPECIFIED, OPTIONAL_ONCE,
   * OPTIONAL_MULTIPLE, REQUIRED_ONCE, REQUIRED_MULTIPLE
   *
   * @param self::OCCURRENCE_TYPE_* $occurrenceType
   */
  public function setOccurrenceType($occurrenceType)
  {
    $this->occurrenceType = $occurrenceType;
  }
  /**
   * @return self::OCCURRENCE_TYPE_*
   */
  public function getOccurrenceType()
  {
    return $this->occurrenceType;
  }
  /**
   * A reference to the value type of the property. This type is subject to the
   * same conventions as the `Entity.base_types` field.
   *
   * @param string $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return string
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentSchemaEntityTypeProperty::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentSchemaEntityTypeProperty');
