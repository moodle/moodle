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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2EnvVar extends \Google\Model
{
  /**
   * Required. Name of the environment variable. Must not exceed 32768
   * characters.
   *
   * @var string
   */
  public $name;
  /**
   * Literal value of the environment variable. Defaults to "", and the maximum
   * length is 32768 bytes. Variable references are not supported in Cloud Run.
   *
   * @var string
   */
  public $value;
  protected $valueSourceType = GoogleCloudRunV2EnvVarSource::class;
  protected $valueSourceDataType = '';

  /**
   * Required. Name of the environment variable. Must not exceed 32768
   * characters.
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
   * Literal value of the environment variable. Defaults to "", and the maximum
   * length is 32768 bytes. Variable references are not supported in Cloud Run.
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
  /**
   * Source for the environment variable's value.
   *
   * @param GoogleCloudRunV2EnvVarSource $valueSource
   */
  public function setValueSource(GoogleCloudRunV2EnvVarSource $valueSource)
  {
    $this->valueSource = $valueSource;
  }
  /**
   * @return GoogleCloudRunV2EnvVarSource
   */
  public function getValueSource()
  {
    return $this->valueSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2EnvVar::class, 'Google_Service_CloudRun_GoogleCloudRunV2EnvVar');
