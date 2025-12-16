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

namespace Google\Service\Sheets;

class AddNamedRangeRequest extends \Google\Model
{
  protected $namedRangeType = NamedRange::class;
  protected $namedRangeDataType = '';

  /**
   * The named range to add. The namedRangeId field is optional; if one is not
   * set, an id will be randomly generated. (It is an error to specify the ID of
   * a range that already exists.)
   *
   * @param NamedRange $namedRange
   */
  public function setNamedRange(NamedRange $namedRange)
  {
    $this->namedRange = $namedRange;
  }
  /**
   * @return NamedRange
   */
  public function getNamedRange()
  {
    return $this->namedRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddNamedRangeRequest::class, 'Google_Service_Sheets_AddNamedRangeRequest');
