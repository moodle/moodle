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

class GtagConfig extends \Google\Collection
{
  protected $collection_key = 'parameter';
  /**
   * Google tag account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * Google tag container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * The fingerprint of the Google tag config as computed at storage time. This
   * value is recomputed whenever the config is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * The ID uniquely identifies the Google tag config.
   *
   * @var string
   */
  public $gtagConfigId;
  protected $parameterType = Parameter::class;
  protected $parameterDataType = 'array';
  /**
   * Google tag config's API relative path.
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
   * Google tag config type.
   *
   * @var string
   */
  public $type;
  /**
   * Google tag workspace ID. Only used by GTM containers. Set to 0 otherwise.
   *
   * @var string
   */
  public $workspaceId;

  /**
   * Google tag account ID.
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
   * Google tag container ID.
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
   * The fingerprint of the Google tag config as computed at storage time. This
   * value is recomputed whenever the config is modified.
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
   * The ID uniquely identifies the Google tag config.
   *
   * @param string $gtagConfigId
   */
  public function setGtagConfigId($gtagConfigId)
  {
    $this->gtagConfigId = $gtagConfigId;
  }
  /**
   * @return string
   */
  public function getGtagConfigId()
  {
    return $this->gtagConfigId;
  }
  /**
   * The Google tag config's parameters.
   *
   * @param Parameter[] $parameter
   */
  public function setParameter($parameter)
  {
    $this->parameter = $parameter;
  }
  /**
   * @return Parameter[]
   */
  public function getParameter()
  {
    return $this->parameter;
  }
  /**
   * Google tag config's API relative path.
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
   * Google tag config type.
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
  /**
   * Google tag workspace ID. Only used by GTM containers. Set to 0 otherwise.
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
class_alias(GtagConfig::class, 'Google_Service_TagManager_GtagConfig');
