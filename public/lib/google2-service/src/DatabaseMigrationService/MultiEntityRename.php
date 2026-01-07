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

namespace Google\Service\DatabaseMigrationService;

class MultiEntityRename extends \Google\Model
{
  /**
   * Entity name transformation unspecified.
   */
  public const SOURCE_NAME_TRANSFORMATION_ENTITY_NAME_TRANSFORMATION_UNSPECIFIED = 'ENTITY_NAME_TRANSFORMATION_UNSPECIFIED';
  /**
   * No transformation.
   */
  public const SOURCE_NAME_TRANSFORMATION_ENTITY_NAME_TRANSFORMATION_NO_TRANSFORMATION = 'ENTITY_NAME_TRANSFORMATION_NO_TRANSFORMATION';
  /**
   * Transform to lower case.
   */
  public const SOURCE_NAME_TRANSFORMATION_ENTITY_NAME_TRANSFORMATION_LOWER_CASE = 'ENTITY_NAME_TRANSFORMATION_LOWER_CASE';
  /**
   * Transform to upper case.
   */
  public const SOURCE_NAME_TRANSFORMATION_ENTITY_NAME_TRANSFORMATION_UPPER_CASE = 'ENTITY_NAME_TRANSFORMATION_UPPER_CASE';
  /**
   * Transform to capitalized case.
   */
  public const SOURCE_NAME_TRANSFORMATION_ENTITY_NAME_TRANSFORMATION_CAPITALIZED_CASE = 'ENTITY_NAME_TRANSFORMATION_CAPITALIZED_CASE';
  /**
   * Optional. The pattern used to generate the new entity's name. This pattern
   * must include the characters '{name}', which will be replaced with the name
   * of the original entity. For example, the pattern 't_{name}' for an entity
   * name jobs would be converted to 't_jobs'. If unspecified, the default value
   * for this field is '{name}'
   *
   * @var string
   */
  public $newNamePattern;
  /**
   * Optional. Additional transformation that can be done on the source entity
   * name before it is being used by the new_name_pattern, for example lower
   * case. If no transformation is desired, use NO_TRANSFORMATION
   *
   * @var string
   */
  public $sourceNameTransformation;

  /**
   * Optional. The pattern used to generate the new entity's name. This pattern
   * must include the characters '{name}', which will be replaced with the name
   * of the original entity. For example, the pattern 't_{name}' for an entity
   * name jobs would be converted to 't_jobs'. If unspecified, the default value
   * for this field is '{name}'
   *
   * @param string $newNamePattern
   */
  public function setNewNamePattern($newNamePattern)
  {
    $this->newNamePattern = $newNamePattern;
  }
  /**
   * @return string
   */
  public function getNewNamePattern()
  {
    return $this->newNamePattern;
  }
  /**
   * Optional. Additional transformation that can be done on the source entity
   * name before it is being used by the new_name_pattern, for example lower
   * case. If no transformation is desired, use NO_TRANSFORMATION
   *
   * Accepted values: ENTITY_NAME_TRANSFORMATION_UNSPECIFIED,
   * ENTITY_NAME_TRANSFORMATION_NO_TRANSFORMATION,
   * ENTITY_NAME_TRANSFORMATION_LOWER_CASE,
   * ENTITY_NAME_TRANSFORMATION_UPPER_CASE,
   * ENTITY_NAME_TRANSFORMATION_CAPITALIZED_CASE
   *
   * @param self::SOURCE_NAME_TRANSFORMATION_* $sourceNameTransformation
   */
  public function setSourceNameTransformation($sourceNameTransformation)
  {
    $this->sourceNameTransformation = $sourceNameTransformation;
  }
  /**
   * @return self::SOURCE_NAME_TRANSFORMATION_*
   */
  public function getSourceNameTransformation()
  {
    return $this->sourceNameTransformation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultiEntityRename::class, 'Google_Service_DatabaseMigrationService_MultiEntityRename');
