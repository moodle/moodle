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

namespace Google\Service\Config;

class PropertyDrift extends \Google\Collection
{
  protected $collection_key = 'beforeSensitivePaths';
  /**
   * Output only. Representations of the object value after the actions.
   *
   * @var array
   */
  public $after;
  /**
   * Output only. The paths of sensitive fields in `after`. Paths are relative
   * to `path`.
   *
   * @var string[]
   */
  public $afterSensitivePaths;
  /**
   * Output only. Representations of the object value before the actions.
   *
   * @var array
   */
  public $before;
  /**
   * Output only. The paths of sensitive fields in `before`. Paths are relative
   * to `path`.
   *
   * @var string[]
   */
  public $beforeSensitivePaths;
  /**
   * Output only. The path of the property drift.
   *
   * @var string
   */
  public $path;

  /**
   * Output only. Representations of the object value after the actions.
   *
   * @param array $after
   */
  public function setAfter($after)
  {
    $this->after = $after;
  }
  /**
   * @return array
   */
  public function getAfter()
  {
    return $this->after;
  }
  /**
   * Output only. The paths of sensitive fields in `after`. Paths are relative
   * to `path`.
   *
   * @param string[] $afterSensitivePaths
   */
  public function setAfterSensitivePaths($afterSensitivePaths)
  {
    $this->afterSensitivePaths = $afterSensitivePaths;
  }
  /**
   * @return string[]
   */
  public function getAfterSensitivePaths()
  {
    return $this->afterSensitivePaths;
  }
  /**
   * Output only. Representations of the object value before the actions.
   *
   * @param array $before
   */
  public function setBefore($before)
  {
    $this->before = $before;
  }
  /**
   * @return array
   */
  public function getBefore()
  {
    return $this->before;
  }
  /**
   * Output only. The paths of sensitive fields in `before`. Paths are relative
   * to `path`.
   *
   * @param string[] $beforeSensitivePaths
   */
  public function setBeforeSensitivePaths($beforeSensitivePaths)
  {
    $this->beforeSensitivePaths = $beforeSensitivePaths;
  }
  /**
   * @return string[]
   */
  public function getBeforeSensitivePaths()
  {
    return $this->beforeSensitivePaths;
  }
  /**
   * Output only. The path of the property drift.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertyDrift::class, 'Google_Service_Config_PropertyDrift');
