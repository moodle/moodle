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

class GoogleCloudAiplatformV1ComputeTokensRequest extends \Google\Collection
{
  protected $collection_key = 'instances';
  protected $contentsType = GoogleCloudAiplatformV1Content::class;
  protected $contentsDataType = 'array';
  /**
   * Optional. The instances that are the input to token computing API call.
   * Schema is identical to the prediction schema of the text model, even for
   * the non-text models, like chat models, or Codey models.
   *
   * @var array[]
   */
  public $instances;
  /**
   * Optional. The name of the publisher model requested to serve the
   * prediction. Format:
   * projects/{project}/locations/{location}/publishers/models
   *
   * @var string
   */
  public $model;

  /**
   * Optional. Input content.
   *
   * @param GoogleCloudAiplatformV1Content[] $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudAiplatformV1Content[]
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Optional. The instances that are the input to token computing API call.
   * Schema is identical to the prediction schema of the text model, even for
   * the non-text models, like chat models, or Codey models.
   *
   * @param array[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return array[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Optional. The name of the publisher model requested to serve the
   * prediction. Format:
   * projects/{project}/locations/{location}/publishers/models
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ComputeTokensRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ComputeTokensRequest');
