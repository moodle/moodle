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

namespace Google\Service\Digitalassetlinks;

class BulkCheckResponse extends \Google\Collection
{
  /**
   * Default value, otherwise unused.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * Unable to parse query.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_INVALID_QUERY = 'ERROR_CODE_INVALID_QUERY';
  /**
   * Unable to fetch the asset links data.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_FETCH_ERROR = 'ERROR_CODE_FETCH_ERROR';
  /**
   * Invalid HTTPS certificate .
   */
  public const BULK_ERROR_CODE_ERROR_CODE_FAILED_SSL_VALIDATION = 'ERROR_CODE_FAILED_SSL_VALIDATION';
  /**
   * HTTP redirects (e.g, 301) are not allowed.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_REDIRECT = 'ERROR_CODE_REDIRECT';
  /**
   * Asset links data exceeds maximum size.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_TOO_LARGE = 'ERROR_CODE_TOO_LARGE';
  /**
   * Can't parse HTTP response.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_MALFORMED_HTTP_RESPONSE = 'ERROR_CODE_MALFORMED_HTTP_RESPONSE';
  /**
   * HTTP Content-type should be application/json.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_WRONG_CONTENT_TYPE = 'ERROR_CODE_WRONG_CONTENT_TYPE';
  /**
   * JSON content is malformed.
   */
  public const BULK_ERROR_CODE_ERROR_CODE_MALFORMED_CONTENT = 'ERROR_CODE_MALFORMED_CONTENT';
  /**
   * A secure asset includes an insecure asset (security downgrade).
   */
  public const BULK_ERROR_CODE_ERROR_CODE_SECURE_ASSET_INCLUDES_INSECURE = 'ERROR_CODE_SECURE_ASSET_INCLUDES_INSECURE';
  /**
   * Too many includes (maybe a loop).
   */
  public const BULK_ERROR_CODE_ERROR_CODE_FETCH_BUDGET_EXHAUSTED = 'ERROR_CODE_FETCH_BUDGET_EXHAUSTED';
  protected $collection_key = 'checkResults';
  /**
   * Error code for the entire request. Present only if the entire request
   * failed. Individual check errors will not trigger the presence of this
   * field.
   *
   * @var string
   */
  public $bulkErrorCode;
  protected $checkResultsType = CheckResponse::class;
  protected $checkResultsDataType = 'array';

  /**
   * Error code for the entire request. Present only if the entire request
   * failed. Individual check errors will not trigger the presence of this
   * field.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, ERROR_CODE_INVALID_QUERY,
   * ERROR_CODE_FETCH_ERROR, ERROR_CODE_FAILED_SSL_VALIDATION,
   * ERROR_CODE_REDIRECT, ERROR_CODE_TOO_LARGE,
   * ERROR_CODE_MALFORMED_HTTP_RESPONSE, ERROR_CODE_WRONG_CONTENT_TYPE,
   * ERROR_CODE_MALFORMED_CONTENT, ERROR_CODE_SECURE_ASSET_INCLUDES_INSECURE,
   * ERROR_CODE_FETCH_BUDGET_EXHAUSTED
   *
   * @param self::BULK_ERROR_CODE_* $bulkErrorCode
   */
  public function setBulkErrorCode($bulkErrorCode)
  {
    $this->bulkErrorCode = $bulkErrorCode;
  }
  /**
   * @return self::BULK_ERROR_CODE_*
   */
  public function getBulkErrorCode()
  {
    return $this->bulkErrorCode;
  }
  /**
   * List of results for each check request. Results are returned in the same
   * order in which they were sent in the request.
   *
   * @param CheckResponse[] $checkResults
   */
  public function setCheckResults($checkResults)
  {
    $this->checkResults = $checkResults;
  }
  /**
   * @return CheckResponse[]
   */
  public function getCheckResults()
  {
    return $this->checkResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkCheckResponse::class, 'Google_Service_Digitalassetlinks_BulkCheckResponse');
