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

namespace Google\Service\FirebaseDataConnect;

class Connector extends \Google\Model
{
  /**
   * Optional. Stores small amounts of arbitrary data.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. [Output only] Create time stamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Mutable human-readable name. 63 character limit.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   * [AIP-154](https://google.aip.dev/154)
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The relative resource name of the connector, in the format: ```
   * projects/{project}/locations/{location}/services/{service}/connectors/{conn
   * ector} ```
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A field that if true, indicates that the system is working to
   * compile and deploy the connector.
   *
   * @var bool
   */
  public $reconciling;
  protected $sourceType = Source::class;
  protected $sourceDataType = '';
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. [Output only] Update time stamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Stores small amounts of arbitrary data.
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
   * Output only. [Output only] Create time stamp.
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
   * Optional. Mutable human-readable name. 63 character limit.
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
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   * [AIP-154](https://google.aip.dev/154)
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
   * Optional. Labels as key value pairs.
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
   * Identifier. The relative resource name of the connector, in the format: ```
   * projects/{project}/locations/{location}/services/{service}/connectors/{conn
   * ector} ```
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
   * Output only. A field that if true, indicates that the system is working to
   * compile and deploy the connector.
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
   * Required. The source files that comprise the connector.
   *
   * @param Source $source
   */
  public function setSource(Source $source)
  {
    $this->source = $source;
  }
  /**
   * @return Source
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. System-assigned, unique identifier.
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
   * Output only. [Output only] Update time stamp.
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
class_alias(Connector::class, 'Google_Service_FirebaseDataConnect_Connector');
