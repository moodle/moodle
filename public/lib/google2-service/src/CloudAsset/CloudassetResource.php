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

namespace Google\Service\CloudAsset;

class CloudassetResource extends \Google\Model
{
  /**
   * The content of the resource, in which some sensitive fields are removed and
   * may not be present.
   *
   * @var array[]
   */
  public $data;
  /**
   * The URL of the discovery document containing the resource's JSON schema.
   * Example: `https://www.googleapis.com/discovery/v1/apis/compute/v1/rest`
   * This value is unspecified for resources that do not have an API based on a
   * discovery document, such as Cloud Bigtable.
   *
   * @var string
   */
  public $discoveryDocumentUri;
  /**
   * The JSON schema name listed in the discovery document. Example: `Project`
   * This value is unspecified for resources that do not have an API based on a
   * discovery document, such as Cloud Bigtable.
   *
   * @var string
   */
  public $discoveryName;
  /**
   * The location of the resource in Google Cloud, such as its zone and region.
   * For more information, see https://cloud.google.com/about/locations/.
   *
   * @var string
   */
  public $location;
  /**
   * The full name of the immediate parent of this resource. See [Resource Names
   * ](https://cloud.google.com/apis/design/resource_names#full_resource_name)
   * for more information. For Google Cloud assets, this value is the parent
   * resource defined in the [IAM policy
   * hierarchy](https://cloud.google.com/iam/docs/overview#policy_hierarchy).
   * Example: `//cloudresourcemanager.googleapis.com/projects/my_project_123`
   *
   * @var string
   */
  public $parent;
  /**
   * The REST URL for accessing the resource. An HTTP `GET` request using this
   * URL returns the resource itself. Example:
   * `https://cloudresourcemanager.googleapis.com/v1/projects/my-project-123`
   * This value is unspecified for resources without a REST API.
   *
   * @var string
   */
  public $resourceUrl;
  /**
   * The API version. Example: `v1`
   *
   * @var string
   */
  public $version;

  /**
   * The content of the resource, in which some sensitive fields are removed and
   * may not be present.
   *
   * @param array[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return array[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * The URL of the discovery document containing the resource's JSON schema.
   * Example: `https://www.googleapis.com/discovery/v1/apis/compute/v1/rest`
   * This value is unspecified for resources that do not have an API based on a
   * discovery document, such as Cloud Bigtable.
   *
   * @param string $discoveryDocumentUri
   */
  public function setDiscoveryDocumentUri($discoveryDocumentUri)
  {
    $this->discoveryDocumentUri = $discoveryDocumentUri;
  }
  /**
   * @return string
   */
  public function getDiscoveryDocumentUri()
  {
    return $this->discoveryDocumentUri;
  }
  /**
   * The JSON schema name listed in the discovery document. Example: `Project`
   * This value is unspecified for resources that do not have an API based on a
   * discovery document, such as Cloud Bigtable.
   *
   * @param string $discoveryName
   */
  public function setDiscoveryName($discoveryName)
  {
    $this->discoveryName = $discoveryName;
  }
  /**
   * @return string
   */
  public function getDiscoveryName()
  {
    return $this->discoveryName;
  }
  /**
   * The location of the resource in Google Cloud, such as its zone and region.
   * For more information, see https://cloud.google.com/about/locations/.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The full name of the immediate parent of this resource. See [Resource Names
   * ](https://cloud.google.com/apis/design/resource_names#full_resource_name)
   * for more information. For Google Cloud assets, this value is the parent
   * resource defined in the [IAM policy
   * hierarchy](https://cloud.google.com/iam/docs/overview#policy_hierarchy).
   * Example: `//cloudresourcemanager.googleapis.com/projects/my_project_123`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * The REST URL for accessing the resource. An HTTP `GET` request using this
   * URL returns the resource itself. Example:
   * `https://cloudresourcemanager.googleapis.com/v1/projects/my-project-123`
   * This value is unspecified for resources without a REST API.
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
  /**
   * The API version. Example: `v1`
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudassetResource::class, 'Google_Service_CloudAsset_CloudassetResource');
