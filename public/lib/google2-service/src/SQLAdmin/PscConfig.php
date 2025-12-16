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

namespace Google\Service\SQLAdmin;

class PscConfig extends \Google\Collection
{
  protected $collection_key = 'pscAutoConnections';
  /**
   * Optional. The list of consumer projects that are allow-listed for PSC
   * connections to this instance. This instance can be connected to with PSC
   * from any network in these projects. Each consumer project in this list may
   * be represented by a project number (numeric) or by a project id
   * (alphanumeric).
   *
   * @var string[]
   */
  public $allowedConsumerProjects;
  /**
   * Optional. The network attachment of the consumer network that the Private
   * Service Connect enabled Cloud SQL instance is authorized to connect via PSC
   * interface. format: projects/PROJECT/regions/REGION/networkAttachments/ID
   *
   * @var string
   */
  public $networkAttachmentUri;
  protected $pscAutoConnectionsType = PscAutoConnectionConfig::class;
  protected $pscAutoConnectionsDataType = 'array';
  /**
   * Whether PSC connectivity is enabled for this instance.
   *
   * @var bool
   */
  public $pscEnabled;

  /**
   * Optional. The list of consumer projects that are allow-listed for PSC
   * connections to this instance. This instance can be connected to with PSC
   * from any network in these projects. Each consumer project in this list may
   * be represented by a project number (numeric) or by a project id
   * (alphanumeric).
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
   * Optional. The network attachment of the consumer network that the Private
   * Service Connect enabled Cloud SQL instance is authorized to connect via PSC
   * interface. format: projects/PROJECT/regions/REGION/networkAttachments/ID
   *
   * @param string $networkAttachmentUri
   */
  public function setNetworkAttachmentUri($networkAttachmentUri)
  {
    $this->networkAttachmentUri = $networkAttachmentUri;
  }
  /**
   * @return string
   */
  public function getNetworkAttachmentUri()
  {
    return $this->networkAttachmentUri;
  }
  /**
   * Optional. The list of settings for requested Private Service Connect
   * consumer endpoints that can be used to connect to this Cloud SQL instance.
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
   * Whether PSC connectivity is enabled for this instance.
   *
   * @param bool $pscEnabled
   */
  public function setPscEnabled($pscEnabled)
  {
    $this->pscEnabled = $pscEnabled;
  }
  /**
   * @return bool
   */
  public function getPscEnabled()
  {
    return $this->pscEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscConfig::class, 'Google_Service_SQLAdmin_PscConfig');
