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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestConversationConfig extends \Google\Model
{
  /**
   * Optional. Indicates which of the channels, 1 or 2, contains the agent. Note
   * that this must be set for conversations to be properly displayed and
   * analyzed.
   *
   * @var int
   */
  public $agentChannel;
  /**
   * Optional. An opaque, user-specified string representing a human agent who
   * handled all conversations in the import. Note that this will be overridden
   * if per-conversation metadata is provided through the `metadata_bucket_uri`.
   *
   * @var string
   */
  public $agentId;
  /**
   * Optional. Indicates which of the channels, 1 or 2, contains the agent. Note
   * that this must be set for conversations to be properly displayed and
   * analyzed.
   *
   * @var int
   */
  public $customerChannel;

  /**
   * Optional. Indicates which of the channels, 1 or 2, contains the agent. Note
   * that this must be set for conversations to be properly displayed and
   * analyzed.
   *
   * @param int $agentChannel
   */
  public function setAgentChannel($agentChannel)
  {
    $this->agentChannel = $agentChannel;
  }
  /**
   * @return int
   */
  public function getAgentChannel()
  {
    return $this->agentChannel;
  }
  /**
   * Optional. An opaque, user-specified string representing a human agent who
   * handled all conversations in the import. Note that this will be overridden
   * if per-conversation metadata is provided through the `metadata_bucket_uri`.
   *
   * @param string $agentId
   */
  public function setAgentId($agentId)
  {
    $this->agentId = $agentId;
  }
  /**
   * @return string
   */
  public function getAgentId()
  {
    return $this->agentId;
  }
  /**
   * Optional. Indicates which of the channels, 1 or 2, contains the agent. Note
   * that this must be set for conversations to be properly displayed and
   * analyzed.
   *
   * @param int $customerChannel
   */
  public function setCustomerChannel($customerChannel)
  {
    $this->customerChannel = $customerChannel;
  }
  /**
   * @return int
   */
  public function getCustomerChannel()
  {
    return $this->customerChannel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestConversationConfig::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestConversationConfig');
