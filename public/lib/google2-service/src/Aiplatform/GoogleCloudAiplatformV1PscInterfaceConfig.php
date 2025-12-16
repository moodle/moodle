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

class GoogleCloudAiplatformV1PscInterfaceConfig extends \Google\Collection
{
  protected $collection_key = 'dnsPeeringConfigs';
  protected $dnsPeeringConfigsType = GoogleCloudAiplatformV1DnsPeeringConfig::class;
  protected $dnsPeeringConfigsDataType = 'array';
  /**
   * Optional. The name of the Compute Engine [network
   * attachment](https://cloud.google.com/vpc/docs/about-network-attachments) to
   * attach to the resource within the region and user project. To specify this
   * field, you must have already [created a network attachment]
   * (https://cloud.google.com/vpc/docs/create-manage-network-
   * attachments#create-network-attachments). This field is only used for
   * resources using PSC-I.
   *
   * @var string
   */
  public $networkAttachment;

  /**
   * Optional. DNS peering configurations. When specified, Vertex AI will
   * attempt to configure DNS peering zones in the tenant project VPC to resolve
   * the specified domains using the target network's Cloud DNS. The user must
   * grant the dns.peer role to the Vertex AI Service Agent on the target
   * project.
   *
   * @param GoogleCloudAiplatformV1DnsPeeringConfig[] $dnsPeeringConfigs
   */
  public function setDnsPeeringConfigs($dnsPeeringConfigs)
  {
    $this->dnsPeeringConfigs = $dnsPeeringConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1DnsPeeringConfig[]
   */
  public function getDnsPeeringConfigs()
  {
    return $this->dnsPeeringConfigs;
  }
  /**
   * Optional. The name of the Compute Engine [network
   * attachment](https://cloud.google.com/vpc/docs/about-network-attachments) to
   * attach to the resource within the region and user project. To specify this
   * field, you must have already [created a network attachment]
   * (https://cloud.google.com/vpc/docs/create-manage-network-
   * attachments#create-network-attachments). This field is only used for
   * resources using PSC-I.
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
class_alias(GoogleCloudAiplatformV1PscInterfaceConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PscInterfaceConfig');
