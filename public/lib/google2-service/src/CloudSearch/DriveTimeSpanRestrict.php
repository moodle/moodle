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

namespace Google\Service\CloudSearch;

class DriveTimeSpanRestrict extends \Google\Model
{
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  public const TYPE_TODAY = 'TODAY';
  public const TYPE_YESTERDAY = 'YESTERDAY';
  public const TYPE_LAST_7_DAYS = 'LAST_7_DAYS';
  /**
   * Not Enabled
   */
  public const TYPE_LAST_30_DAYS = 'LAST_30_DAYS';
  /**
   * Not Enabled
   */
  public const TYPE_LAST_90_DAYS = 'LAST_90_DAYS';
  /**
   * @var string
   */
  public $type;

  /**
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveTimeSpanRestrict::class, 'Google_Service_CloudSearch_DriveTimeSpanRestrict');
