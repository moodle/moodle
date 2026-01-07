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

namespace Google\Service\Monitoring;

class SyntheticMonitorTarget extends \Google\Model
{
  protected $cloudFunctionV2Type = CloudFunctionV2Target::class;
  protected $cloudFunctionV2DataType = '';

  /**
   * Target a Synthetic Monitor GCFv2 instance.
   *
   * @param CloudFunctionV2Target $cloudFunctionV2
   */
  public function setCloudFunctionV2(CloudFunctionV2Target $cloudFunctionV2)
  {
    $this->cloudFunctionV2 = $cloudFunctionV2;
  }
  /**
   * @return CloudFunctionV2Target
   */
  public function getCloudFunctionV2()
  {
    return $this->cloudFunctionV2;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SyntheticMonitorTarget::class, 'Google_Service_Monitoring_SyntheticMonitorTarget');
