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

class StoredColumnsUnusedReason extends \Google\Collection
{
  /**
   * Default value.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * If stored columns do not fully cover the columns.
   */
  public const CODE_STORED_COLUMNS_COVER_INSUFFICIENT = 'STORED_COLUMNS_COVER_INSUFFICIENT';
  /**
   * If the base table has RLS (Row Level Security).
   */
  public const CODE_BASE_TABLE_HAS_RLS = 'BASE_TABLE_HAS_RLS';
  /**
   * If the base table has CLS (Column Level Security).
   */
  public const CODE_BASE_TABLE_HAS_CLS = 'BASE_TABLE_HAS_CLS';
  /**
   * If the provided prefilter is not supported.
   */
  public const CODE_UNSUPPORTED_PREFILTER = 'UNSUPPORTED_PREFILTER';
  /**
   * If an internal error is preventing stored columns from being used.
   */
  public const CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * Indicates that the reason stored columns cannot be used in the query is not
   * covered by any of the other StoredColumnsUnusedReason options.
   */
  public const CODE_OTHER_REASON = 'OTHER_REASON';
  protected $collection_key = 'uncoveredColumns';
  /**
   * Specifies the high-level reason for the unused scenario, each reason must
   * have a code associated.
   *
   * @var string
   */
  public $code;
  /**
   * Specifies the detailed description for the scenario.
   *
   * @var string
   */
  public $message;
  /**
   * Specifies which columns were not covered by the stored columns for the
   * specified code up to 20 columns. This is populated when the code is
   * STORED_COLUMNS_COVER_INSUFFICIENT and BASE_TABLE_HAS_CLS.
   *
   * @var string[]
   */
  public $uncoveredColumns;

  /**
   * Specifies the high-level reason for the unused scenario, each reason must
   * have a code associated.
   *
   * Accepted values: CODE_UNSPECIFIED, STORED_COLUMNS_COVER_INSUFFICIENT,
   * BASE_TABLE_HAS_RLS, BASE_TABLE_HAS_CLS, UNSUPPORTED_PREFILTER,
   * INTERNAL_ERROR, OTHER_REASON
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Specifies the detailed description for the scenario.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Specifies which columns were not covered by the stored columns for the
   * specified code up to 20 columns. This is populated when the code is
   * STORED_COLUMNS_COVER_INSUFFICIENT and BASE_TABLE_HAS_CLS.
   *
   * @param string[] $uncoveredColumns
   */
  public function setUncoveredColumns($uncoveredColumns)
  {
    $this->uncoveredColumns = $uncoveredColumns;
  }
  /**
   * @return string[]
   */
  public function getUncoveredColumns()
  {
    return $this->uncoveredColumns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoredColumnsUnusedReason::class, 'Google_Service_Bigquery_StoredColumnsUnusedReason');
