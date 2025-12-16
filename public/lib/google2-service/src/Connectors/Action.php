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

namespace Google\Service\Connectors;

class Action extends \Google\Collection
{
  protected $collection_key = 'resultMetadata';
  /**
   * Brief Description of action
   *
   * @var string
   */
  public $description;
  /**
   * Display Name of action to be shown on client side
   *
   * @var string
   */
  public $displayName;
  protected $inputJsonSchemaType = JsonSchema::class;
  protected $inputJsonSchemaDataType = '';
  protected $inputParametersType = InputParameter::class;
  protected $inputParametersDataType = 'array';
  /**
   * Metadata like service latency, etc.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Name of the action.
   *
   * @var string
   */
  public $name;
  protected $resultJsonSchemaType = JsonSchema::class;
  protected $resultJsonSchemaDataType = '';
  protected $resultMetadataType = ResultMetadata::class;
  protected $resultMetadataDataType = 'array';

  /**
   * Brief Description of action
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
   * Display Name of action to be shown on client side
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
   * JsonSchema representation of this actions's input schema
   *
   * @param JsonSchema $inputJsonSchema
   */
  public function setInputJsonSchema(JsonSchema $inputJsonSchema)
  {
    $this->inputJsonSchema = $inputJsonSchema;
  }
  /**
   * @return JsonSchema
   */
  public function getInputJsonSchema()
  {
    return $this->inputJsonSchema;
  }
  /**
   * List containing input parameter metadata.
   *
   * @param InputParameter[] $inputParameters
   */
  public function setInputParameters($inputParameters)
  {
    $this->inputParameters = $inputParameters;
  }
  /**
   * @return InputParameter[]
   */
  public function getInputParameters()
  {
    return $this->inputParameters;
  }
  /**
   * Metadata like service latency, etc.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Name of the action.
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
   * JsonSchema representation of this actions's result schema
   *
   * @param JsonSchema $resultJsonSchema
   */
  public function setResultJsonSchema(JsonSchema $resultJsonSchema)
  {
    $this->resultJsonSchema = $resultJsonSchema;
  }
  /**
   * @return JsonSchema
   */
  public function getResultJsonSchema()
  {
    return $this->resultJsonSchema;
  }
  /**
   * List containing the metadata of result fields.
   *
   * @param ResultMetadata[] $resultMetadata
   */
  public function setResultMetadata($resultMetadata)
  {
    $this->resultMetadata = $resultMetadata;
  }
  /**
   * @return ResultMetadata[]
   */
  public function getResultMetadata()
  {
    return $this->resultMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Action::class, 'Google_Service_Connectors_Action');
