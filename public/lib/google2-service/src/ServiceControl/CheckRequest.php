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

namespace Google\Service\ServiceControl;

class CheckRequest extends \Google\Collection
{
  protected $collection_key = 'resources';
  protected $attributesType = AttributeContext::class;
  protected $attributesDataType = '';
  /**
   * Optional. Contains a comma-separated list of flags.
   *
   * @var string
   */
  public $flags;
  protected $resourcesType = ResourceInfo::class;
  protected $resourcesDataType = 'array';
  /**
   * Specifies the version of the service configuration that should be used to
   * process the request. Must not be empty. Set this field to 'latest' to
   * specify using the latest configuration.
   *
   * @var string
   */
  public $serviceConfigId;

  /**
   * Describes attributes about the operation being executed by the service.
   *
   * @param AttributeContext $attributes
   */
  public function setAttributes(AttributeContext $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return AttributeContext
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. Contains a comma-separated list of flags.
   *
   * @param string $flags
   */
  public function setFlags($flags)
  {
    $this->flags = $flags;
  }
  /**
   * @return string
   */
  public function getFlags()
  {
    return $this->flags;
  }
  /**
   * Describes the resources and the policies applied to each resource.
   *
   * @param ResourceInfo[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return ResourceInfo[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Specifies the version of the service configuration that should be used to
   * process the request. Must not be empty. Set this field to 'latest' to
   * specify using the latest configuration.
   *
   * @param string $serviceConfigId
   */
  public function setServiceConfigId($serviceConfigId)
  {
    $this->serviceConfigId = $serviceConfigId;
  }
  /**
   * @return string
   */
  public function getServiceConfigId()
  {
    return $this->serviceConfigId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckRequest::class, 'Google_Service_ServiceControl_CheckRequest');
