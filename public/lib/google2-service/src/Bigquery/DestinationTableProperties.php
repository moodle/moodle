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

namespace Google\Service\Bigquery;

class DestinationTableProperties extends \Google\Model
{
  /**
   * Optional. The description for the destination table. This will only be used
   * if the destination table is newly created. If the table already exists and
   * a value different than the current description is provided, the job will
   * fail.
   *
   * @var string
   */
  public $description;
  /**
   * Internal use only.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * Optional. Friendly name for the destination table. If the table already
   * exists, it should be same as the existing friendly name.
   *
   * @var string
   */
  public $friendlyName;
  /**
   * Optional. The labels associated with this table. You can use these to
   * organize and group your tables. This will only be used if the destination
   * table is newly created. If the table already exists and labels are
   * different than the current labels are provided, the job will fail.
   *
   * @var string[]
   */
  public $labels;

  /**
   * Optional. The description for the destination table. This will only be used
   * if the destination table is newly created. If the table already exists and
   * a value different than the current description is provided, the job will
   * fail.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Internal use only.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * Optional. Friendly name for the destination table. If the table already
   * exists, it should be same as the existing friendly name.
   *
   * @param string $friendlyName
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;
  }
  /**
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }
  /**
   * Optional. The labels associated with this table. You can use these to
   * organize and group your tables. This will only be used if the destination
   * table is newly created. If the table already exists and labels are
   * different than the current labels are provided, the job will fail.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationTableProperties::class, 'Google_Service_Bigquery_DestinationTableProperties');
