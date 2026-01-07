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

namespace Google\Service\WorkloadManager;

class InvalidRule extends \Google\Model
{
  /**
   * display name of the invalid rule
   *
   * @var string
   */
  public $displayName;
  /**
   * cloud storage destination of the invalid rule
   *
   * @var string
   */
  public $gcsUri;
  /**
   * name of the invalid rule
   *
   * @var string
   */
  public $name;
  /**
   * The error message of valdating rule formats.
   *
   * @var string
   */
  public $valiadtionError;

  /**
   * display name of the invalid rule
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
   * cloud storage destination of the invalid rule
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
  /**
   * name of the invalid rule
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
   * The error message of valdating rule formats.
   *
   * @param string $valiadtionError
   */
  public function setValiadtionError($valiadtionError)
  {
    $this->valiadtionError = $valiadtionError;
  }
  /**
   * @return string
   */
  public function getValiadtionError()
  {
    return $this->valiadtionError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InvalidRule::class, 'Google_Service_WorkloadManager_InvalidRule');
