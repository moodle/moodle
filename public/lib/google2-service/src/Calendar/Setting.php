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

namespace Google\Service\Calendar;

class Setting extends \Google\Model
{
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The id of the user setting.
   *
   * @var string
   */
  public $id;
  /**
   * Type of the resource ("calendar#setting").
   *
   * @var string
   */
  public $kind;
  /**
   * Value of the user setting. The format of the value depends on the ID of the
   * setting. It must always be a UTF-8 string of length up to 1024 characters.
   *
   * @var string
   */
  public $value;

  /**
   * ETag of the resource.
   *
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
   * The id of the user setting.
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
   * Type of the resource ("calendar#setting").
   *
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
   * Value of the user setting. The format of the value depends on the ID of the
   * setting. It must always be a UTF-8 string of length up to 1024 characters.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Setting::class, 'Google_Service_Calendar_Setting');
