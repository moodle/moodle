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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1BuildApproval extends \Google\Model
{
  /**
   * Default enum type. This should not be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Build approval is pending.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Build approval has been approved.
   */
  public const STATE_APPROVED = 'APPROVED';
  /**
   * Build approval has been rejected.
   */
  public const STATE_REJECTED = 'REJECTED';
  /**
   * Build was cancelled while it was still pending approval.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  protected $configType = GoogleDevtoolsCloudbuildV1ApprovalConfig::class;
  protected $configDataType = '';
  protected $resultType = GoogleDevtoolsCloudbuildV1ApprovalResult::class;
  protected $resultDataType = '';
  /**
   * Output only. The state of this build's approval.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Configuration for manual approval of this build.
   *
   * @param GoogleDevtoolsCloudbuildV1ApprovalConfig $config
   */
  public function setConfig(GoogleDevtoolsCloudbuildV1ApprovalConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1ApprovalConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. Result of manual approval for this Build.
   *
   * @param GoogleDevtoolsCloudbuildV1ApprovalResult $result
   */
  public function setResult(GoogleDevtoolsCloudbuildV1ApprovalResult $result)
  {
    $this->result = $result;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1ApprovalResult
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * Output only. The state of this build's approval.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, APPROVED, REJECTED, CANCELLED
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
class_alias(GoogleDevtoolsCloudbuildV1BuildApproval::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1BuildApproval');
