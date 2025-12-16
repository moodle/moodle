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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1RuntimeTraceConfigOverride extends \Google\Model
{
  /**
   * Name of the API proxy that will have its trace configuration overridden
   * following format: `organizations/{org}/apis/{api}`
   *
   * @var string
   */
  public $apiProxy;
  /**
   * Name of the trace config override in the following format:
   * `organizations/{org}/environment/{env}/traceConfig/overrides/{override}`
   *
   * @var string
   */
  public $name;
  /**
   * The timestamp that the revision was created or updated.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Revision number which can be used by the runtime to detect if the trace
   * config override has changed between two versions.
   *
   * @var string
   */
  public $revisionId;
  protected $samplingConfigType = GoogleCloudApigeeV1RuntimeTraceSamplingConfig::class;
  protected $samplingConfigDataType = '';
  /**
   * Unique ID for the configuration override. The ID will only change if the
   * override is deleted and recreated. Corresponds to name's "override" field.
   *
   * @var string
   */
  public $uid;

  /**
   * Name of the API proxy that will have its trace configuration overridden
   * following format: `organizations/{org}/apis/{api}`
   *
   * @param string $apiProxy
   */
  public function setApiProxy($apiProxy)
  {
    $this->apiProxy = $apiProxy;
  }
  /**
   * @return string
   */
  public function getApiProxy()
  {
    return $this->apiProxy;
  }
  /**
   * Name of the trace config override in the following format:
   * `organizations/{org}/environment/{env}/traceConfig/overrides/{override}`
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
   * The timestamp that the revision was created or updated.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Revision number which can be used by the runtime to detect if the trace
   * config override has changed between two versions.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Trace configuration override for a specific API proxy in an environment.
   *
   * @param GoogleCloudApigeeV1RuntimeTraceSamplingConfig $samplingConfig
   */
  public function setSamplingConfig(GoogleCloudApigeeV1RuntimeTraceSamplingConfig $samplingConfig)
  {
    $this->samplingConfig = $samplingConfig;
  }
  /**
   * @return GoogleCloudApigeeV1RuntimeTraceSamplingConfig
   */
  public function getSamplingConfig()
  {
    return $this->samplingConfig;
  }
  /**
   * Unique ID for the configuration override. The ID will only change if the
   * override is deleted and recreated. Corresponds to name's "override" field.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1RuntimeTraceConfigOverride::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RuntimeTraceConfigOverride');
