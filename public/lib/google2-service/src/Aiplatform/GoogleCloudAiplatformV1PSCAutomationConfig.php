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

class GoogleCloudAiplatformV1PSCAutomationConfig extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const STATE_PSC_AUTOMATION_STATE_UNSPECIFIED = 'PSC_AUTOMATION_STATE_UNSPECIFIED';
  /**
   * The PSC service automation is successful.
   */
  public const STATE_PSC_AUTOMATION_STATE_SUCCESSFUL = 'PSC_AUTOMATION_STATE_SUCCESSFUL';
  /**
   * The PSC service automation has failed.
   */
  public const STATE_PSC_AUTOMATION_STATE_FAILED = 'PSC_AUTOMATION_STATE_FAILED';
  /**
   * Output only. Error message if the PSC service automation failed.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Output only. Forwarding rule created by the PSC service automation.
   *
   * @var string
   */
  public $forwardingRule;
  /**
   * Output only. IP address rule created by the PSC service automation.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Required. The full name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks). [Format](https://cloud.google.com/compute/docs/referen
   * ce/rest/v1/networks/get): `projects/{project}/global/networks/{network}`.
   *
   * @var string
   */
  public $network;
  /**
   * Required. Project id used to create forwarding rule.
   *
   * @var string
   */
  public $projectId;
  /**
   * Output only. The state of the PSC service automation.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Error message if the PSC service automation failed.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Output only. Forwarding rule created by the PSC service automation.
   *
   * @param string $forwardingRule
   */
  public function setForwardingRule($forwardingRule)
  {
    $this->forwardingRule = $forwardingRule;
  }
  /**
   * @return string
   */
  public function getForwardingRule()
  {
    return $this->forwardingRule;
  }
  /**
   * Output only. IP address rule created by the PSC service automation.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Required. The full name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks). [Format](https://cloud.google.com/compute/docs/referen
   * ce/rest/v1/networks/get): `projects/{project}/global/networks/{network}`.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Required. Project id used to create forwarding rule.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Output only. The state of the PSC service automation.
   *
   * Accepted values: PSC_AUTOMATION_STATE_UNSPECIFIED,
   * PSC_AUTOMATION_STATE_SUCCESSFUL, PSC_AUTOMATION_STATE_FAILED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PSCAutomationConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PSCAutomationConfig');
