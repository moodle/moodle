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

namespace Google\Service\Transcoder;

class EditAtom extends \Google\Collection
{
  protected $collection_key = 'inputs';
  /**
   * End time in seconds for the atom, relative to the input file timeline. When
   * `end_time_offset` is not specified, the `inputs` are used until the end of
   * the atom.
   *
   * @var string
   */
  public $endTimeOffset;
  /**
   * List of Input.key values identifying files that should be used in this
   * atom. The listed `inputs` must have the same timeline.
   *
   * @var string[]
   */
  public $inputs;
  /**
   * A unique key for this atom. Must be specified when using advanced mapping.
   *
   * @var string
   */
  public $key;
  /**
   * Start time in seconds for the atom, relative to the input file timeline.
   * The default is `0s`.
   *
   * @var string
   */
  public $startTimeOffset;

  /**
   * End time in seconds for the atom, relative to the input file timeline. When
   * `end_time_offset` is not specified, the `inputs` are used until the end of
   * the atom.
   *
   * @param string $endTimeOffset
   */
  public function setEndTimeOffset($endTimeOffset)
  {
    $this->endTimeOffset = $endTimeOffset;
  }
  /**
   * @return string
   */
  public function getEndTimeOffset()
  {
    return $this->endTimeOffset;
  }
  /**
   * List of Input.key values identifying files that should be used in this
   * atom. The listed `inputs` must have the same timeline.
   *
   * @param string[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return string[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * A unique key for this atom. Must be specified when using advanced mapping.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Start time in seconds for the atom, relative to the input file timeline.
   * The default is `0s`.
   *
   * @param string $startTimeOffset
   */
  public function setStartTimeOffset($startTimeOffset)
  {
    $this->startTimeOffset = $startTimeOffset;
  }
  /**
   * @return string
   */
  public function getStartTimeOffset()
  {
    return $this->startTimeOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EditAtom::class, 'Google_Service_Transcoder_EditAtom');
