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

namespace Google\Service\Dns;

class Project extends \Google\Model
{
  /**
   * User assigned unique identifier for the resource (output only).
   *
   * @var string
   */
  public $id;
  /**
   * @var string
   */
  public $kind;
  /**
   * Unique numeric identifier for the resource; defined by the server (output
   * only).
   *
   * @var string
   */
  public $number;
  protected $quotaType = Quota::class;
  protected $quotaDataType = '';

  /**
   * User assigned unique identifier for the resource (output only).
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
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Unique numeric identifier for the resource; defined by the server (output
   * only).
   *
   * @param string $number
   */
  public function setNumber($number)
  {
    $this->number = $number;
  }
  /**
   * @return string
   */
  public function getNumber()
  {
    return $this->number;
  }
  /**
   * Quotas assigned to this project (output only).
   *
   * @param Quota $quota
   */
  public function setQuota(Quota $quota)
  {
    $this->quota = $quota;
  }
  /**
   * @return Quota
   */
  public function getQuota()
  {
    return $this->quota;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Project::class, 'Google_Service_Dns_Project');
