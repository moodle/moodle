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

namespace Google\Service\Storagetransfer;

class Manifest extends \Google\Model
{
  /**
   * @var string
   */
  public $manifestLocation;
  /**
   * @var string
   */
  public $root;

  /**
   * @param string
   */
  public function setManifestLocation($manifestLocation)
  {
    $this->manifestLocation = $manifestLocation;
  }
  /**
   * @return string
   */
  public function getManifestLocation()
  {
    return $this->manifestLocation;
  }
  /**
   * @param string
   */
  public function setRoot($root)
  {
    $this->root = $root;
  }
  /**
   * @return string
   */
  public function getRoot()
  {
    return $this->root;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Manifest::class, 'Google_Service_Storagetransfer_Manifest');
