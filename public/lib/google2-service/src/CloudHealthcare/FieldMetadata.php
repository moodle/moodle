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

namespace Google\Service\CloudHealthcare;

class FieldMetadata extends \Google\Collection
{
  /**
   * No action specified. Defaults to DO_NOT_TRANSFORM.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Transform the entire field.
   */
  public const ACTION_TRANSFORM = 'TRANSFORM';
  /**
   * Inspect and transform any found PHI.
   */
  public const ACTION_INSPECT_AND_TRANSFORM = 'INSPECT_AND_TRANSFORM';
  /**
   * Do not transform.
   */
  public const ACTION_DO_NOT_TRANSFORM = 'DO_NOT_TRANSFORM';
  protected $collection_key = 'paths';
  /**
   * Optional. Deidentify action for one field.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. List of paths to FHIR fields to be redacted. Each path is a
   * period-separated list where each component is either a field name or FHIR
   * type name, for example: Patient, HumanName. For "choice" types (those
   * defined in the FHIR spec with the form: field[x]) we use two separate
   * components. For example, "deceasedAge.unit" is matched by
   * "Deceased.Age.unit". Supported types are: AdministrativeGenderCode,
   * Base64Binary, Boolean, Code, Date, DateTime, Decimal, HumanName, Id,
   * Instant, Integer, LanguageCode, Markdown, Oid, PositiveInt, String,
   * UnsignedInt, Uri, Uuid, Xhtml.
   *
   * @var string[]
   */
  public $paths;

  /**
   * Optional. Deidentify action for one field.
   *
   * Accepted values: ACTION_UNSPECIFIED, TRANSFORM, INSPECT_AND_TRANSFORM,
   * DO_NOT_TRANSFORM
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. List of paths to FHIR fields to be redacted. Each path is a
   * period-separated list where each component is either a field name or FHIR
   * type name, for example: Patient, HumanName. For "choice" types (those
   * defined in the FHIR spec with the form: field[x]) we use two separate
   * components. For example, "deceasedAge.unit" is matched by
   * "Deceased.Age.unit". Supported types are: AdministrativeGenderCode,
   * Base64Binary, Boolean, Code, Date, DateTime, Decimal, HumanName, Id,
   * Instant, Integer, LanguageCode, Markdown, Oid, PositiveInt, String,
   * UnsignedInt, Uri, Uuid, Xhtml.
   *
   * @param string[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return string[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldMetadata::class, 'Google_Service_CloudHealthcare_FieldMetadata');
