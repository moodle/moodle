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

class GoogleCloudAiplatformV1ReadIndexDatapointsResponse extends \Google\Collection
{
  protected $collection_key = 'datapoints';
  protected $datapointsType = GoogleCloudAiplatformV1IndexDatapoint::class;
  protected $datapointsDataType = 'array';

  /**
   * The result list of datapoints.
   *
   * @param GoogleCloudAiplatformV1IndexDatapoint[] $datapoints
   */
  public function setDatapoints($datapoints)
  {
    $this->datapoints = $datapoints;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexDatapoint[]
   */
  public function getDatapoints()
  {
    return $this->datapoints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReadIndexDatapointsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReadIndexDatapointsResponse');
