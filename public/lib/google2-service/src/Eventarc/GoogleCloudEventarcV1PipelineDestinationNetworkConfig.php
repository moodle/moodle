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

namespace Google\Service\Eventarc;

class GoogleCloudEventarcV1PipelineDestinationNetworkConfig extends \Google\Model
{
  /**
   * Required. Name of the NetworkAttachment that allows access to the consumer
   * VPC. Format: `projects/{PROJECT_ID}/regions/{REGION}/networkAttachments/{NE
   * TWORK_ATTACHMENT_NAME}`
   *
   * @var string
   */
  public $networkAttachment;

  /**
   * Required. Name of the NetworkAttachment that allows access to the consumer
   * VPC. Format: `projects/{PROJECT_ID}/regions/{REGION}/networkAttachments/{NE
   * TWORK_ATTACHMENT_NAME}`
   *
   * @param string $networkAttachment
   */
  public function setNetworkAttachment($networkAttachment)
  {
    $this->networkAttachment = $networkAttachment;
  }
  /**
   * @return string
   */
  public function getNetworkAttachment()
  {
    return $this->networkAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEventarcV1PipelineDestinationNetworkConfig::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineDestinationNetworkConfig');
