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

namespace Google\Service\Networkconnectivity;

class MulticloudDataTransferSupportedService extends \Google\Collection
{
  protected $collection_key = 'serviceConfigs';
  /**
   * Identifier. The name of the service.
   *
   * @var string
   */
  public $name;
  protected $serviceConfigsType = ServiceConfig::class;
  protected $serviceConfigsDataType = 'array';

  /**
   * Identifier. The name of the service.
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
  /**
   * Output only. The network service tier or regional endpoint supported for
   * the service.
   *
   * @param ServiceConfig[] $serviceConfigs
   */
  public function setServiceConfigs($serviceConfigs)
  {
    $this->serviceConfigs = $serviceConfigs;
  }
  /**
   * @return ServiceConfig[]
   */
  public function getServiceConfigs()
  {
    return $this->serviceConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MulticloudDataTransferSupportedService::class, 'Google_Service_Networkconnectivity_MulticloudDataTransferSupportedService');
