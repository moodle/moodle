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

namespace Google\Service\TagManager;

class Environment extends \Google\Model
{
  /**
   * Points to a user defined environment.
   */
  public const TYPE_user = 'user';
  /**
   * Points to the current live container version.
   */
  public const TYPE_live = 'live';
  /**
   * Points to the latest container version.
   */
  public const TYPE_latest = 'latest';
  /**
   * Automatically managed environment that points to a workspace preview or
   * version created by a workspace.
   */
  public const TYPE_workspace = 'workspace';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * The environment authorization code.
   *
   * @var string
   */
  public $authorizationCode;
  /**
   * The last update time-stamp for the authorization code.
   *
   * @var string
   */
  public $authorizationTimestamp;
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * Represents a link to a container version.
   *
   * @var string
   */
  public $containerVersionId;
  /**
   * The environment description. Can be set or changed only on USER type
   * environments.
   *
   * @var string
   */
  public $description;
  /**
   * Whether or not to enable debug by default for the environment.
   *
   * @var bool
   */
  public $enableDebug;
  /**
   * GTM Environment ID uniquely identifies the GTM Environment.
   *
   * @var string
   */
  public $environmentId;
  /**
   * The fingerprint of the GTM environment as computed at storage time. This
   * value is recomputed whenever the environment is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * The environment display name. Can be set or changed only on USER type
   * environments.
   *
   * @var string
   */
  public $name;
  /**
   * GTM Environment's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  /**
   * The type of this environment.
   *
   * @var string
   */
  public $type;
  /**
   * Default preview page url for the environment.
   *
   * @var string
   */
  public $url;
  /**
   * Represents a link to a quick preview of a workspace.
   *
   * @var string
   */
  public $workspaceId;

  /**
   * GTM Account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The environment authorization code.
   *
   * @param string $authorizationCode
   */
  public function setAuthorizationCode($authorizationCode)
  {
    $this->authorizationCode = $authorizationCode;
  }
  /**
   * @return string
   */
  public function getAuthorizationCode()
  {
    return $this->authorizationCode;
  }
  /**
   * The last update time-stamp for the authorization code.
   *
   * @param string $authorizationTimestamp
   */
  public function setAuthorizationTimestamp($authorizationTimestamp)
  {
    $this->authorizationTimestamp = $authorizationTimestamp;
  }
  /**
   * @return string
   */
  public function getAuthorizationTimestamp()
  {
    return $this->authorizationTimestamp;
  }
  /**
   * GTM Container ID.
   *
   * @param string $containerId
   */
  public function setContainerId($containerId)
  {
    $this->containerId = $containerId;
  }
  /**
   * @return string
   */
  public function getContainerId()
  {
    return $this->containerId;
  }
  /**
   * Represents a link to a container version.
   *
   * @param string $containerVersionId
   */
  public function setContainerVersionId($containerVersionId)
  {
    $this->containerVersionId = $containerVersionId;
  }
  /**
   * @return string
   */
  public function getContainerVersionId()
  {
    return $this->containerVersionId;
  }
  /**
   * The environment description. Can be set or changed only on USER type
   * environments.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Whether or not to enable debug by default for the environment.
   *
   * @param bool $enableDebug
   */
  public function setEnableDebug($enableDebug)
  {
    $this->enableDebug = $enableDebug;
  }
  /**
   * @return bool
   */
  public function getEnableDebug()
  {
    return $this->enableDebug;
  }
  /**
   * GTM Environment ID uniquely identifies the GTM Environment.
   *
   * @param string $environmentId
   */
  public function setEnvironmentId($environmentId)
  {
    $this->environmentId = $environmentId;
  }
  /**
   * @return string
   */
  public function getEnvironmentId()
  {
    return $this->environmentId;
  }
  /**
   * The fingerprint of the GTM environment as computed at storage time. This
   * value is recomputed whenever the environment is modified.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * The environment display name. Can be set or changed only on USER type
   * environments.
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
   * GTM Environment's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Auto generated link to the tag manager UI
   *
   * @param string $tagManagerUrl
   */
  public function setTagManagerUrl($tagManagerUrl)
  {
    $this->tagManagerUrl = $tagManagerUrl;
  }
  /**
   * @return string
   */
  public function getTagManagerUrl()
  {
    return $this->tagManagerUrl;
  }
  /**
   * The type of this environment.
   *
   * Accepted values: user, live, latest, workspace
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Default preview page url for the environment.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * Represents a link to a quick preview of a workspace.
   *
   * @param string $workspaceId
   */
  public function setWorkspaceId($workspaceId)
  {
    $this->workspaceId = $workspaceId;
  }
  /**
   * @return string
   */
  public function getWorkspaceId()
  {
    return $this->workspaceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Environment::class, 'Google_Service_TagManager_Environment');
