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

class HmacKeyMetadata extends \Google\Model
{
  /**
   * The ID of the HMAC Key.
   *
   * @var string
   */
  public $accessId;
  /**
   * HTTP 1.1 Entity tag for the HMAC key.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID of the HMAC key, including the Project ID and the Access ID.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of item this is. For HMAC Key metadata, this is always
   * storage#hmacKeyMetadata.
   *
   * @var string
   */
  public $kind;
  /**
   * Project ID owning the service account to which the key authenticates.
   *
   * @var string
   */
  public $projectId;
  /**
   * The link to this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The email address of the key's associated service account.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * The state of the key. Can be one of ACTIVE, INACTIVE, or DELETED.
   *
   * @var string
   */
  public $state;
  /**
   * The creation time of the HMAC key in RFC 3339 format.
   *
   * @var string
   */
  public $timeCreated;
  /**
   * The last modification time of the HMAC key metadata in RFC 3339 format.
   *
   * @var string
   */
  public $updated;

  /**
   * The ID of the HMAC Key.
   *
   * @param string $accessId
   */
  public function setAccessId($accessId)
  {
    $this->accessId = $accessId;
  }
  /**
   * @return string
   */
  public function getAccessId()
  {
    return $this->accessId;
  }
  /**
   * HTTP 1.1 Entity tag for the HMAC key.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The ID of the HMAC key, including the Project ID and the Access ID.
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
   * The kind of item this is. For HMAC Key metadata, this is always
   * storage#hmacKeyMetadata.
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
   * Project ID owning the service account to which the key authenticates.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * The link to this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The email address of the key's associated service account.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * The state of the key. Can be one of ACTIVE, INACTIVE, or DELETED.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The creation time of the HMAC key in RFC 3339 format.
   *
   * @param string $timeCreated
   */
  public function setTimeCreated($timeCreated)
  {
    $this->timeCreated = $timeCreated;
  }
  /**
   * @return string
   */
  public function getTimeCreated()
  {
    return $this->timeCreated;
  }
  /**
   * The last modification time of the HMAC key metadata in RFC 3339 format.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HmacKeyMetadata::class, 'Google_Service_Storage_HmacKeyMetadata');
