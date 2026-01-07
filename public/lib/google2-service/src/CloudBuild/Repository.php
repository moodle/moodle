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

namespace Google\Service\CloudBuild;

class Repository extends \Google\Model
{
  /**
   * Optional. Allows clients to store small amounts of arbitrary data.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Server assigned timestamp for when the connection was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Immutable. Resource name of the repository, in the format
   * `projects/locations/connections/repositories`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Git Clone HTTPS URI.
   *
   * @var string
   */
  public $remoteUri;
  /**
   * Output only. Server assigned timestamp for when the connection was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. External ID of the webhook created for the repository.
   *
   * @var string
   */
  public $webhookId;

  /**
   * Optional. Allows clients to store small amounts of arbitrary data.
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
   * Output only. Server assigned timestamp for when the connection was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
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
   * Immutable. Resource name of the repository, in the format
   * `projects/locations/connections/repositories`.
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
   * Required. Git Clone HTTPS URI.
   *
   * @param string $remoteUri
   */
  public function setRemoteUri($remoteUri)
  {
    $this->remoteUri = $remoteUri;
  }
  /**
   * @return string
   */
  public function getRemoteUri()
  {
    return $this->remoteUri;
  }
  /**
   * Output only. Server assigned timestamp for when the connection was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. External ID of the webhook created for the repository.
   *
   * @param string $webhookId
   */
  public function setWebhookId($webhookId)
  {
    $this->webhookId = $webhookId;
  }
  /**
   * @return string
   */
  public function getWebhookId()
  {
    return $this->webhookId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Repository::class, 'Google_Service_CloudBuild_Repository');
