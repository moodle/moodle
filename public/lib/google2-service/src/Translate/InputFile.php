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

namespace Google\Service\Translate;

class InputFile extends \Google\Model
{
  protected $gcsSourceType = GcsInputSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Optional. Usage of the file contents. Options are TRAIN|VALIDATION|TEST, or
   * UNASSIGNED (by default) for auto split.
   *
   * @var string
   */
  public $usage;

  /**
   * Google Cloud Storage file source.
   *
   * @param GcsInputSource $gcsSource
   */
  public function setGcsSource(GcsInputSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GcsInputSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Optional. Usage of the file contents. Options are TRAIN|VALIDATION|TEST, or
   * UNASSIGNED (by default) for auto split.
   *
   * @param string $usage
   */
  public function setUsage($usage)
  {
    $this->usage = $usage;
  }
  /**
   * @return string
   */
  public function getUsage()
  {
    return $this->usage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InputFile::class, 'Google_Service_Translate_InputFile');
