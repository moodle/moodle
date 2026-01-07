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

class RestrictionConfig extends \Google\Model
{
  /**
   * Should never be used.
   */
  public const TYPE_RESTRICTION_TYPE_UNSPECIFIED = 'RESTRICTION_TYPE_UNSPECIFIED';
  /**
   * Restrict data egress. See [Data
   * egress](https://cloud.google.com/bigquery/docs/analytics-hub-
   * introduction#data_egress) for more details.
   */
  public const TYPE_RESTRICTED_DATA_EGRESS = 'RESTRICTED_DATA_EGRESS';
  /**
   * Output only. Specifies the type of dataset/table restriction.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Specifies the type of dataset/table restriction.
   *
   * Accepted values: RESTRICTION_TYPE_UNSPECIFIED, RESTRICTED_DATA_EGRESS
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
class_alias(RestrictionConfig::class, 'Google_Service_Bigquery_RestrictionConfig');
