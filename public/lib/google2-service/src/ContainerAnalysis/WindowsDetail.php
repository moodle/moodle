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

namespace Google\Service\ContainerAnalysis;

class WindowsDetail extends \Google\Collection
{
  protected $collection_key = 'fixingKbs';
  /**
   * Required. The [CPE URI](https://cpe.mitre.org/specification/) this
   * vulnerability affects.
   *
   * @var string
   */
  public $cpeUri;
  /**
   * The description of this vulnerability.
   *
   * @var string
   */
  public $description;
  protected $fixingKbsType = KnowledgeBase::class;
  protected $fixingKbsDataType = 'array';
  /**
   * Required. The name of this vulnerability.
   *
   * @var string
   */
  public $name;

  /**
   * Required. The [CPE URI](https://cpe.mitre.org/specification/) this
   * vulnerability affects.
   *
   * @param string $cpeUri
   */
  public function setCpeUri($cpeUri)
  {
    $this->cpeUri = $cpeUri;
  }
  /**
   * @return string
   */
  public function getCpeUri()
  {
    return $this->cpeUri;
  }
  /**
   * The description of this vulnerability.
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
   * Required. The names of the KBs which have hotfixes to mitigate this
   * vulnerability. Note that there may be multiple hotfixes (and thus multiple
   * KBs) that mitigate a given vulnerability. Currently any listed KBs presence
   * is considered a fix.
   *
   * @param KnowledgeBase[] $fixingKbs
   */
  public function setFixingKbs($fixingKbs)
  {
    $this->fixingKbs = $fixingKbs;
  }
  /**
   * @return KnowledgeBase[]
   */
  public function getFixingKbs()
  {
    return $this->fixingKbs;
  }
  /**
   * Required. The name of this vulnerability.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WindowsDetail::class, 'Google_Service_ContainerAnalysis_WindowsDetail');
