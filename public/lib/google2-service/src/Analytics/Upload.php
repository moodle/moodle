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

namespace Google\Service\Analytics;

class Upload extends \Google\Collection
{
  protected $collection_key = 'errors';
  /**
   * Account Id to which this upload belongs.
   *
   * @var string
   */
  public $accountId;
  /**
   * Custom data source Id to which this data import belongs.
   *
   * @var string
   */
  public $customDataSourceId;
  /**
   * Data import errors collection.
   *
   * @var string[]
   */
  public $errors;
  /**
   * A unique ID for this upload.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for Analytics upload.
   *
   * @var string
   */
  public $kind;
  /**
   * Upload status. Possible values: PENDING, COMPLETED, FAILED, DELETING,
   * DELETED.
   *
   * @var string
   */
  public $status;
  /**
   * Time this file is uploaded.
   *
   * @var string
   */
  public $uploadTime;

  /**
   * Account Id to which this upload belongs.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Custom data source Id to which this data import belongs.
   *
   * @param string $customDataSourceId
   */
  public function setCustomDataSourceId($customDataSourceId)
  {
    $this->customDataSourceId = $customDataSourceId;
  }
  /**
   * @return string
   */
  public function getCustomDataSourceId()
  {
    return $this->customDataSourceId;
  }
  /**
   * Data import errors collection.
   *
   * @param string[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return string[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * A unique ID for this upload.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Resource type for Analytics upload.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Upload status. Possible values: PENDING, COMPLETED, FAILED, DELETING,
   * DELETED.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Time this file is uploaded.
   *
   * @param string $uploadTime
   */
  public function setUploadTime($uploadTime)
  {
    $this->uploadTime = $uploadTime;
  }
  /**
   * @return string
   */
  public function getUploadTime()
  {
    return $this->uploadTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Upload::class, 'Google_Service_Analytics_Upload');
