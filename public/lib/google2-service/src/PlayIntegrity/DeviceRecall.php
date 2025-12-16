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

namespace Google\Service\PlayIntegrity;

class DeviceRecall extends \Google\Model
{
  protected $valuesType = Values::class;
  protected $valuesDataType = '';
  protected $writeDatesType = WriteDates::class;
  protected $writeDatesDataType = '';

  /**
   * Required. Contains the recall bits values.
   *
   * @param Values $values
   */
  public function setValues(Values $values)
  {
    $this->values = $values;
  }
  /**
   * @return Values
   */
  public function getValues()
  {
    return $this->values;
  }
  /**
   * Required. Contains the recall bits write dates.
   *
   * @param WriteDates $writeDates
   */
  public function setWriteDates(WriteDates $writeDates)
  {
    $this->writeDates = $writeDates;
  }
  /**
   * @return WriteDates
   */
  public function getWriteDates()
  {
    return $this->writeDates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceRecall::class, 'Google_Service_PlayIntegrity_DeviceRecall');
