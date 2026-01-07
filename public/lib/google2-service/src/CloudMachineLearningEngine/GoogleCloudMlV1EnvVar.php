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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1EnvVar extends \Google\Model
{
  /**
   * Name of the environment variable. Must be a [valid C identifier](https://gi
   * thub.com/kubernetes/kubernetes/blob/v1.18.8/staging/src/k8s.io/apimachinery
   * /pkg/util/validation/validation.go#L258) and must not begin with the prefix
   * `AIP_`.
   *
   * @var string
   */
  public $name;
  /**
   * Value of the environment variable. Defaults to an empty string. In this
   * field, you can reference [environment variables set by AI Platform
   * Prediction](/ai-platform/prediction/docs/custom-container-requirements#aip-
   * variables) and environment variables set earlier in the same env field as
   * where this message occurs. You cannot reference environment variables set
   * in the Docker image. In order for environment variables to be expanded,
   * reference them by using the following syntax: $(VARIABLE_NAME) Note that
   * this differs from Bash variable expansion, which does not use parentheses.
   * If a variable cannot be resolved, the reference in the input string is used
   * unchanged. To avoid variable expansion, you can escape this syntax with
   * `$$`; for example: $$(VARIABLE_NAME)
   *
   * @var string
   */
  public $value;

  /**
   * Name of the environment variable. Must be a [valid C identifier](https://gi
   * thub.com/kubernetes/kubernetes/blob/v1.18.8/staging/src/k8s.io/apimachinery
   * /pkg/util/validation/validation.go#L258) and must not begin with the prefix
   * `AIP_`.
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
   * Value of the environment variable. Defaults to an empty string. In this
   * field, you can reference [environment variables set by AI Platform
   * Prediction](/ai-platform/prediction/docs/custom-container-requirements#aip-
   * variables) and environment variables set earlier in the same env field as
   * where this message occurs. You cannot reference environment variables set
   * in the Docker image. In order for environment variables to be expanded,
   * reference them by using the following syntax: $(VARIABLE_NAME) Note that
   * this differs from Bash variable expansion, which does not use parentheses.
   * If a variable cannot be resolved, the reference in the input string is used
   * unchanged. To avoid variable expansion, you can escape this syntax with
   * `$$`; for example: $$(VARIABLE_NAME)
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1EnvVar::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1EnvVar');
