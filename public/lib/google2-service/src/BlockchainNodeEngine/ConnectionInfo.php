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

namespace Google\Service\BlockchainNodeEngine;

class ConnectionInfo extends \Google\Model
{
  protected $endpointInfoType = EndpointInfo::class;
  protected $endpointInfoDataType = '';
  /**
   * Output only. A service attachment that exposes a node, and has the
   * following format: projects/{project}/regions/{region}/serviceAttachments/{s
   * ervice_attachment_name}
   *
   * @var string
   */
  public $serviceAttachment;

  /**
   * Output only. The endpoint information through which to interact with a
   * blockchain node.
   *
   * @param EndpointInfo $endpointInfo
   */
  public function setEndpointInfo(EndpointInfo $endpointInfo)
  {
    $this->endpointInfo = $endpointInfo;
  }
  /**
   * @return EndpointInfo
   */
  public function getEndpointInfo()
  {
    return $this->endpointInfo;
  }
  /**
   * Output only. A service attachment that exposes a node, and has the
   * following format: projects/{project}/regions/{region}/serviceAttachments/{s
   * ervice_attachment_name}
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
class_alias(ConnectionInfo::class, 'Google_Service_BlockchainNodeEngine_ConnectionInfo');
