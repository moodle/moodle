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

namespace Google\Service\Monitoring;

class ResponseStatusCode extends \Google\Model
{
  /**
   * Default value that matches no status codes.
   */
  public const STATUS_CLASS_STATUS_CLASS_UNSPECIFIED = 'STATUS_CLASS_UNSPECIFIED';
  /**
   * The class of status codes between 100 and 199.
   */
  public const STATUS_CLASS_STATUS_CLASS_1XX = 'STATUS_CLASS_1XX';
  /**
   * The class of status codes between 200 and 299.
   */
  public const STATUS_CLASS_STATUS_CLASS_2XX = 'STATUS_CLASS_2XX';
  /**
   * The class of status codes between 300 and 399.
   */
  public const STATUS_CLASS_STATUS_CLASS_3XX = 'STATUS_CLASS_3XX';
  /**
   * The class of status codes between 400 and 499.
   */
  public const STATUS_CLASS_STATUS_CLASS_4XX = 'STATUS_CLASS_4XX';
  /**
   * The class of status codes between 500 and 599.
   */
  public const STATUS_CLASS_STATUS_CLASS_5XX = 'STATUS_CLASS_5XX';
  /**
   * The class of all status codes.
   */
  public const STATUS_CLASS_STATUS_CLASS_ANY = 'STATUS_CLASS_ANY';
  /**
   * A class of status codes to accept.
   *
   * @var string
   */
  public $statusClass;
  /**
   * A status code to accept.
   *
   * @var int
   */
  public $statusValue;

  /**
   * A class of status codes to accept.
   *
   * Accepted values: STATUS_CLASS_UNSPECIFIED, STATUS_CLASS_1XX,
   * STATUS_CLASS_2XX, STATUS_CLASS_3XX, STATUS_CLASS_4XX, STATUS_CLASS_5XX,
   * STATUS_CLASS_ANY
   *
   * @param self::STATUS_CLASS_* $statusClass
   */
  public function setStatusClass($statusClass)
  {
    $this->statusClass = $statusClass;
  }
  /**
   * @return self::STATUS_CLASS_*
   */
  public function getStatusClass()
  {
    return $this->statusClass;
  }
  /**
   * A status code to accept.
   *
   * @param int $statusValue
   */
  public function setStatusValue($statusValue)
  {
    $this->statusValue = $statusValue;
  }
  /**
   * @return int
   */
  public function getStatusValue()
  {
    return $this->statusValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResponseStatusCode::class, 'Google_Service_Monitoring_ResponseStatusCode');
