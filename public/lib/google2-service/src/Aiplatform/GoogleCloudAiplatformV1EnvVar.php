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

class GoogleCloudAiplatformV1EnvVar extends \Google\Model
{
  /**
   * Required. Name of the environment variable. Must be a valid C identifier.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Variables that reference a $(VAR_NAME) are expanded using the
   * previous defined environment variables in the container and any service
   * environment variables. If a variable cannot be resolved, the reference in
   * the input string will be unchanged. The $(VAR_NAME) syntax can be escaped
   * with a double $$, ie: $$(VAR_NAME). Escaped references will never be
   * expanded, regardless of whether the variable exists or not.
   *
   * @var string
   */
  public $value;

  /**
   * Required. Name of the environment variable. Must be a valid C identifier.
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
   * Required. Variables that reference a $(VAR_NAME) are expanded using the
   * previous defined environment variables in the container and any service
   * environment variables. If a variable cannot be resolved, the reference in
   * the input string will be unchanged. The $(VAR_NAME) syntax can be escaped
   * with a double $$, ie: $$(VAR_NAME). Escaped references will never be
   * expanded, regardless of whether the variable exists or not.
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
class_alias(GoogleCloudAiplatformV1EnvVar::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EnvVar');
