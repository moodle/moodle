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

namespace Google\Service\DeveloperConnect;

class GitRepositoryLink extends \Google\Model
{
  /**
   * Optional. Allows clients to store small amounts of arbitrary data.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Required. Git Clone URI.
   *
   * @var string
   */
  public $cloneUri;
  /**
   * Output only. [Output only] Create timestamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. [Output only] Delete timestamp
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. URI to access the linked repository through the Git Proxy.
   * This field is only populated if the git proxy is enabled for the
   * connection.
   *
   * @var string
   */
  public $gitProxyUri;
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Resource name of the repository, in the format
   * `projects/locations/connections/gitRepositoryLinks`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Set to true when the connection is being set up or updated in
   * the background.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. A system-assigned unique identifier for the GitRepositoryLink.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. [Output only] Update timestamp
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
   * Required. Git Clone URI.
   *
   * @param string $cloneUri
   */
  public function setCloneUri($cloneUri)
  {
    $this->cloneUri = $cloneUri;
  }
  /**
   * @return string
   */
  public function getCloneUri()
  {
    return $this->cloneUri;
  }
  /**
   * Output only. [Output only] Create timestamp
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
   * Output only. [Output only] Delete timestamp
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
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
   * Output only. URI to access the linked repository through the Git Proxy.
   * This field is only populated if the git proxy is enabled for the
   * connection.
   *
   * @param string $gitProxyUri
   */
  public function setGitProxyUri($gitProxyUri)
  {
    $this->gitProxyUri = $gitProxyUri;
  }
  /**
   * @return string
   */
  public function getGitProxyUri()
  {
    return $this->gitProxyUri;
  }
  /**
   * Optional. Labels as key value pairs
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Resource name of the repository, in the format
   * `projects/locations/connections/gitRepositoryLinks`.
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
   * Output only. Set to true when the connection is being set up or updated in
   * the background.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. A system-assigned unique identifier for the GitRepositoryLink.
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
  /**
   * Output only. [Output only] Update timestamp
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
class_alias(GitRepositoryLink::class, 'Google_Service_DeveloperConnect_GitRepositoryLink');
