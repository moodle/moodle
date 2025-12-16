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

class Client extends \Google\Collection
{
  protected $collection_key = 'parameter';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * The Client ID uniquely identifies the GTM client.
   *
   * @var string
   */
  public $clientId;
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * The fingerprint of the GTM Client as computed at storage time. This value
   * is recomputed whenever the client is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Client display name.
   *
   * @var string
   */
  public $name;
  /**
   * User notes on how to apply this tag in the container.
   *
   * @var string
   */
  public $notes;
  protected $parameterType = Parameter::class;
  protected $parameterDataType = 'array';
  /**
   * Parent folder id.
   *
   * @var string
   */
  public $parentFolderId;
  /**
   * GTM client's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Priority determines relative firing order.
   *
   * @var int
   */
  public $priority;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  /**
   * Client type.
   *
   * @var string
   */
  public $type;
  /**
   * GTM Workspace ID.
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
   * The Client ID uniquely identifies the GTM client.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
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
   * The fingerprint of the GTM Client as computed at storage time. This value
   * is recomputed whenever the client is modified.
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
   * Client display name.
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
   * User notes on how to apply this tag in the container.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * The client's parameters.
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
   * Parent folder id.
   *
   * @param string $parentFolderId
   */
  public function setParentFolderId($parentFolderId)
  {
    $this->parentFolderId = $parentFolderId;
  }
  /**
   * @return string
   */
  public function getParentFolderId()
  {
    return $this->parentFolderId;
  }
  /**
   * GTM client's API relative path.
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
   * Priority determines relative firing order.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
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
   * Client type.
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
   * GTM Workspace ID.
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
class_alias(Client::class, 'Google_Service_TagManager_Client');
