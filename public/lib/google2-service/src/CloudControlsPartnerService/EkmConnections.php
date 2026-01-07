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

namespace Google\Service\CloudControlsPartnerService;

class EkmConnections extends \Google\Collection
{
  protected $collection_key = 'ekmConnections';
  protected $ekmConnectionsType = EkmConnection::class;
  protected $ekmConnectionsDataType = 'array';
  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}/ekmConnections`
   *
   * @var string
   */
  public $name;

  /**
   * The EKM connections associated with the workload
   *
   * @param EkmConnection[] $ekmConnections
   */
  public function setEkmConnections($ekmConnections)
  {
    $this->ekmConnections = $ekmConnections;
  }
  /**
   * @return EkmConnection[]
   */
  public function getEkmConnections()
  {
    return $this->ekmConnections;
  }
  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}/ekmConnections`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EkmConnections::class, 'Google_Service_CloudControlsPartnerService_EkmConnections');
