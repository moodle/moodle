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

namespace Google\Service\DeveloperConnect;

class RuntimeConfig extends \Google\Model
{
  /**
   * No state specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The runtime configuration has been linked to the InsightsConfig.
   */
  public const STATE_LINKED = 'LINKED';
  /**
   * The runtime configuration has been unlinked to the InsightsConfig.
   */
  public const STATE_UNLINKED = 'UNLINKED';
  protected $appHubServiceType = AppHubService::class;
  protected $appHubServiceDataType = '';
  protected $appHubWorkloadType = AppHubWorkload::class;
  protected $appHubWorkloadDataType = '';
  protected $gkeWorkloadType = GKEWorkload::class;
  protected $gkeWorkloadDataType = '';
  protected $googleCloudRunType = GoogleCloudRun::class;
  protected $googleCloudRunDataType = '';
  /**
   * Output only. The state of the Runtime.
   *
   * @var string
   */
  public $state;
  /**
   * Required. Immutable. The URI of the runtime configuration. For GKE, this is
   * the cluster name. For Cloud Run, this is the service name.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. App Hub Service.
   *
   * @param AppHubService $appHubService
   */
  public function setAppHubService(AppHubService $appHubService)
  {
    $this->appHubService = $appHubService;
  }
  /**
   * @return AppHubService
   */
  public function getAppHubService()
  {
    return $this->appHubService;
  }
  /**
   * Output only. App Hub Workload.
   *
   * @param AppHubWorkload $appHubWorkload
   */
  public function setAppHubWorkload(AppHubWorkload $appHubWorkload)
  {
    $this->appHubWorkload = $appHubWorkload;
  }
  /**
   * @return AppHubWorkload
   */
  public function getAppHubWorkload()
  {
    return $this->appHubWorkload;
  }
  /**
   * Output only. Google Kubernetes Engine runtime.
   *
   * @param GKEWorkload $gkeWorkload
   */
  public function setGkeWorkload(GKEWorkload $gkeWorkload)
  {
    $this->gkeWorkload = $gkeWorkload;
  }
  /**
   * @return GKEWorkload
   */
  public function getGkeWorkload()
  {
    return $this->gkeWorkload;
  }
  /**
   * Output only. Cloud Run runtime.
   *
   * @param GoogleCloudRun $googleCloudRun
   */
  public function setGoogleCloudRun(GoogleCloudRun $googleCloudRun)
  {
    $this->googleCloudRun = $googleCloudRun;
  }
  /**
   * @return GoogleCloudRun
   */
  public function getGoogleCloudRun()
  {
    return $this->googleCloudRun;
  }
  /**
   * Output only. The state of the Runtime.
   *
   * Accepted values: STATE_UNSPECIFIED, LINKED, UNLINKED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Required. Immutable. The URI of the runtime configuration. For GKE, this is
   * the cluster name. For Cloud Run, this is the service name.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuntimeConfig::class, 'Google_Service_DeveloperConnect_RuntimeConfig');
