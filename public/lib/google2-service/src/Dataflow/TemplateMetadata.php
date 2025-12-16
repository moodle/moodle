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

namespace Google\Service\Dataflow;

class TemplateMetadata extends \Google\Collection
{
  protected $collection_key = 'parameters';
  /**
   * Optional. Indicates the default streaming mode for a streaming template.
   * Only valid if both supports_at_least_once and supports_exactly_once are
   * true. Possible values: UNSPECIFIED, EXACTLY_ONCE and AT_LEAST_ONCE
   *
   * @var string
   */
  public $defaultStreamingMode;
  /**
   * Optional. A description of the template.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The name of the template.
   *
   * @var string
   */
  public $name;
  protected $parametersType = ParameterMetadata::class;
  protected $parametersDataType = 'array';
  /**
   * Optional. Indicates if the template is streaming or not.
   *
   * @var bool
   */
  public $streaming;
  /**
   * Optional. Indicates if the streaming template supports at least once mode.
   *
   * @var bool
   */
  public $supportsAtLeastOnce;
  /**
   * Optional. Indicates if the streaming template supports exactly once mode.
   *
   * @var bool
   */
  public $supportsExactlyOnce;
  /**
   * Optional. For future use.
   *
   * @var string
   */
  public $yamlDefinition;

  /**
   * Optional. Indicates the default streaming mode for a streaming template.
   * Only valid if both supports_at_least_once and supports_exactly_once are
   * true. Possible values: UNSPECIFIED, EXACTLY_ONCE and AT_LEAST_ONCE
   *
   * @param string $defaultStreamingMode
   */
  public function setDefaultStreamingMode($defaultStreamingMode)
  {
    $this->defaultStreamingMode = $defaultStreamingMode;
  }
  /**
   * @return string
   */
  public function getDefaultStreamingMode()
  {
    return $this->defaultStreamingMode;
  }
  /**
   * Optional. A description of the template.
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
   * Required. The name of the template.
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
   * The parameters for the template.
   *
   * @param ParameterMetadata[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return ParameterMetadata[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Indicates if the template is streaming or not.
   *
   * @param bool $streaming
   */
  public function setStreaming($streaming)
  {
    $this->streaming = $streaming;
  }
  /**
   * @return bool
   */
  public function getStreaming()
  {
    return $this->streaming;
  }
  /**
   * Optional. Indicates if the streaming template supports at least once mode.
   *
   * @param bool $supportsAtLeastOnce
   */
  public function setSupportsAtLeastOnce($supportsAtLeastOnce)
  {
    $this->supportsAtLeastOnce = $supportsAtLeastOnce;
  }
  /**
   * @return bool
   */
  public function getSupportsAtLeastOnce()
  {
    return $this->supportsAtLeastOnce;
  }
  /**
   * Optional. Indicates if the streaming template supports exactly once mode.
   *
   * @param bool $supportsExactlyOnce
   */
  public function setSupportsExactlyOnce($supportsExactlyOnce)
  {
    $this->supportsExactlyOnce = $supportsExactlyOnce;
  }
  /**
   * @return bool
   */
  public function getSupportsExactlyOnce()
  {
    return $this->supportsExactlyOnce;
  }
  /**
   * Optional. For future use.
   *
   * @param string $yamlDefinition
   */
  public function setYamlDefinition($yamlDefinition)
  {
    $this->yamlDefinition = $yamlDefinition;
  }
  /**
   * @return string
   */
  public function getYamlDefinition()
  {
    return $this->yamlDefinition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TemplateMetadata::class, 'Google_Service_Dataflow_TemplateMetadata');
