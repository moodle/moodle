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

class Endpoint extends \Google\Model
{
  /**
   * Optional. An IPv4 or IPv6 address. Service Directory rejects bad addresses
   * like: * `8.8.8` * `8.8.8.8:53` * `test:bad:address` * `[::1]` *
   * `[::1]:8080` Limited to 45 characters.
   *
   * @var string
   */
  public $address;
  /**
   * Optional. Annotations for the endpoint. This data can be consumed by
   * service clients. Restrictions: * The entire annotations dictionary may
   * contain up to 512 characters, spread accoss all key-value pairs.
   * Annotations that go beyond this limit are rejected * Valid annotation keys
   * have two segments: an optional prefix and name, separated by a slash (/).
   * The name segment is required and must be 63 characters or less, beginning
   * and ending with an alphanumeric character ([a-z0-9A-Z]) with dashes (-),
   * underscores (_), dots (.), and alphanumerics between. The prefix is
   * optional. If specified, the prefix must be a DNS subdomain: a series of DNS
   * labels separated by dots (.), not longer than 253 characters in total,
   * followed by a slash (/) Annotations that fails to meet these requirements
   * are rejected. Note: This field is equivalent to the `metadata` field in the
   * v1beta1 API. They have the same syntax and read/write to the same location
   * in Service Directory.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Immutable. The resource name for the endpoint in the format
   * `projects/locations/namespaces/services/endpoints`.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The Google Compute Engine network (VPC) of the endpoint in the
   * format `projects//locations/global/networks`. The project must be specified
   * by project number (project id is rejected). Incorrectly formatted networks
   * are rejected, we also check to make sure that you have the
   * servicedirectory.networks.attach permission on the project specified.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. Service Directory rejects values outside of `[0, 65535]`.
   *
   * @var int
   */
  public $port;
  /**
   * Output only. The globally unique identifier of the endpoint in the UUID4
   * format.
   *
   * @var string
   */
  public $uid;

  /**
   * Optional. An IPv4 or IPv6 address. Service Directory rejects bad addresses
   * like: * `8.8.8` * `8.8.8.8:53` * `test:bad:address` * `[::1]` *
   * `[::1]:8080` Limited to 45 characters.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Optional. Annotations for the endpoint. This data can be consumed by
   * service clients. Restrictions: * The entire annotations dictionary may
   * contain up to 512 characters, spread accoss all key-value pairs.
   * Annotations that go beyond this limit are rejected * Valid annotation keys
   * have two segments: an optional prefix and name, separated by a slash (/).
   * The name segment is required and must be 63 characters or less, beginning
   * and ending with an alphanumeric character ([a-z0-9A-Z]) with dashes (-),
   * underscores (_), dots (.), and alphanumerics between. The prefix is
   * optional. If specified, the prefix must be a DNS subdomain: a series of DNS
   * labels separated by dots (.), not longer than 253 characters in total,
   * followed by a slash (/) Annotations that fails to meet these requirements
   * are rejected. Note: This field is equivalent to the `metadata` field in the
   * v1beta1 API. They have the same syntax and read/write to the same location
   * in Service Directory.
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
   * Immutable. The resource name for the endpoint in the format
   * `projects/locations/namespaces/services/endpoints`.
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
   * Immutable. The Google Compute Engine network (VPC) of the endpoint in the
   * format `projects//locations/global/networks`. The project must be specified
   * by project number (project id is rejected). Incorrectly formatted networks
   * are rejected, we also check to make sure that you have the
   * servicedirectory.networks.attach permission on the project specified.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. Service Directory rejects values outside of `[0, 65535]`.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Output only. The globally unique identifier of the endpoint in the UUID4
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
class_alias(Endpoint::class, 'Google_Service_ServiceDirectory_Endpoint');
