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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3beta1FulfillmentGeneratorSettings extends \Google\Model
{
  /**
   * Required. The generator to call. Format:
   * `projects//locations//agents//generators/`.
   *
   * @var string
   */
  public $generator;
  /**
   * Map from placeholder parameter in the Generator to corresponding session
   * parameters. By default, Dialogflow uses the session parameter with the same
   * name to fill in the generator template. e.g. If there is a placeholder
   * parameter `city` in the Generator, Dialogflow default to fill in the
   * `$city` with `$session.params.city`. However, you may choose to fill
   * `$city` with `$session.params.desination-city`. - Map key: parameter ID -
   * Map value: session parameter name
   *
   * @var string[]
   */
  public $inputParameters;
  /**
   * Required. Output parameter which should contain the generator response.
   *
   * @var string
   */
  public $outputParameter;

  /**
   * Required. The generator to call. Format:
   * `projects//locations//agents//generators/`.
   *
   * @param string $generator
   */
  public function setGenerator($generator)
  {
    $this->generator = $generator;
  }
  /**
   * @return string
   */
  public function getGenerator()
  {
    return $this->generator;
  }
  /**
   * Map from placeholder parameter in the Generator to corresponding session
   * parameters. By default, Dialogflow uses the session parameter with the same
   * name to fill in the generator template. e.g. If there is a placeholder
   * parameter `city` in the Generator, Dialogflow default to fill in the
   * `$city` with `$session.params.city`. However, you may choose to fill
   * `$city` with `$session.params.desination-city`. - Map key: parameter ID -
   * Map value: session parameter name
   *
   * @param string[] $inputParameters
   */
  public function setInputParameters($inputParameters)
  {
    $this->inputParameters = $inputParameters;
  }
  /**
   * @return string[]
   */
  public function getInputParameters()
  {
    return $this->inputParameters;
  }
  /**
   * Required. Output parameter which should contain the generator response.
   *
   * @param string $outputParameter
   */
  public function setOutputParameter($outputParameter)
  {
    $this->outputParameter = $outputParameter;
  }
  /**
   * @return string
   */
  public function getOutputParameter()
  {
    return $this->outputParameter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1FulfillmentGeneratorSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1FulfillmentGeneratorSettings');
