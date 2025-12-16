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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputsTransformationNumericArrayTransformation extends \Google\Model
{
  /**
   * @var string
   */
  public $columnName;
  /**
   * If invalid values is allowed, the training pipeline will create a boolean
   * feature that indicated whether the value is valid. Otherwise, the training
   * pipeline will discard the input row from trainining data.
   *
   * @var bool
   */
  public $invalidValuesAllowed;

  /**
   * @param string $columnName
   */
  public function setColumnName($columnName)
  {
    $this->columnName = $columnName;
  }
  /**
   * @return string
   */
  public function getColumnName()
  {
    return $this->columnName;
  }
  /**
   * If invalid values is allowed, the training pipeline will create a boolean
   * feature that indicated whether the value is valid. Otherwise, the training
   * pipeline will discard the input row from trainining data.
   *
   * @param bool $invalidValuesAllowed
   */
  public function setInvalidValuesAllowed($invalidValuesAllowed)
  {
    $this->invalidValuesAllowed = $invalidValuesAllowed;
  }
  /**
   * @return bool
   */
  public function getInvalidValuesAllowed()
  {
    return $this->invalidValuesAllowed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputsTransformationNumericArrayTransformation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputsTransformationNumericArrayTransformation');
