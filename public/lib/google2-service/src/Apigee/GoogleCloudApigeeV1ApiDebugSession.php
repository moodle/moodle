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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ApiDebugSession extends \Google\Model
{
  /**
   * The revision ID of the deployed API proxy.
   *
   * @var string
   */
  public $apiProxyRevisionId;
  /**
   * The first transaction creation timestamp in millisecond, recorded by UAP.
   *
   * @var string
   */
  public $createTime;
  /**
   * The environment ID of the deployed API proxy.
   *
   * @var string
   */
  public $environmentId;
  /**
   * The debug session ID.
   *
   * @var string
   */
  public $id;

  /**
   * The revision ID of the deployed API proxy.
   *
   * @param string $apiProxyRevisionId
   */
  public function setApiProxyRevisionId($apiProxyRevisionId)
  {
    $this->apiProxyRevisionId = $apiProxyRevisionId;
  }
  /**
   * @return string
   */
  public function getApiProxyRevisionId()
  {
    return $this->apiProxyRevisionId;
  }
  /**
   * The first transaction creation timestamp in millisecond, recorded by UAP.
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
   * The environment ID of the deployed API proxy.
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
   * The debug session ID.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ApiDebugSession::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ApiDebugSession');
