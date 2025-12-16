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

namespace Google\Service\MigrationCenterAPI;

class Relation extends \Google\Model
{
  /**
   * Default value.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * DBDeployment -> Database
   */
  public const TYPE_LOGICAL_DATABASE = 'LOGICAL_DATABASE';
  /**
   * A relation between a machine/VM and the database deployment it hosts.
   */
  public const TYPE_DATABASE_DEPLOYMENT_HOSTING_SERVER = 'DATABASE_DEPLOYMENT_HOSTING_SERVER';
  /**
   * Output only. The timestamp when the relation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The destination asset name in the relation.
   *
   * @var string
   */
  public $dstAsset;
  /**
   * Output only. Identifier. The identifier of the relation.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The source asset name in the relation.
   *
   * @var string
   */
  public $srcAsset;
  /**
   * Optional. The type of the relation.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The timestamp when the relation was created.
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
   * Output only. The destination asset name in the relation.
   *
   * @param string $dstAsset
   */
  public function setDstAsset($dstAsset)
  {
    $this->dstAsset = $dstAsset;
  }
  /**
   * @return string
   */
  public function getDstAsset()
  {
    return $this->dstAsset;
  }
  /**
   * Output only. Identifier. The identifier of the relation.
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
   * Output only. The source asset name in the relation.
   *
   * @param string $srcAsset
   */
  public function setSrcAsset($srcAsset)
  {
    $this->srcAsset = $srcAsset;
  }
  /**
   * @return string
   */
  public function getSrcAsset()
  {
    return $this->srcAsset;
  }
  /**
   * Optional. The type of the relation.
   *
   * Accepted values: TYPE_UNSPECIFIED, LOGICAL_DATABASE,
   * DATABASE_DEPLOYMENT_HOSTING_SERVER
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Relation::class, 'Google_Service_MigrationCenterAPI_Relation');
