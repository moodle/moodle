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

namespace Google\Service\Connectors;

class ProvisionedResource extends \Google\Model
{
  /**
   * Type of the resource. This can be either a GCP resource or a custom one
   * (e.g. another cloud provider's VM). For GCP compute resources use singular
   * form of the names listed in GCP compute API documentation
   * (https://cloud.google.com/compute/docs/reference/rest/v1/), prefixed with
   * 'compute-', for example: 'compute-instance', 'compute-disk', 'compute-
   * autoscaler'.
   *
   * @var string
   */
  public $resourceType;
  /**
   * URL identifying the resource, e.g.
   * "https://www.googleapis.com/compute/v1/projects/...)".
   *
   * @var string
   */
  public $resourceUrl;

  /**
   * Type of the resource. This can be either a GCP resource or a custom one
   * (e.g. another cloud provider's VM). For GCP compute resources use singular
   * form of the names listed in GCP compute API documentation
   * (https://cloud.google.com/compute/docs/reference/rest/v1/), prefixed with
   * 'compute-', for example: 'compute-instance', 'compute-disk', 'compute-
   * autoscaler'.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * URL identifying the resource, e.g.
   * "https://www.googleapis.com/compute/v1/projects/...)".
   *
   * @param string $resourceUrl
   */
  public function setResourceUrl($resourceUrl)
  {
    $this->resourceUrl = $resourceUrl;
  }
  /**
   * @return string
   */
  public function getResourceUrl()
  {
    return $this->resourceUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisionedResource::class, 'Google_Service_Connectors_ProvisionedResource');
