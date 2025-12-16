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

class InstancePropertiesPatch extends \Google\Model
{
  /**
   * The label key-value pairs that you want to patch onto the instance.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The metadata key-value pairs that you want to patch onto the instance. For
   * more information, see Project and instance metadata.
   *
   * @var string[]
   */
  public $metadata;

  /**
   * The label key-value pairs that you want to patch onto the instance.
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
   * The metadata key-value pairs that you want to patch onto the instance. For
   * more information, see Project and instance metadata.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancePropertiesPatch::class, 'Google_Service_Compute_InstancePropertiesPatch');
