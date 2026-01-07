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

namespace Google\Service\Vault;

class MailExportOptions extends \Google\Model
{
  /**
   * No export format specified.
   */
  public const EXPORT_FORMAT_EXPORT_FORMAT_UNSPECIFIED = 'EXPORT_FORMAT_UNSPECIFIED';
  /**
   * Export as MBOX. Only available for Gmail, Groups, Hangouts and Voice.
   */
  public const EXPORT_FORMAT_MBOX = 'MBOX';
  /**
   * Export as PST. Only available for Gmail, Groups, Hangouts, Voice and
   * Calendar.
   */
  public const EXPORT_FORMAT_PST = 'PST';
  /**
   * Export as ICS. Only available for Calendar.
   */
  public const EXPORT_FORMAT_ICS = 'ICS';
  /**
   * Export as XML. Only available for Gemini.
   */
  public const EXPORT_FORMAT_XML = 'XML';
  /**
   * The file format for exported messages.
   *
   * @var string
   */
  public $exportFormat;
  /**
   * Optional. To enable exporting linked Drive files, set to **true**.
   *
   * @var bool
   */
  public $exportLinkedDriveFiles;
  /**
   * To export confidential mode content, set to **true**.
   *
   * @var bool
   */
  public $showConfidentialModeContent;
  /**
   * To use the new export system, set to **true**.
   *
   * @var bool
   */
  public $useNewExport;

  /**
   * The file format for exported messages.
   *
   * Accepted values: EXPORT_FORMAT_UNSPECIFIED, MBOX, PST, ICS, XML
   *
   * @param self::EXPORT_FORMAT_* $exportFormat
   */
  public function setExportFormat($exportFormat)
  {
    $this->exportFormat = $exportFormat;
  }
  /**
   * @return self::EXPORT_FORMAT_*
   */
  public function getExportFormat()
  {
    return $this->exportFormat;
  }
  /**
   * Optional. To enable exporting linked Drive files, set to **true**.
   *
   * @param bool $exportLinkedDriveFiles
   */
  public function setExportLinkedDriveFiles($exportLinkedDriveFiles)
  {
    $this->exportLinkedDriveFiles = $exportLinkedDriveFiles;
  }
  /**
   * @return bool
   */
  public function getExportLinkedDriveFiles()
  {
    return $this->exportLinkedDriveFiles;
  }
  /**
   * To export confidential mode content, set to **true**.
   *
   * @param bool $showConfidentialModeContent
   */
  public function setShowConfidentialModeContent($showConfidentialModeContent)
  {
    $this->showConfidentialModeContent = $showConfidentialModeContent;
  }
  /**
   * @return bool
   */
  public function getShowConfidentialModeContent()
  {
    return $this->showConfidentialModeContent;
  }
  /**
   * To use the new export system, set to **true**.
   *
   * @param bool $useNewExport
   */
  public function setUseNewExport($useNewExport)
  {
    $this->useNewExport = $useNewExport;
  }
  /**
   * @return bool
   */
  public function getUseNewExport()
  {
    return $this->useNewExport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MailExportOptions::class, 'Google_Service_Vault_MailExportOptions');
