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

namespace Google\Service\BigQueryConnectionService;

class ConnectorConfigurationPrivateServiceConnect extends \Google\Model
{
  /**
   * Required. Network Attachment name in the format of `projects/{project}/regi
   * ons/{region}/networkAttachments/{networkattachment}`.
   *
   * @var string
   */
  public $networkAttachment;

  /**
   * Required. Network Attachment name in the format of `projects/{project}/regi
   * ons/{region}/networkAttachments/{networkattachment}`.
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
class_alias(ConnectorConfigurationPrivateServiceConnect::class, 'Google_Service_BigQueryConnectionService_ConnectorConfigurationPrivateServiceConnect');
