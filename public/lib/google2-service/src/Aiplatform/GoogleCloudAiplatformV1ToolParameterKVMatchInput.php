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

class GoogleCloudAiplatformV1ToolParameterKVMatchInput extends \Google\Collection
{
  protected $collection_key = 'instances';
  protected $instancesType = GoogleCloudAiplatformV1ToolParameterKVMatchInstance::class;
  protected $instancesDataType = 'array';
  protected $metricSpecType = GoogleCloudAiplatformV1ToolParameterKVMatchSpec::class;
  protected $metricSpecDataType = '';

  /**
   * Required. Repeated tool parameter key value match instances.
   *
   * @param GoogleCloudAiplatformV1ToolParameterKVMatchInstance[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolParameterKVMatchInstance[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Required. Spec for tool parameter key value match metric.
   *
   * @param GoogleCloudAiplatformV1ToolParameterKVMatchSpec $metricSpec
   */
  public function setMetricSpec(GoogleCloudAiplatformV1ToolParameterKVMatchSpec $metricSpec)
  {
    $this->metricSpec = $metricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolParameterKVMatchSpec
   */
  public function getMetricSpec()
  {
    return $this->metricSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ToolParameterKVMatchInput::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ToolParameterKVMatchInput');
