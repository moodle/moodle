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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaSearchIntegrationsResponseIntegrationSearchResult extends \Google\Model
{
  /**
   * Default.
   */
  public const STATUS_INTEGRATION_STATE_UNSPECIFIED = 'INTEGRATION_STATE_UNSPECIFIED';
  /**
   * Draft.
   */
  public const STATUS_DRAFT = 'DRAFT';
  /**
   * Active.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * Archived.
   */
  public const STATUS_ARCHIVED = 'ARCHIVED';
  /**
   * Snapshot.
   */
  public const STATUS_SNAPSHOT = 'SNAPSHOT';
  /**
   * Output only. The create time of the integration version.
   *
   * @var string
   */
  public $createTime;
  /**
   * The creator of the integration version.
   *
   * @var string
   */
  public $creator;
  /**
   * The description of the integration version.
   *
   * @var string
   */
  public $description;
  /**
   * The integration id.
   *
   * @var string
   */
  public $id;
  /**
   * The integration document metadata.
   *
   * @var string
   */
  public $name;
  /**
   * The region of the integration version.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. The status of the integration version.
   *
   * @var string
   */
  public $status;
  /**
   * The version of the integration version.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The create time of the integration version.
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
   * The creator of the integration version.
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
   * The description of the integration version.
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
   * The integration id.
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
   * The integration document metadata.
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
   * The region of the integration version.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Output only. The status of the integration version.
   *
   * Accepted values: INTEGRATION_STATE_UNSPECIFIED, DRAFT, ACTIVE, ARCHIVED,
   * SNAPSHOT
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The version of the integration version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaSearchIntegrationsResponseIntegrationSearchResult::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaSearchIntegrationsResponseIntegrationSearchResult');
