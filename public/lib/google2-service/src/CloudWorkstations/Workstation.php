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

namespace Google\Service\CloudWorkstations;

class Workstation extends \Google\Model
{
  /**
   * Do not use.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The workstation is not yet ready to accept requests from users but will be
   * soon.
   */
  public const STATE_STATE_STARTING = 'STATE_STARTING';
  /**
   * The workstation is ready to accept requests from users.
   */
  public const STATE_STATE_RUNNING = 'STATE_RUNNING';
  /**
   * The workstation is being stopped.
   */
  public const STATE_STATE_STOPPING = 'STATE_STOPPING';
  /**
   * The workstation is stopped and will not be able to receive requests until
   * it is started.
   */
  public const STATE_STATE_STOPPED = 'STATE_STOPPED';
  /**
   * Optional. Client-specified annotations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Time when this workstation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time when this workstation was soft-deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. Human-readable name for this workstation.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Environment variables passed to the workstation container's
   * entrypoint.
   *
   * @var string[]
   */
  public $env;
  /**
   * Optional. Checksum computed by the server. May be sent on update and delete
   * requests to make sure that the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Host to which clients can send HTTPS traffic that will be
   * received by the workstation. Authorized traffic will be received to the
   * workstation as HTTP on port 80. To send traffic to a different port,
   * clients may prefix the host with the destination port in the format
   * `{port}-{host}`.
   *
   * @var string
   */
  public $host;
  /**
   * Output only. The name of the Google Cloud KMS encryption key used to
   * encrypt this workstation. The KMS key can only be configured in the
   * WorkstationConfig. The expected format is
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Optional. [Labels](https://cloud.google.com/workstations/docs/label-
   * resources) that are applied to the workstation and that are also propagated
   * to the underlying Compute Engine resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Full name of this workstation.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Indicates whether this workstation is currently being updated
   * to match its intended state.
   *
   * @var bool
   */
  public $reconciling;
  protected $runtimeHostType = RuntimeHost::class;
  protected $runtimeHostDataType = '';
  /**
   * Optional. The source workstation from which this workstation's persistent
   * directories were cloned on creation.
   *
   * @var string
   */
  public $sourceWorkstation;
  /**
   * Output only. Time when this workstation was most recently successfully
   * started, regardless of the workstation's initial state.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. Current state of the workstation.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. A system-assigned unique identifier for this workstation.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time when this workstation was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Client-specified annotations.
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
   * Output only. Time when this workstation was created.
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
   * Output only. Time when this workstation was soft-deleted.
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
   * Optional. Human-readable name for this workstation.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Environment variables passed to the workstation container's
   * entrypoint.
   *
   * @param string[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return string[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Optional. Checksum computed by the server. May be sent on update and delete
   * requests to make sure that the client has an up-to-date value before
   * proceeding.
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
   * Output only. Host to which clients can send HTTPS traffic that will be
   * received by the workstation. Authorized traffic will be received to the
   * workstation as HTTP on port 80. To send traffic to a different port,
   * clients may prefix the host with the destination port in the format
   * `{port}-{host}`.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Output only. The name of the Google Cloud KMS encryption key used to
   * encrypt this workstation. The KMS key can only be configured in the
   * WorkstationConfig. The expected format is
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Optional. [Labels](https://cloud.google.com/workstations/docs/label-
   * resources) that are applied to the workstation and that are also propagated
   * to the underlying Compute Engine resources.
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
   * Identifier. Full name of this workstation.
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
   * Output only. Indicates whether this workstation is currently being updated
   * to match its intended state.
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
   * Optional. Output only. Runtime host for the workstation when in
   * STATE_RUNNING.
   *
   * @param RuntimeHost $runtimeHost
   */
  public function setRuntimeHost(RuntimeHost $runtimeHost)
  {
    $this->runtimeHost = $runtimeHost;
  }
  /**
   * @return RuntimeHost
   */
  public function getRuntimeHost()
  {
    return $this->runtimeHost;
  }
  /**
   * Optional. The source workstation from which this workstation's persistent
   * directories were cloned on creation.
   *
   * @param string $sourceWorkstation
   */
  public function setSourceWorkstation($sourceWorkstation)
  {
    $this->sourceWorkstation = $sourceWorkstation;
  }
  /**
   * @return string
   */
  public function getSourceWorkstation()
  {
    return $this->sourceWorkstation;
  }
  /**
   * Output only. Time when this workstation was most recently successfully
   * started, regardless of the workstation's initial state.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. Current state of the workstation.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_STARTING, STATE_RUNNING,
   * STATE_STOPPING, STATE_STOPPED
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
   * Output only. A system-assigned unique identifier for this workstation.
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
   * Output only. Time when this workstation was most recently updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Workstation::class, 'Google_Service_CloudWorkstations_Workstation');
