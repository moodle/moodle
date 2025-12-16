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

namespace Google\Service\MigrationCenterAPI;

class ImportRowError extends \Google\Collection
{
  protected $collection_key = 'errors';
  protected $archiveErrorType = ImportRowErrorArchiveErrorDetails::class;
  protected $archiveErrorDataType = '';
  /**
   * Output only. The asset title.
   *
   * @var string
   */
  public $assetTitle;
  protected $csvErrorType = ImportRowErrorCsvErrorDetails::class;
  protected $csvErrorDataType = '';
  protected $errorsType = ImportError::class;
  protected $errorsDataType = 'array';
  /**
   * The row number where the error was detected.
   *
   * @deprecated
   * @var int
   */
  public $rowNumber;
  /**
   * The name of the VM in the row.
   *
   * @var string
   */
  public $vmName;
  /**
   * The VM UUID.
   *
   * @var string
   */
  public $vmUuid;
  protected $xlsxErrorType = ImportRowErrorXlsxErrorDetails::class;
  protected $xlsxErrorDataType = '';

  /**
   * Error details for an archive file.
   *
   * @param ImportRowErrorArchiveErrorDetails $archiveError
   */
  public function setArchiveError(ImportRowErrorArchiveErrorDetails $archiveError)
  {
    $this->archiveError = $archiveError;
  }
  /**
   * @return ImportRowErrorArchiveErrorDetails
   */
  public function getArchiveError()
  {
    return $this->archiveError;
  }
  /**
   * Output only. The asset title.
   *
   * @param string $assetTitle
   */
  public function setAssetTitle($assetTitle)
  {
    $this->assetTitle = $assetTitle;
  }
  /**
   * @return string
   */
  public function getAssetTitle()
  {
    return $this->assetTitle;
  }
  /**
   * Error details for a CSV file.
   *
   * @param ImportRowErrorCsvErrorDetails $csvError
   */
  public function setCsvError(ImportRowErrorCsvErrorDetails $csvError)
  {
    $this->csvError = $csvError;
  }
  /**
   * @return ImportRowErrorCsvErrorDetails
   */
  public function getCsvError()
  {
    return $this->csvError;
  }
  /**
   * The list of errors detected in the row.
   *
   * @param ImportError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ImportError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The row number where the error was detected.
   *
   * @deprecated
   * @param int $rowNumber
   */
  public function setRowNumber($rowNumber)
  {
    $this->rowNumber = $rowNumber;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getRowNumber()
  {
    return $this->rowNumber;
  }
  /**
   * The name of the VM in the row.
   *
   * @param string $vmName
   */
  public function setVmName($vmName)
  {
    $this->vmName = $vmName;
  }
  /**
   * @return string
   */
  public function getVmName()
  {
    return $this->vmName;
  }
  /**
   * The VM UUID.
   *
   * @param string $vmUuid
   */
  public function setVmUuid($vmUuid)
  {
    $this->vmUuid = $vmUuid;
  }
  /**
   * @return string
   */
  public function getVmUuid()
  {
    return $this->vmUuid;
  }
  /**
   * Error details for an XLSX file.
   *
   * @param ImportRowErrorXlsxErrorDetails $xlsxError
   */
  public function setXlsxError(ImportRowErrorXlsxErrorDetails $xlsxError)
  {
    $this->xlsxError = $xlsxError;
  }
  /**
   * @return ImportRowErrorXlsxErrorDetails
   */
  public function getXlsxError()
  {
    return $this->xlsxError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportRowError::class, 'Google_Service_MigrationCenterAPI_ImportRowError');
