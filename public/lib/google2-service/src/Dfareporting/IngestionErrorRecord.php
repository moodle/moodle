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

namespace Google\Service\Dfareporting;

class IngestionErrorRecord extends \Google\Collection
{
  protected $collection_key = 'errors';
  protected $errorsType = FieldError::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. The record ID of the ingestion error record.
   *
   * @var string
   */
  public $recordId;

  /**
   * Output only. The list of field errors of the ingestion error record.
   *
   * @param FieldError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return FieldError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. The record ID of the ingestion error record.
   *
   * @param string $recordId
   */
  public function setRecordId($recordId)
  {
    $this->recordId = $recordId;
  }
  /**
   * @return string
   */
  public function getRecordId()
  {
    return $this->recordId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IngestionErrorRecord::class, 'Google_Service_Dfareporting_IngestionErrorRecord');
