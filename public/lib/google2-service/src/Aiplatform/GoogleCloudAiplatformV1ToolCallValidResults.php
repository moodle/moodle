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

class GoogleCloudAiplatformV1ToolCallValidResults extends \Google\Collection
{
  protected $collection_key = 'toolCallValidMetricValues';
  protected $toolCallValidMetricValuesType = GoogleCloudAiplatformV1ToolCallValidMetricValue::class;
  protected $toolCallValidMetricValuesDataType = 'array';

  /**
   * Output only. Tool call valid metric values.
   *
   * @param GoogleCloudAiplatformV1ToolCallValidMetricValue[] $toolCallValidMetricValues
   */
  public function setToolCallValidMetricValues($toolCallValidMetricValues)
  {
    $this->toolCallValidMetricValues = $toolCallValidMetricValues;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolCallValidMetricValue[]
   */
  public function getToolCallValidMetricValues()
  {
    return $this->toolCallValidMetricValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ToolCallValidResults::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ToolCallValidResults');
