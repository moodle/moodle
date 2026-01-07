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

namespace Google\Service\ServiceDirectory;

class Service extends \Google\Collection
{
  protected $collection_key = 'endpoints';
  /**
   * Optional. Annotations for the service. This data can be consumed by service
   * clients. Restrictions: * The entire annotations dictionary may contain up
   * to 2000 characters, spread accoss all key-value pairs. Annotations that go
   * beyond this limit are rejected * Valid annotation keys have two segments:
   * an optional prefix and name, separated by a slash (/). The name segment is
   * required and must be 63 characters or less, beginning and ending with an
   * alphanumeric character ([a-z0-9A-Z]) with dashes (-), underscores (_), dots
   * (.), and alphanumerics between. The prefix is optional. If specified, the
   * prefix must be a DNS subdomain: a series of DNS labels separated by dots
   * (.), not longer than 253 characters in total, followed by a slash (/).
   * Annotations that fails to meet these requirements are rejected Note: This
   * field is equivalent to the `metadata` field in the v1beta1 API. They have
   * the same syntax and read/write to the same location in Service Directory.
   *
   * @var string[]
   */
  public $annotations;
  protected $endpointsType = Endpoint::class;
  protected $endpointsDataType = 'array';
  /**
   * Immutable. The resource name for the service in the format
   * `projects/locations/namespaces/services`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The globally unique identifier of the service in the UUID4
   * format.
   *
   * @var string
   */
  public $uid;

  /**
   * Optional. Annotations for the service. This data can be consumed by service
   * clients. Restrictions: * The entire annotations dictionary may contain up
   * to 2000 characters, spread accoss all key-value pairs. Annotations that go
   * beyond this limit are rejected * Valid annotation keys have two segments:
   * an optional prefix and name, separated by a slash (/). The name segment is
   * required and must be 63 characters or less, beginning and ending with an
   * alphanumeric character ([a-z0-9A-Z]) with dashes (-), underscores (_), dots
   * (.), and alphanumerics between. The prefix is optional. If specified, the
   * prefix must be a DNS subdomain: a series of DNS labels separated by dots
   * (.), not longer than 253 characters in total, followed by a slash (/).
   * Annotations that fails to meet these requirements are rejected Note: This
   * field is equivalent to the `metadata` field in the v1beta1 API. They have
   * the same syntax and read/write to the same location in Service Directory.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. Endpoints associated with this service. Returned on
   * LookupService.ResolveService. Control plane clients should use
   * RegistrationService.ListEndpoints.
   *
   * @param Endpoint[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return Endpoint[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Immutable. The resource name for the service in the format
   * `projects/locations/namespaces/services`.
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
   * Output only. The globally unique identifier of the service in the UUID4
   * format.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Service::class, 'Google_Service_ServiceDirectory_Service');
