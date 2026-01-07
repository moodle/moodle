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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1ImportEntriesMetadata extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_IMPORT_STATE_UNSPECIFIED = 'IMPORT_STATE_UNSPECIFIED';
  /**
   * The dump with entries has been queued for import.
   */
  public const STATE_IMPORT_QUEUED = 'IMPORT_QUEUED';
  /**
   * The import of entries is in progress.
   */
  public const STATE_IMPORT_IN_PROGRESS = 'IMPORT_IN_PROGRESS';
  /**
   * The import of entries has been finished.
   */
  public const STATE_IMPORT_DONE = 'IMPORT_DONE';
  /**
   * The import of entries has been abandoned in favor of a newer request.
   */
  public const STATE_IMPORT_OBSOLETE = 'IMPORT_OBSOLETE';
  protected $collection_key = 'errors';
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  /**
   * State of the import operation.
   *
   * @var string
   */
  public $state;

  /**
   * Partial errors that are encountered during the ImportEntries operation.
   * There is no guarantee that all the encountered errors are reported.
   * However, if no errors are reported, it means that no errors were
   * encountered.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * State of the import operation.
   *
   * Accepted values: IMPORT_STATE_UNSPECIFIED, IMPORT_QUEUED,
   * IMPORT_IN_PROGRESS, IMPORT_DONE, IMPORT_OBSOLETE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1ImportEntriesMetadata::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ImportEntriesMetadata');
