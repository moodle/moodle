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

namespace Google\Service\CloudShell;

class Environment extends \Google\Collection
{
  /**
   * The environment's states is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The environment is not running and can't be connected to. Starting the
   * environment will transition it to the PENDING state.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The environment is being started but is not yet ready to accept
   * connections.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The environment is running and ready to accept connections. It will
   * automatically transition back to DISABLED after a period of inactivity or
   * if another environment is started.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The environment is being deleted and can't be connected to.
   */
  public const STATE_DELETING = 'DELETING';
  protected $collection_key = 'publicKeys';
  /**
   * Required. Immutable. Full path to the Docker image used to run this
   * environment, e.g. "gcr.io/dev-con/cloud-devshell:latest".
   *
   * @var string
   */
  public $dockerImage;
  /**
   * Output only. The environment's identifier, unique among the user's
   * environments.
   *
   * @var string
   */
  public $id;
  /**
   * Immutable. Full name of this resource, in the format
   * `users/{owner_email}/environments/{environment_id}`. `{owner_email}` is the
   * email address of the user to whom this environment belongs, and
   * `{environment_id}` is the identifier of this environment. For example,
   * `users/someone@example.com/environments/default`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Public keys associated with the environment. Clients can
   * connect to this environment via SSH only if they possess a private key
   * corresponding to at least one of these public keys. Keys can be added to or
   * removed from the environment using the AddPublicKey and RemovePublicKey
   * methods.
   *
   * @var string[]
   */
  public $publicKeys;
  /**
   * Output only. Host to which clients can connect to initiate SSH sessions
   * with the environment.
   *
   * @var string
   */
  public $sshHost;
  /**
   * Output only. Port to which clients can connect to initiate SSH sessions
   * with the environment.
   *
   * @var int
   */
  public $sshPort;
  /**
   * Output only. Username that clients should use when initiating SSH sessions
   * with the environment.
   *
   * @var string
   */
  public $sshUsername;
  /**
   * Output only. Current execution state of this environment.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Host to which clients can connect to initiate HTTPS or WSS
   * connections with the environment.
   *
   * @var string
   */
  public $webHost;

  /**
   * Required. Immutable. Full path to the Docker image used to run this
   * environment, e.g. "gcr.io/dev-con/cloud-devshell:latest".
   *
   * @param string $dockerImage
   */
  public function setDockerImage($dockerImage)
  {
    $this->dockerImage = $dockerImage;
  }
  /**
   * @return string
   */
  public function getDockerImage()
  {
    return $this->dockerImage;
  }
  /**
   * Output only. The environment's identifier, unique among the user's
   * environments.
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
   * Immutable. Full name of this resource, in the format
   * `users/{owner_email}/environments/{environment_id}`. `{owner_email}` is the
   * email address of the user to whom this environment belongs, and
   * `{environment_id}` is the identifier of this environment. For example,
   * `users/someone@example.com/environments/default`.
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
   * Output only. Public keys associated with the environment. Clients can
   * connect to this environment via SSH only if they possess a private key
   * corresponding to at least one of these public keys. Keys can be added to or
   * removed from the environment using the AddPublicKey and RemovePublicKey
   * methods.
   *
   * @param string[] $publicKeys
   */
  public function setPublicKeys($publicKeys)
  {
    $this->publicKeys = $publicKeys;
  }
  /**
   * @return string[]
   */
  public function getPublicKeys()
  {
    return $this->publicKeys;
  }
  /**
   * Output only. Host to which clients can connect to initiate SSH sessions
   * with the environment.
   *
   * @param string $sshHost
   */
  public function setSshHost($sshHost)
  {
    $this->sshHost = $sshHost;
  }
  /**
   * @return string
   */
  public function getSshHost()
  {
    return $this->sshHost;
  }
  /**
   * Output only. Port to which clients can connect to initiate SSH sessions
   * with the environment.
   *
   * @param int $sshPort
   */
  public function setSshPort($sshPort)
  {
    $this->sshPort = $sshPort;
  }
  /**
   * @return int
   */
  public function getSshPort()
  {
    return $this->sshPort;
  }
  /**
   * Output only. Username that clients should use when initiating SSH sessions
   * with the environment.
   *
   * @param string $sshUsername
   */
  public function setSshUsername($sshUsername)
  {
    $this->sshUsername = $sshUsername;
  }
  /**
   * @return string
   */
  public function getSshUsername()
  {
    return $this->sshUsername;
  }
  /**
   * Output only. Current execution state of this environment.
   *
   * Accepted values: STATE_UNSPECIFIED, SUSPENDED, PENDING, RUNNING, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Host to which clients can connect to initiate HTTPS or WSS
   * connections with the environment.
   *
   * @param string $webHost
   */
  public function setWebHost($webHost)
  {
    $this->webHost = $webHost;
  }
  /**
   * @return string
   */
  public function getWebHost()
  {
    return $this->webHost;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Environment::class, 'Google_Service_CloudShell_Environment');
