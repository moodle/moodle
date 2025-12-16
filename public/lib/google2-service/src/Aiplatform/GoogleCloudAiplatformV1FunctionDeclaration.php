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

class GoogleCloudAiplatformV1FunctionDeclaration extends \Google\Model
{
  /**
   * Optional. Description and purpose of the function. Model uses it to decide
   * how and whether to call the function.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The name of the function to call. Must start with a letter or an
   * underscore. Must be a-z, A-Z, 0-9, or contain underscores, dots, colons and
   * dashes, with a maximum length of 64.
   *
   * @var string
   */
  public $name;
  protected $parametersType = GoogleCloudAiplatformV1Schema::class;
  protected $parametersDataType = '';
  /**
   * Optional. Describes the parameters to the function in JSON Schema format.
   * The schema must describe an object where the properties are the parameters
   * to the function. For example: ``` { "type": "object", "properties": {
   * "name": { "type": "string" }, "age": { "type": "integer" } },
   * "additionalProperties": false, "required": ["name", "age"],
   * "propertyOrdering": ["name", "age"] } ``` This field is mutually exclusive
   * with `parameters`.
   *
   * @var array
   */
  public $parametersJsonSchema;
  protected $responseType = GoogleCloudAiplatformV1Schema::class;
  protected $responseDataType = '';
  /**
   * Optional. Describes the output from this function in JSON Schema format.
   * The value specified by the schema is the response value of the function.
   * This field is mutually exclusive with `response`.
   *
   * @var array
   */
  public $responseJsonSchema;

  /**
   * Optional. Description and purpose of the function. Model uses it to decide
   * how and whether to call the function.
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
   * Required. The name of the function to call. Must start with a letter or an
   * underscore. Must be a-z, A-Z, 0-9, or contain underscores, dots, colons and
   * dashes, with a maximum length of 64.
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
   * Optional. Describes the parameters to this function in JSON Schema Object
   * format. Reflects the Open API 3.03 Parameter Object. string Key: the name
   * of the parameter. Parameter names are case sensitive. Schema Value: the
   * Schema defining the type used for the parameter. For function with no
   * parameters, this can be left unset. Parameter names must start with a
   * letter or an underscore and must only contain chars a-z, A-Z, 0-9, or
   * underscores with a maximum length of 64. Example with 1 required and 1
   * optional parameter: type: OBJECT properties: param1: type: STRING param2:
   * type: INTEGER required: - param1
   *
   * @param GoogleCloudAiplatformV1Schema $parameters
   */
  public function setParameters(GoogleCloudAiplatformV1Schema $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Describes the parameters to the function in JSON Schema format.
   * The schema must describe an object where the properties are the parameters
   * to the function. For example: ``` { "type": "object", "properties": {
   * "name": { "type": "string" }, "age": { "type": "integer" } },
   * "additionalProperties": false, "required": ["name", "age"],
   * "propertyOrdering": ["name", "age"] } ``` This field is mutually exclusive
   * with `parameters`.
   *
   * @param array $parametersJsonSchema
   */
  public function setParametersJsonSchema($parametersJsonSchema)
  {
    $this->parametersJsonSchema = $parametersJsonSchema;
  }
  /**
   * @return array
   */
  public function getParametersJsonSchema()
  {
    return $this->parametersJsonSchema;
  }
  /**
   * Optional. Describes the output from this function in JSON Schema format.
   * Reflects the Open API 3.03 Response Object. The Schema defines the type
   * used for the response value of the function.
   *
   * @param GoogleCloudAiplatformV1Schema $response
   */
  public function setResponse(GoogleCloudAiplatformV1Schema $response)
  {
    $this->response = $response;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema
   */
  public function getResponse()
  {
    return $this->response;
  }
  /**
   * Optional. Describes the output from this function in JSON Schema format.
   * The value specified by the schema is the response value of the function.
   * This field is mutually exclusive with `response`.
   *
   * @param array $responseJsonSchema
   */
  public function setResponseJsonSchema($responseJsonSchema)
  {
    $this->responseJsonSchema = $responseJsonSchema;
  }
  /**
   * @return array
   */
  public function getResponseJsonSchema()
  {
    return $this->responseJsonSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FunctionDeclaration::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FunctionDeclaration');
