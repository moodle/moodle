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

class GoogleCloudAiplatformV1FunctionResponse extends \Google\Collection
{
  protected $collection_key = 'parts';
  /**
   * Required. The name of the function to call. Matches
   * [FunctionDeclaration.name] and [FunctionCall.name].
   *
   * @var string
   */
  public $name;
  protected $partsType = GoogleCloudAiplatformV1FunctionResponsePart::class;
  protected $partsDataType = 'array';
  /**
   * Required. The function response in JSON object format. Use "output" key to
   * specify function output and "error" key to specify error details (if any).
   * If "output" and "error" keys are not specified, then whole "response" is
   * treated as function output.
   *
   * @var array[]
   */
  public $response;

  /**
   * Required. The name of the function to call. Matches
   * [FunctionDeclaration.name] and [FunctionCall.name].
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
   * Optional. Ordered `Parts` that constitute a function response. Parts may
   * have different IANA MIME types.
   *
   * @param GoogleCloudAiplatformV1FunctionResponsePart[] $parts
   */
  public function setParts($parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return GoogleCloudAiplatformV1FunctionResponsePart[]
   */
  public function getParts()
  {
    return $this->parts;
  }
  /**
   * Required. The function response in JSON object format. Use "output" key to
   * specify function output and "error" key to specify error details (if any).
   * If "output" and "error" keys are not specified, then whole "response" is
   * treated as function output.
   *
   * @param array[] $response
   */
  public function setResponse($response)
  {
    $this->response = $response;
  }
  /**
   * @return array[]
   */
  public function getResponse()
  {
    return $this->response;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FunctionResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FunctionResponse');
