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

namespace Google\Service\AppHub;

class DiscoveredService extends \Google\Model
{
  /**
   * Identifier. The resource name of the discovered service. Format:
   * `"projects/{host-project-
   * id}/locations/{location}/discoveredServices/{uuid}"`
   *
   * @var string
   */
  public $name;
  protected $servicePropertiesType = ServiceProperties::class;
  protected $servicePropertiesDataType = '';
  protected $serviceReferenceType = ServiceReference::class;
  protected $serviceReferenceDataType = '';

  /**
   * Identifier. The resource name of the discovered service. Format:
   * `"projects/{host-project-
   * id}/locations/{location}/discoveredServices/{uuid}"`
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
   * Output only. Properties of an underlying compute resource that can comprise
   * a Service. These are immutable.
   *
   * @param ServiceProperties $serviceProperties
   */
  public function setServiceProperties(ServiceProperties $serviceProperties)
  {
    $this->serviceProperties = $serviceProperties;
  }
  /**
   * @return ServiceProperties
   */
  public function getServiceProperties()
  {
    return $this->serviceProperties;
  }
  /**
   * Output only. Reference to an underlying networking resource that can
   * comprise a Service. These are immutable.
   *
   * @param ServiceReference $serviceReference
   */
  public function setServiceReference(ServiceReference $serviceReference)
  {
    $this->serviceReference = $serviceReference;
  }
  /**
   * @return ServiceReference
   */
  public function getServiceReference()
  {
    return $this->serviceReference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoveredService::class, 'Google_Service_AppHub_DiscoveredService');
