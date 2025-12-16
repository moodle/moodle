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

class GoogleCloudAiplatformV1UpsertDatapointsRequest extends \Google\Collection
{
  protected $collection_key = 'datapoints';
  protected $datapointsType = GoogleCloudAiplatformV1IndexDatapoint::class;
  protected $datapointsDataType = 'array';
  /**
   * Optional. Update mask is used to specify the fields to be overwritten in
   * the datapoints by the update. The fields specified in the update_mask are
   * relative to each IndexDatapoint inside datapoints, not the full request.
   * Updatable fields: * Use `all_restricts` to update both restricts and
   * numeric_restricts.
   *
   * @var string
   */
  public $updateMask;

  /**
   * A list of datapoints to be created/updated.
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
  /**
   * Optional. Update mask is used to specify the fields to be overwritten in
   * the datapoints by the update. The fields specified in the update_mask are
   * relative to each IndexDatapoint inside datapoints, not the full request.
   * Updatable fields: * Use `all_restricts` to update both restricts and
   * numeric_restricts.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UpsertDatapointsRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UpsertDatapointsRequest');
