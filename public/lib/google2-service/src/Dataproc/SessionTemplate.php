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

namespace Google\Service\Dataproc;

class SessionTemplate extends \Google\Model
{
  /**
   * Output only. The time when the template was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The email address of the user who created the template.
   *
   * @var string
   */
  public $creator;
  /**
   * Optional. Brief description of the template.
   *
   * @var string
   */
  public $description;
  protected $environmentConfigType = EnvironmentConfig::class;
  protected $environmentConfigDataType = '';
  protected $jupyterSessionType = JupyterConfig::class;
  protected $jupyterSessionDataType = '';
  /**
   * Optional. Labels to associate with sessions created using this template.
   * Label keys must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values can be empty, but, if
   * present, must contain 1 to 63 characters and conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a session.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Identifier. The resource name of the session template.
   *
   * @var string
   */
  public $name;
  protected $runtimeConfigType = RuntimeConfig::class;
  protected $runtimeConfigDataType = '';
  protected $sparkConnectSessionType = SparkConnectConfig::class;
  protected $sparkConnectSessionDataType = '';
  /**
   * Output only. The time the template was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. A session template UUID (Unique Universal Identifier). The
   * service generates this value when it creates the session template.
   *
   * @var string
   */
  public $uuid;

  /**
   * Output only. The time when the template was created.
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
   * Output only. The email address of the user who created the template.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Optional. Brief description of the template.
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
   * Optional. Environment configuration for session execution.
   *
   * @param EnvironmentConfig $environmentConfig
   */
  public function setEnvironmentConfig(EnvironmentConfig $environmentConfig)
  {
    $this->environmentConfig = $environmentConfig;
  }
  /**
   * @return EnvironmentConfig
   */
  public function getEnvironmentConfig()
  {
    return $this->environmentConfig;
  }
  /**
   * Optional. Jupyter session config.
   *
   * @param JupyterConfig $jupyterSession
   */
  public function setJupyterSession(JupyterConfig $jupyterSession)
  {
    $this->jupyterSession = $jupyterSession;
  }
  /**
   * @return JupyterConfig
   */
  public function getJupyterSession()
  {
    return $this->jupyterSession;
  }
  /**
   * Optional. Labels to associate with sessions created using this template.
   * Label keys must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values can be empty, but, if
   * present, must contain 1 to 63 characters and conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a session.
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
   * Required. Identifier. The resource name of the session template.
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
   * Optional. Runtime configuration for session execution.
   *
   * @param RuntimeConfig $runtimeConfig
   */
  public function setRuntimeConfig(RuntimeConfig $runtimeConfig)
  {
    $this->runtimeConfig = $runtimeConfig;
  }
  /**
   * @return RuntimeConfig
   */
  public function getRuntimeConfig()
  {
    return $this->runtimeConfig;
  }
  /**
   * Optional. Spark connect session config.
   *
   * @param SparkConnectConfig $sparkConnectSession
   */
  public function setSparkConnectSession(SparkConnectConfig $sparkConnectSession)
  {
    $this->sparkConnectSession = $sparkConnectSession;
  }
  /**
   * @return SparkConnectConfig
   */
  public function getSparkConnectSession()
  {
    return $this->sparkConnectSession;
  }
  /**
   * Output only. The time the template was last updated.
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
   * Output only. A session template UUID (Unique Universal Identifier). The
   * service generates this value when it creates the session template.
   *
   * @param string $uuid
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SessionTemplate::class, 'Google_Service_Dataproc_SessionTemplate');
