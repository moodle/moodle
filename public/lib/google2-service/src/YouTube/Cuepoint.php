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

namespace Google\Service\YouTube;

class Cuepoint extends \Google\Model
{
  public const CUE_TYPE_cueTypeUnspecified = 'cueTypeUnspecified';
  public const CUE_TYPE_cueTypeAd = 'cueTypeAd';
  /**
   * @var string
   */
  public $cueType;
  /**
   * The duration of this cuepoint.
   *
   * @var string
   */
  public $durationSecs;
  /**
   * @var string
   */
  public $etag;
  /**
   * The identifier for cuepoint resource.
   *
   * @var string
   */
  public $id;
  /**
   * The time when the cuepoint should be inserted by offset to the broadcast
   * actual start time.
   *
   * @var string
   */
  public $insertionOffsetTimeMs;
  /**
   * The wall clock time at which the cuepoint should be inserted. Only one of
   * insertion_offset_time_ms and walltime_ms may be set at a time.
   *
   * @var string
   */
  public $walltimeMs;

  /**
   * @param self::CUE_TYPE_* $cueType
   */
  public function setCueType($cueType)
  {
    $this->cueType = $cueType;
  }
  /**
   * @return self::CUE_TYPE_*
   */
  public function getCueType()
  {
    return $this->cueType;
  }
  /**
   * The duration of this cuepoint.
   *
   * @param string $durationSecs
   */
  public function setDurationSecs($durationSecs)
  {
    $this->durationSecs = $durationSecs;
  }
  /**
   * @return string
   */
  public function getDurationSecs()
  {
    return $this->durationSecs;
  }
  /**
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The identifier for cuepoint resource.
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
   * The time when the cuepoint should be inserted by offset to the broadcast
   * actual start time.
   *
   * @param string $insertionOffsetTimeMs
   */
  public function setInsertionOffsetTimeMs($insertionOffsetTimeMs)
  {
    $this->insertionOffsetTimeMs = $insertionOffsetTimeMs;
  }
  /**
   * @return string
   */
  public function getInsertionOffsetTimeMs()
  {
    return $this->insertionOffsetTimeMs;
  }
  /**
   * The wall clock time at which the cuepoint should be inserted. Only one of
   * insertion_offset_time_ms and walltime_ms may be set at a time.
   *
   * @param string $walltimeMs
   */
  public function setWalltimeMs($walltimeMs)
  {
    $this->walltimeMs = $walltimeMs;
  }
  /**
   * @return string
   */
  public function getWalltimeMs()
  {
    return $this->walltimeMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cuepoint::class, 'Google_Service_YouTube_Cuepoint');
