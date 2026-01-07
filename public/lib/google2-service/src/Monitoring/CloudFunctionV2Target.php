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

class CloudFunctionV2Target extends \Google\Model
{
  protected $cloudRunRevisionType = MonitoredResource::class;
  protected $cloudRunRevisionDataType = '';
  /**
   * Required. Fully qualified GCFv2 resource name i.e.
   * projects/{project}/locations/{location}/functions/{function} Required.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The cloud_run_revision Monitored Resource associated with the
   * GCFv2. The Synthetic Monitor execution results (metrics, logs, and spans)
   * are reported against this Monitored Resource. This field is output only.
   *
   * @param MonitoredResource $cloudRunRevision
   */
  public function setCloudRunRevision(MonitoredResource $cloudRunRevision)
  {
    $this->cloudRunRevision = $cloudRunRevision;
  }
  /**
   * @return MonitoredResource
   */
  public function getCloudRunRevision()
  {
    return $this->cloudRunRevision;
  }
  /**
   * Required. Fully qualified GCFv2 resource name i.e.
   * projects/{project}/locations/{location}/functions/{function} Required.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudFunctionV2Target::class, 'Google_Service_Monitoring_CloudFunctionV2Target');
