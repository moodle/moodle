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

namespace Google\Service\CertificateManager;

class CertificateMap extends \Google\Collection
{
  protected $collection_key = 'gclbTargets';
  /**
   * Output only. The creation timestamp of a Certificate Map.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. One or more paragraphs of text description of a certificate map.
   *
   * @var string
   */
  public $description;
  protected $gclbTargetsType = GclbTarget::class;
  protected $gclbTargetsDataType = 'array';
  /**
   * Optional. Set of labels associated with a Certificate Map.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. A user-defined name of the Certificate Map. Certificate Map
   * names must be unique globally and match pattern
   * `projects/locations/certificateMaps`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The update timestamp of a Certificate Map.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation timestamp of a Certificate Map.
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
   * Optional. One or more paragraphs of text description of a certificate map.
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
   * Output only. A list of GCLB targets that use this Certificate Map. A Target
   * Proxy is only present on this list if it's attached to a Forwarding Rule.
   *
   * @param GclbTarget[] $gclbTargets
   */
  public function setGclbTargets($gclbTargets)
  {
    $this->gclbTargets = $gclbTargets;
  }
  /**
   * @return GclbTarget[]
   */
  public function getGclbTargets()
  {
    return $this->gclbTargets;
  }
  /**
   * Optional. Set of labels associated with a Certificate Map.
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
   * Identifier. A user-defined name of the Certificate Map. Certificate Map
   * names must be unique globally and match pattern
   * `projects/locations/certificateMaps`.
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
   * Output only. The update timestamp of a Certificate Map.
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
class_alias(CertificateMap::class, 'Google_Service_CertificateManager_CertificateMap');
