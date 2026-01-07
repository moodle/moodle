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

class GoogleCloudAiplatformV1PrivateServiceConnectConfig extends \Google\Collection
{
  protected $collection_key = 'pscAutomationConfigs';
  /**
   * Required. If true, expose the IndexEndpoint via private service connect.
   *
   * @var bool
   */
  public $enablePrivateServiceConnect;
  /**
   * A list of Projects from which the forwarding rule will target the service
   * attachment.
   *
   * @var string[]
   */
  public $projectAllowlist;
  protected $pscAutomationConfigsType = GoogleCloudAiplatformV1PSCAutomationConfig::class;
  protected $pscAutomationConfigsDataType = 'array';
  /**
   * Output only. The name of the generated service attachment resource. This is
   * only populated if the endpoint is deployed with PrivateServiceConnect.
   *
   * @var string
   */
  public $serviceAttachment;

  /**
   * Required. If true, expose the IndexEndpoint via private service connect.
   *
   * @param bool $enablePrivateServiceConnect
   */
  public function setEnablePrivateServiceConnect($enablePrivateServiceConnect)
  {
    $this->enablePrivateServiceConnect = $enablePrivateServiceConnect;
  }
  /**
   * @return bool
   */
  public function getEnablePrivateServiceConnect()
  {
    return $this->enablePrivateServiceConnect;
  }
  /**
   * A list of Projects from which the forwarding rule will target the service
   * attachment.
   *
   * @param string[] $projectAllowlist
   */
  public function setProjectAllowlist($projectAllowlist)
  {
    $this->projectAllowlist = $projectAllowlist;
  }
  /**
   * @return string[]
   */
  public function getProjectAllowlist()
  {
    return $this->projectAllowlist;
  }
  /**
   * Optional. List of projects and networks where the PSC endpoints will be
   * created. This field is used by Online Inference(Prediction) only.
   *
   * @param GoogleCloudAiplatformV1PSCAutomationConfig[] $pscAutomationConfigs
   */
  public function setPscAutomationConfigs($pscAutomationConfigs)
  {
    $this->pscAutomationConfigs = $pscAutomationConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1PSCAutomationConfig[]
   */
  public function getPscAutomationConfigs()
  {
    return $this->pscAutomationConfigs;
  }
  /**
   * Output only. The name of the generated service attachment resource. This is
   * only populated if the endpoint is deployed with PrivateServiceConnect.
   *
   * @param string $serviceAttachment
   */
  public function setServiceAttachment($serviceAttachment)
  {
    $this->serviceAttachment = $serviceAttachment;
  }
  /**
   * @return string
   */
  public function getServiceAttachment()
  {
    return $this->serviceAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PrivateServiceConnectConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PrivateServiceConnectConfig');
