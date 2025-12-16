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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DataProfileBigQueryRowSchema extends \Google\Model
{
  protected $columnProfileType = GooglePrivacyDlpV2ColumnDataProfile::class;
  protected $columnProfileDataType = '';
  protected $fileStoreProfileType = GooglePrivacyDlpV2FileStoreDataProfile::class;
  protected $fileStoreProfileDataType = '';
  protected $tableProfileType = GooglePrivacyDlpV2TableDataProfile::class;
  protected $tableProfileDataType = '';

  /**
   * Column data profile column
   *
   * @param GooglePrivacyDlpV2ColumnDataProfile $columnProfile
   */
  public function setColumnProfile(GooglePrivacyDlpV2ColumnDataProfile $columnProfile)
  {
    $this->columnProfile = $columnProfile;
  }
  /**
   * @return GooglePrivacyDlpV2ColumnDataProfile
   */
  public function getColumnProfile()
  {
    return $this->columnProfile;
  }
  /**
   * File store data profile column.
   *
   * @param GooglePrivacyDlpV2FileStoreDataProfile $fileStoreProfile
   */
  public function setFileStoreProfile(GooglePrivacyDlpV2FileStoreDataProfile $fileStoreProfile)
  {
    $this->fileStoreProfile = $fileStoreProfile;
  }
  /**
   * @return GooglePrivacyDlpV2FileStoreDataProfile
   */
  public function getFileStoreProfile()
  {
    return $this->fileStoreProfile;
  }
  /**
   * Table data profile column
   *
   * @param GooglePrivacyDlpV2TableDataProfile $tableProfile
   */
  public function setTableProfile(GooglePrivacyDlpV2TableDataProfile $tableProfile)
  {
    $this->tableProfile = $tableProfile;
  }
  /**
   * @return GooglePrivacyDlpV2TableDataProfile
   */
  public function getTableProfile()
  {
    return $this->tableProfile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataProfileBigQueryRowSchema::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataProfileBigQueryRowSchema');
