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

class GoogleCloudAiplatformV1FunctionCall extends \Google\Collection
{
  protected $collection_key = 'partialArgs';
  /**
   * Optional. The function parameters and values in JSON object format. See
   * [FunctionDeclaration.parameters] for parameter details.
   *
   * @var array[]
   */
  public $args;
  /**
   * Optional. The name of the function to call. Matches
   * [FunctionDeclaration.name].
   *
   * @var string
   */
  public $name;
  protected $partialArgsType = GoogleCloudAiplatformV1PartialArg::class;
  protected $partialArgsDataType = 'array';
  /**
   * Optional. Whether this is the last part of the FunctionCall. If true,
   * another partial message for the current FunctionCall is expected to follow.
   *
   * @var bool
   */
  public $willContinue;

  /**
   * Optional. The function parameters and values in JSON object format. See
   * [FunctionDeclaration.parameters] for parameter details.
   *
   * @param array[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return array[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Optional. The name of the function to call. Matches
   * [FunctionDeclaration.name].
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
   * Optional. The partial argument value of the function call. If provided,
   * represents the arguments/fields that are streamed incrementally.
   *
   * @param GoogleCloudAiplatformV1PartialArg[] $partialArgs
   */
  public function setPartialArgs($partialArgs)
  {
    $this->partialArgs = $partialArgs;
  }
  /**
   * @return GoogleCloudAiplatformV1PartialArg[]
   */
  public function getPartialArgs()
  {
    return $this->partialArgs;
  }
  /**
   * Optional. Whether this is the last part of the FunctionCall. If true,
   * another partial message for the current FunctionCall is expected to follow.
   *
   * @param bool $willContinue
   */
  public function setWillContinue($willContinue)
  {
    $this->willContinue = $willContinue;
  }
  /**
   * @return bool
   */
  public function getWillContinue()
  {
    return $this->willContinue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FunctionCall::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FunctionCall');
