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

namespace Google\Service\CloudRedis;

class PscServiceAttachment extends \Google\Model
{
  /**
   * Cluster endpoint Type is not set
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_UNSPECIFIED = 'CONNECTION_TYPE_UNSPECIFIED';
  /**
   * Cluster endpoint that will be used as for cluster topology discovery.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_DISCOVERY = 'CONNECTION_TYPE_DISCOVERY';
  /**
   * Cluster endpoint that will be used as primary endpoint to access primary.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_PRIMARY = 'CONNECTION_TYPE_PRIMARY';
  /**
   * Cluster endpoint that will be used as reader endpoint to access replicas.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_READER = 'CONNECTION_TYPE_READER';
  /**
   * Output only. Type of a PSC connection targeting this service attachment.
   *
   * @var string
   */
  public $connectionType;
  /**
   * Output only. Service attachment URI which your self-created PscConnection
   * should use as target
   *
   * @var string
   */
  public $serviceAttachment;

  /**
   * Output only. Type of a PSC connection targeting this service attachment.
   *
   * Accepted values: CONNECTION_TYPE_UNSPECIFIED, CONNECTION_TYPE_DISCOVERY,
   * CONNECTION_TYPE_PRIMARY, CONNECTION_TYPE_READER
   *
   * @param self::CONNECTION_TYPE_* $connectionType
   */
  public function setConnectionType($connectionType)
  {
    $this->connectionType = $connectionType;
  }
  /**
   * @return self::CONNECTION_TYPE_*
   */
  public function getConnectionType()
  {
    return $this->connectionType;
  }
  /**
   * Output only. Service attachment URI which your self-created PscConnection
   * should use as target
   *
   * @param string $serviceAttachment
   */
  public function setServiceAttachment($serviceAttachment)
  {
    $this->serviceAttachment = $serviceAttachment;
  }
  /**
   * @return string
   */
  public function getServiceAttachment()
  {
    return $this->serviceAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscServiceAttachment::class, 'Google_Service_CloudRedis_PscServiceAttachment');
