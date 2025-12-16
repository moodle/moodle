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

class GoogleCloudAiplatformV1OutputFieldSpec extends \Google\Model
{
  /**
   * Field type is unspecified.
   */
  public const FIELD_TYPE_FIELD_TYPE_UNSPECIFIED = 'FIELD_TYPE_UNSPECIFIED';
  /**
   * Arbitrary content field type.
   */
  public const FIELD_TYPE_CONTENT = 'CONTENT';
  /**
   * Text field type.
   */
  public const FIELD_TYPE_TEXT = 'TEXT';
  /**
   * Image field type.
   */
  public const FIELD_TYPE_IMAGE = 'IMAGE';
  /**
   * Audio field type.
   */
  public const FIELD_TYPE_AUDIO = 'AUDIO';
  /**
   * Required. The name of the output field.
   *
   * @var string
   */
  public $fieldName;
  /**
   * Optional. The data type of the field. Defaults to CONTENT if not set.
   *
   * @var string
   */
  public $fieldType;
  /**
   * Optional. Optional, but recommended. Additional guidance specific to this
   * field to provide targeted instructions for the LLM to generate the content
   * of a single output field. While the LLM can sometimes infer content from
   * the field name, providing explicit guidance is preferred.
   *
   * @var string
   */
  public $guidance;

  /**
   * Required. The name of the output field.
   *
   * @param string $fieldName
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * Optional. The data type of the field. Defaults to CONTENT if not set.
   *
   * Accepted values: FIELD_TYPE_UNSPECIFIED, CONTENT, TEXT, IMAGE, AUDIO
   *
   * @param self::FIELD_TYPE_* $fieldType
   */
  public function setFieldType($fieldType)
  {
    $this->fieldType = $fieldType;
  }
  /**
   * @return self::FIELD_TYPE_*
   */
  public function getFieldType()
  {
    return $this->fieldType;
  }
  /**
   * Optional. Optional, but recommended. Additional guidance specific to this
   * field to provide targeted instructions for the LLM to generate the content
   * of a single output field. While the LLM can sometimes infer content from
   * the field name, providing explicit guidance is preferred.
   *
   * @param string $guidance
   */
  public function setGuidance($guidance)
  {
    $this->guidance = $guidance;
  }
  /**
   * @return string
   */
  public function getGuidance()
  {
    return $this->guidance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1OutputFieldSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1OutputFieldSpec');
