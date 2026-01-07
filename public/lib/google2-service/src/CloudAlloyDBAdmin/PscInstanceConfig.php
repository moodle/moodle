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

namespace Google\Service\CloudAlloyDBAdmin;

class PscInstanceConfig extends \Google\Collection
{
  protected $collection_key = 'pscInterfaceConfigs';
  /**
   * Optional. List of consumer projects that are allowed to create PSC
   * endpoints to service-attachments to this instance.
   *
   * @var string[]
   */
  public $allowedConsumerProjects;
  protected $pscAutoConnectionsType = PscAutoConnectionConfig::class;
  protected $pscAutoConnectionsDataType = 'array';
  /**
   * Output only. The DNS name of the instance for PSC connectivity. Name
   * convention: ...alloydb-psc.goog
   *
   * @var string
   */
  public $pscDnsName;
  protected $pscInterfaceConfigsType = PscInterfaceConfig::class;
  protected $pscInterfaceConfigsDataType = 'array';
  /**
   * Output only. The service attachment created when Private Service Connect
   * (PSC) is enabled for the instance. The name of the resource will be in the
   * format of `projects//regions//serviceAttachments/`
   *
   * @var string
   */
  public $serviceAttachmentLink;

  /**
   * Optional. List of consumer projects that are allowed to create PSC
   * endpoints to service-attachments to this instance.
   *
   * @param string[] $allowedConsumerProjects
   */
  public function setAllowedConsumerProjects($allowedConsumerProjects)
  {
    $this->allowedConsumerProjects = $allowedConsumerProjects;
  }
  /**
   * @return string[]
   */
  public function getAllowedConsumerProjects()
  {
    return $this->allowedConsumerProjects;
  }
  /**
   * Optional. Configurations for setting up PSC service automation.
   *
   * @param PscAutoConnectionConfig[] $pscAutoConnections
   */
  public function setPscAutoConnections($pscAutoConnections)
  {
    $this->pscAutoConnections = $pscAutoConnections;
  }
  /**
   * @return PscAutoConnectionConfig[]
   */
  public function getPscAutoConnections()
  {
    return $this->pscAutoConnections;
  }
  /**
   * Output only. The DNS name of the instance for PSC connectivity. Name
   * convention: ...alloydb-psc.goog
   *
   * @param string $pscDnsName
   */
  public function setPscDnsName($pscDnsName)
  {
    $this->pscDnsName = $pscDnsName;
  }
  /**
   * @return string
   */
  public function getPscDnsName()
  {
    return $this->pscDnsName;
  }
  /**
   * Optional. Configurations for setting up PSC interfaces attached to the
   * instance which are used for outbound connectivity. Only primary instances
   * can have PSC interface attached. Currently we only support 0 or 1 PSC
   * interface.
   *
   * @param PscInterfaceConfig[] $pscInterfaceConfigs
   */
  public function setPscInterfaceConfigs($pscInterfaceConfigs)
  {
    $this->pscInterfaceConfigs = $pscInterfaceConfigs;
  }
  /**
   * @return PscInterfaceConfig[]
   */
  public function getPscInterfaceConfigs()
  {
    return $this->pscInterfaceConfigs;
  }
  /**
   * Output only. The service attachment created when Private Service Connect
   * (PSC) is enabled for the instance. The name of the resource will be in the
   * format of `projects//regions//serviceAttachments/`
   *
   * @param string $serviceAttachmentLink
   */
  public function setServiceAttachmentLink($serviceAttachmentLink)
  {
    $this->serviceAttachmentLink = $serviceAttachmentLink;
  }
  /**
   * @return string
   */
  public function getServiceAttachmentLink()
  {
    return $this->serviceAttachmentLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscInstanceConfig::class, 'Google_Service_CloudAlloyDBAdmin_PscInstanceConfig');
