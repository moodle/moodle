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

namespace Google\Service\Compute;

class UrlMapsValidateRequest extends \Google\Collection
{
  protected $collection_key = 'loadBalancingSchemes';
  /**
   * Specifies the load balancer type(s) this validation request is for.
   * UseEXTERNAL_MANAGED for global external Application Load Balancers and
   * regional external Application Load Balancers. Use EXTERNAL for classic
   * Application Load Balancers.
   *
   * Use INTERNAL_MANAGED for internal Application Load Balancers. For more
   * information, refer to Choosing a load balancer.
   *
   * If unspecified, the load balancing scheme will be inferred from the backend
   * service resources this URL map references. If that can not be inferred (for
   * example, this URL map only references backend buckets, or this Url map is
   * for rewrites and redirects only and doesn't reference any
   * backends),EXTERNAL will be used as the default type.
   *
   * If specified, the scheme(s) must not conflict with the load balancing
   * scheme of the backend service resources this Url map references.
   *
   * @var string[]
   */
  public $loadBalancingSchemes;
  protected $resourceType = UrlMap::class;
  protected $resourceDataType = '';

  /**
   * Specifies the load balancer type(s) this validation request is for.
   * UseEXTERNAL_MANAGED for global external Application Load Balancers and
   * regional external Application Load Balancers. Use EXTERNAL for classic
   * Application Load Balancers.
   *
   * Use INTERNAL_MANAGED for internal Application Load Balancers. For more
   * information, refer to Choosing a load balancer.
   *
   * If unspecified, the load balancing scheme will be inferred from the backend
   * service resources this URL map references. If that can not be inferred (for
   * example, this URL map only references backend buckets, or this Url map is
   * for rewrites and redirects only and doesn't reference any
   * backends),EXTERNAL will be used as the default type.
   *
   * If specified, the scheme(s) must not conflict with the load balancing
   * scheme of the backend service resources this Url map references.
   *
   * @param string[] $loadBalancingSchemes
   */
  public function setLoadBalancingSchemes($loadBalancingSchemes)
  {
    $this->loadBalancingSchemes = $loadBalancingSchemes;
  }
  /**
   * @return string[]
   */
  public function getLoadBalancingSchemes()
  {
    return $this->loadBalancingSchemes;
  }
  /**
   * Content of the UrlMap to be validated.
   *
   * @param UrlMap $resource
   */
  public function setResource(UrlMap $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return UrlMap
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlMapsValidateRequest::class, 'Google_Service_Compute_UrlMapsValidateRequest');
