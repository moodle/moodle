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

namespace Google\Service\Compute;

class RouterNatLogConfig extends \Google\Model
{
  /**
   * Export logs for all (successful and unsuccessful) connections.
   */
  public const FILTER_ALL = 'ALL';
  /**
   * Export logs for connection failures only.
   */
  public const FILTER_ERRORS_ONLY = 'ERRORS_ONLY';
  /**
   * Export logs for successful connections only.
   */
  public const FILTER_TRANSLATIONS_ONLY = 'TRANSLATIONS_ONLY';
  /**
   * Indicates whether or not to export logs. This is false by default.
   *
   * @var bool
   */
  public $enable;
  /**
   * Specify the desired filtering of logs on this NAT. If unspecified, logs are
   * exported for all connections handled by this NAT. This option can take one
   * of the following values:        - ERRORS_ONLY: Export logs only for
   * connection failures.    - TRANSLATIONS_ONLY: Export logs only for
   * successful    connections.    - ALL: Export logs for all connections,
   * successful and    unsuccessful.
   *
   * @var string
   */
  public $filter;

  /**
   * Indicates whether or not to export logs. This is false by default.
   *
   * @param bool $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return bool
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * Specify the desired filtering of logs on this NAT. If unspecified, logs are
   * exported for all connections handled by this NAT. This option can take one
   * of the following values:        - ERRORS_ONLY: Export logs only for
   * connection failures.    - TRANSLATIONS_ONLY: Export logs only for
   * successful    connections.    - ALL: Export logs for all connections,
   * successful and    unsuccessful.
   *
   * Accepted values: ALL, ERRORS_ONLY, TRANSLATIONS_ONLY
   *
   * @param self::FILTER_* $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return self::FILTER_*
   */
  public function getFilter()
  {
    return $this->filter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterNatLogConfig::class, 'Google_Service_Compute_RouterNatLogConfig');
