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

class ProcessingError extends \Google\Collection
{
  /**
   * Input only value. Use this value in Items.
   */
  public const CODE_PROCESSING_ERROR_CODE_UNSPECIFIED = 'PROCESSING_ERROR_CODE_UNSPECIFIED';
  /**
   * Item's ACL, metadata, or content is malformed or in invalid state.
   * FieldViolations contains more details on where the problem is.
   */
  public const CODE_MALFORMED_REQUEST = 'MALFORMED_REQUEST';
  /**
   * Countent format is unsupported.
   */
  public const CODE_UNSUPPORTED_CONTENT_FORMAT = 'UNSUPPORTED_CONTENT_FORMAT';
  /**
   * Items with incomplete ACL information due to inheriting other items with
   * broken ACL or having groups with unmapped descendants.
   */
  public const CODE_INDIRECT_BROKEN_ACL = 'INDIRECT_BROKEN_ACL';
  /**
   * ACL inheritance graph formed a cycle.
   */
  public const CODE_ACL_CYCLE = 'ACL_CYCLE';
  protected $collection_key = 'fieldViolations';
  /**
   * Error code indicating the nature of the error.
   *
   * @var string
   */
  public $code;
  /**
   * The description of the error.
   *
   * @var string
   */
  public $errorMessage;
  protected $fieldViolationsType = FieldViolation::class;
  protected $fieldViolationsDataType = 'array';

  /**
   * Error code indicating the nature of the error.
   *
   * Accepted values: PROCESSING_ERROR_CODE_UNSPECIFIED, MALFORMED_REQUEST,
   * UNSUPPORTED_CONTENT_FORMAT, INDIRECT_BROKEN_ACL, ACL_CYCLE
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
   * The description of the error.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * In case the item fields are invalid, this field contains the details about
   * the validation errors.
   *
   * @param FieldViolation[] $fieldViolations
   */
  public function setFieldViolations($fieldViolations)
  {
    $this->fieldViolations = $fieldViolations;
  }
  /**
   * @return FieldViolation[]
   */
  public function getFieldViolations()
  {
    return $this->fieldViolations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProcessingError::class, 'Google_Service_CloudSearch_ProcessingError');
