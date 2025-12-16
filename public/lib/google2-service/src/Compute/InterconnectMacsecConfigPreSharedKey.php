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

namespace Google\Service\Compute;

class InterconnectMacsecConfigPreSharedKey extends \Google\Model
{
  /**
   * An auto-generated Connectivity Association Key (CAK) for this key.
   *
   * @var string
   */
  public $cak;
  /**
   * An auto-generated Connectivity Association Key Name (CKN) for this key.
   *
   * @var string
   */
  public $ckn;
  /**
   * User provided name for this pre-shared key.
   *
   * @var string
   */
  public $name;
  /**
   * User provided timestamp on or after which this key is valid.
   *
   * @var string
   */
  public $startTime;

  /**
   * An auto-generated Connectivity Association Key (CAK) for this key.
   *
   * @param string $cak
   */
  public function setCak($cak)
  {
    $this->cak = $cak;
  }
  /**
   * @return string
   */
  public function getCak()
  {
    return $this->cak;
  }
  /**
   * An auto-generated Connectivity Association Key Name (CKN) for this key.
   *
   * @param string $ckn
   */
  public function setCkn($ckn)
  {
    $this->ckn = $ckn;
  }
  /**
   * @return string
   */
  public function getCkn()
  {
    return $this->ckn;
  }
  /**
   * User provided name for this pre-shared key.
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
   * User provided timestamp on or after which this key is valid.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectMacsecConfigPreSharedKey::class, 'Google_Service_Compute_InterconnectMacsecConfigPreSharedKey');
