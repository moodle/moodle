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

namespace Google\Service\Dataflow;

class StageSource extends \Google\Model
{
  /**
   * Dataflow service generated name for this source.
   *
   * @var string
   */
  public $name;
  /**
   * User name for the original user transform or collection with which this
   * source is most closely associated.
   *
   * @var string
   */
  public $originalTransformOrCollection;
  /**
   * Size of the source, if measurable.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Human-readable name for this source; may be user or system generated.
   *
   * @var string
   */
  public $userName;

  /**
   * Dataflow service generated name for this source.
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
   * User name for the original user transform or collection with which this
   * source is most closely associated.
   *
   * @param string $originalTransformOrCollection
   */
  public function setOriginalTransformOrCollection($originalTransformOrCollection)
  {
    $this->originalTransformOrCollection = $originalTransformOrCollection;
  }
  /**
   * @return string
   */
  public function getOriginalTransformOrCollection()
  {
    return $this->originalTransformOrCollection;
  }
  /**
   * Size of the source, if measurable.
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * Human-readable name for this source; may be user or system generated.
   *
   * @param string $userName
   */
  public function setUserName($userName)
  {
    $this->userName = $userName;
  }
  /**
   * @return string
   */
  public function getUserName()
  {
    return $this->userName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StageSource::class, 'Google_Service_Dataflow_StageSource');
