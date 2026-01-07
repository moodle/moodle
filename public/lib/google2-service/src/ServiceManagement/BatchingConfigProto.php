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

namespace Google\Service\ServiceManagement;

class BatchingConfigProto extends \Google\Model
{
  protected $batchDescriptorType = BatchingDescriptorProto::class;
  protected $batchDescriptorDataType = '';
  protected $thresholdsType = BatchingSettingsProto::class;
  protected $thresholdsDataType = '';

  /**
   * The request and response fields used in batching.
   *
   * @param BatchingDescriptorProto $batchDescriptor
   */
  public function setBatchDescriptor(BatchingDescriptorProto $batchDescriptor)
  {
    $this->batchDescriptor = $batchDescriptor;
  }
  /**
   * @return BatchingDescriptorProto
   */
  public function getBatchDescriptor()
  {
    return $this->batchDescriptor;
  }
  /**
   * The thresholds which trigger a batched request to be sent.
   *
   * @param BatchingSettingsProto $thresholds
   */
  public function setThresholds(BatchingSettingsProto $thresholds)
  {
    $this->thresholds = $thresholds;
  }
  /**
   * @return BatchingSettingsProto
   */
  public function getThresholds()
  {
    return $this->thresholds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchingConfigProto::class, 'Google_Service_ServiceManagement_BatchingConfigProto');
