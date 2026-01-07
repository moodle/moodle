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

class ItemStatus extends \Google\Collection
{
  /**
   * Input-only value. Used with Items.list to list all items in the queue,
   * regardless of status.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * Error encountered by Cloud Search while processing this item. Details of
   * the error are in repositoryError.
   */
  public const CODE_ERROR = 'ERROR';
  /**
   * Item has been modified in the repository, and is out of date with the
   * version previously accepted into Cloud Search.
   */
  public const CODE_MODIFIED = 'MODIFIED';
  /**
   * Item is known to exist in the repository, but is not yet accepted by Cloud
   * Search. An item can be in this state when Items.push has been called for an
   * item of this name that did not exist previously.
   */
  public const CODE_NEW_ITEM = 'NEW_ITEM';
  /**
   * API has accepted the up-to-date data of this item.
   */
  public const CODE_ACCEPTED = 'ACCEPTED';
  protected $collection_key = 'repositoryErrors';
  /**
   * Status code.
   *
   * @var string
   */
  public $code;
  protected $processingErrorsType = ProcessingError::class;
  protected $processingErrorsDataType = 'array';
  protected $repositoryErrorsType = RepositoryError::class;
  protected $repositoryErrorsDataType = 'array';

  /**
   * Status code.
   *
   * Accepted values: CODE_UNSPECIFIED, ERROR, MODIFIED, NEW_ITEM, ACCEPTED
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
   * Error details in case the item is in ERROR state.
   *
   * @param ProcessingError[] $processingErrors
   */
  public function setProcessingErrors($processingErrors)
  {
    $this->processingErrors = $processingErrors;
  }
  /**
   * @return ProcessingError[]
   */
  public function getProcessingErrors()
  {
    return $this->processingErrors;
  }
  /**
   * Repository error reported by connector.
   *
   * @param RepositoryError[] $repositoryErrors
   */
  public function setRepositoryErrors($repositoryErrors)
  {
    $this->repositoryErrors = $repositoryErrors;
  }
  /**
   * @return RepositoryError[]
   */
  public function getRepositoryErrors()
  {
    return $this->repositoryErrors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemStatus::class, 'Google_Service_CloudSearch_ItemStatus');
