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

class GoogleCloudAiplatformV1RubricGenerationSpec extends \Google\Collection
{
  /**
   * The content type to generate is not specified.
   */
  public const RUBRIC_CONTENT_TYPE_RUBRIC_CONTENT_TYPE_UNSPECIFIED = 'RUBRIC_CONTENT_TYPE_UNSPECIFIED';
  /**
   * Generate rubrics based on properties.
   */
  public const RUBRIC_CONTENT_TYPE_PROPERTY = 'PROPERTY';
  /**
   * Generate rubrics in an NL question answer format.
   */
  public const RUBRIC_CONTENT_TYPE_NL_QUESTION_ANSWER = 'NL_QUESTION_ANSWER';
  /**
   * Generate rubrics in a unit test format.
   */
  public const RUBRIC_CONTENT_TYPE_PYTHON_CODE_ASSERTION = 'PYTHON_CODE_ASSERTION';
  protected $collection_key = 'rubricTypeOntology';
  protected $modelConfigType = GoogleCloudAiplatformV1AutoraterConfig::class;
  protected $modelConfigDataType = '';
  /**
   * Template for the prompt used to generate rubrics. The details should be
   * updated based on the most-recent recipe requirements.
   *
   * @var string
   */
  public $promptTemplate;
  /**
   * The type of rubric content to be generated.
   *
   * @var string
   */
  public $rubricContentType;
  /**
   * Optional. An optional, pre-defined list of allowed types for generated
   * rubrics. If this field is provided, it implies `include_rubric_type` should
   * be true, and the generated rubric types should be chosen from this
   * ontology.
   *
   * @var string[]
   */
  public $rubricTypeOntology;

  /**
   * Configuration for the model used in rubric generation. Configs including
   * sampling count and base model can be specified here. Flipping is not
   * supported for rubric generation.
   *
   * @param GoogleCloudAiplatformV1AutoraterConfig $modelConfig
   */
  public function setModelConfig(GoogleCloudAiplatformV1AutoraterConfig $modelConfig)
  {
    $this->modelConfig = $modelConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AutoraterConfig
   */
  public function getModelConfig()
  {
    return $this->modelConfig;
  }
  /**
   * Template for the prompt used to generate rubrics. The details should be
   * updated based on the most-recent recipe requirements.
   *
   * @param string $promptTemplate
   */
  public function setPromptTemplate($promptTemplate)
  {
    $this->promptTemplate = $promptTemplate;
  }
  /**
   * @return string
   */
  public function getPromptTemplate()
  {
    return $this->promptTemplate;
  }
  /**
   * The type of rubric content to be generated.
   *
   * Accepted values: RUBRIC_CONTENT_TYPE_UNSPECIFIED, PROPERTY,
   * NL_QUESTION_ANSWER, PYTHON_CODE_ASSERTION
   *
   * @param self::RUBRIC_CONTENT_TYPE_* $rubricContentType
   */
  public function setRubricContentType($rubricContentType)
  {
    $this->rubricContentType = $rubricContentType;
  }
  /**
   * @return self::RUBRIC_CONTENT_TYPE_*
   */
  public function getRubricContentType()
  {
    return $this->rubricContentType;
  }
  /**
   * Optional. An optional, pre-defined list of allowed types for generated
   * rubrics. If this field is provided, it implies `include_rubric_type` should
   * be true, and the generated rubric types should be chosen from this
   * ontology.
   *
   * @param string[] $rubricTypeOntology
   */
  public function setRubricTypeOntology($rubricTypeOntology)
  {
    $this->rubricTypeOntology = $rubricTypeOntology;
  }
  /**
   * @return string[]
   */
  public function getRubricTypeOntology()
  {
    return $this->rubricTypeOntology;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RubricGenerationSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RubricGenerationSpec');
