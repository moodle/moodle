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

namespace Google\Service\Storage;

class Channel extends \Google\Model
{
  /**
   * The address where notifications are delivered for this channel.
   *
   * @var string
   */
  public $address;
  /**
   * Date and time of notification channel expiration, expressed as a Unix
   * timestamp, in milliseconds. Optional.
   *
   * @var string
   */
  public $expiration;
  /**
   * A UUID or similar unique string that identifies this channel.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies this as a notification channel used to watch for changes to a
   * resource, which is "api#channel".
   *
   * @var string
   */
  public $kind;
  /**
   * Additional parameters controlling delivery channel behavior. Optional.
   *
   * @var string[]
   */
  public $params;
  /**
   * A Boolean value to indicate whether payload is wanted. Optional.
   *
   * @var bool
   */
  public $payload;
  /**
   * An opaque ID that identifies the resource being watched on this channel.
   * Stable across different API versions.
   *
   * @var string
   */
  public $resourceId;
  /**
   * A version-specific identifier for the watched resource.
   *
   * @var string
   */
  public $resourceUri;
  /**
   * An arbitrary string delivered to the target address with each notification
   * delivered over this channel. Optional.
   *
   * @var string
   */
  public $token;
  /**
   * The type of delivery mechanism used for this channel.
   *
   * @var string
   */
  public $type;

  /**
   * The address where notifications are delivered for this channel.
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
   * Date and time of notification channel expiration, expressed as a Unix
   * timestamp, in milliseconds. Optional.
   *
   * @param string $expiration
   */
  public function setExpiration($expiration)
  {
    $this->expiration = $expiration;
  }
  /**
   * @return string
   */
  public function getExpiration()
  {
    return $this->expiration;
  }
  /**
   * A UUID or similar unique string that identifies this channel.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies this as a notification channel used to watch for changes to a
   * resource, which is "api#channel".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Additional parameters controlling delivery channel behavior. Optional.
   *
   * @param string[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return string[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * A Boolean value to indicate whether payload is wanted. Optional.
   *
   * @param bool $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return bool
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * An opaque ID that identifies the resource being watched on this channel.
   * Stable across different API versions.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * A version-specific identifier for the watched resource.
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * An arbitrary string delivered to the target address with each notification
   * delivered over this channel. Optional.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * The type of delivery mechanism used for this channel.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Channel::class, 'Google_Service_Storage_Channel');
