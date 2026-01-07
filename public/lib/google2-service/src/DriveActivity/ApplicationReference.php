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

namespace Google\Service\DriveActivity;

class ApplicationReference extends \Google\Model
{
  /**
   * The type is not available.
   */
  public const TYPE_UNSPECIFIED_REFERENCE_TYPE = 'UNSPECIFIED_REFERENCE_TYPE';
  /**
   * The links of one or more Drive items were posted.
   */
  public const TYPE_LINK = 'LINK';
  /**
   * Comments were made regarding a Drive item.
   */
  public const TYPE_DISCUSS = 'DISCUSS';
  /**
   * The reference type corresponding to this event.
   *
   * @var string
   */
  public $type;

  /**
   * The reference type corresponding to this event.
   *
   * Accepted values: UNSPECIFIED_REFERENCE_TYPE, LINK, DISCUSS
   *
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
class_alias(ApplicationReference::class, 'Google_Service_DriveActivity_ApplicationReference');
