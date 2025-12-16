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

namespace Google\Service\Looker;

class ExportMetadata extends \Google\Collection
{
  /**
   * Source not specified
   */
  public const SOURCE_SOURCE_UNSPECIFIED = 'SOURCE_UNSPECIFIED';
  /**
   * Source of export is Looker Core
   */
  public const SOURCE_LOOKER_CORE = 'LOOKER_CORE';
  /**
   * Source of export is Looker Original
   */
  public const SOURCE_LOOKER_ORIGINAL = 'LOOKER_ORIGINAL';
  protected $collection_key = 'filePaths';
  protected $exportEncryptionKeyType = ExportMetadataEncryptionKey::class;
  protected $exportEncryptionKeyDataType = '';
  /**
   * List of files created as part of export artifact (excluding the metadata).
   * The paths are relative to the folder containing the metadata.
   *
   * @var string[]
   */
  public $filePaths;
  /**
   * Looker encryption key, encrypted with the provided export encryption key.
   * This value will only be populated if the looker instance uses Looker
   * managed encryption instead of CMEK.
   *
   * @var string
   */
  public $lookerEncryptionKey;
  /**
   * Name of the exported instance. Format:
   * projects/{project}/locations/{location}/instances/{instance}
   *
   * @var string
   */
  public $lookerInstance;
  /**
   * Platform edition of the exported instance.
   *
   * @var string
   */
  public $lookerPlatformEdition;
  /**
   * Version of instance when the export was created.
   *
   * @var string
   */
  public $lookerVersion;
  /**
   * The source type of the migration.
   *
   * @var string
   */
  public $source;

  /**
   * Encryption key that was used to encrypt the export artifacts.
   *
   * @param ExportMetadataEncryptionKey $exportEncryptionKey
   */
  public function setExportEncryptionKey(ExportMetadataEncryptionKey $exportEncryptionKey)
  {
    $this->exportEncryptionKey = $exportEncryptionKey;
  }
  /**
   * @return ExportMetadataEncryptionKey
   */
  public function getExportEncryptionKey()
  {
    return $this->exportEncryptionKey;
  }
  /**
   * List of files created as part of export artifact (excluding the metadata).
   * The paths are relative to the folder containing the metadata.
   *
   * @param string[] $filePaths
   */
  public function setFilePaths($filePaths)
  {
    $this->filePaths = $filePaths;
  }
  /**
   * @return string[]
   */
  public function getFilePaths()
  {
    return $this->filePaths;
  }
  /**
   * Looker encryption key, encrypted with the provided export encryption key.
   * This value will only be populated if the looker instance uses Looker
   * managed encryption instead of CMEK.
   *
   * @param string $lookerEncryptionKey
   */
  public function setLookerEncryptionKey($lookerEncryptionKey)
  {
    $this->lookerEncryptionKey = $lookerEncryptionKey;
  }
  /**
   * @return string
   */
  public function getLookerEncryptionKey()
  {
    return $this->lookerEncryptionKey;
  }
  /**
   * Name of the exported instance. Format:
   * projects/{project}/locations/{location}/instances/{instance}
   *
   * @param string $lookerInstance
   */
  public function setLookerInstance($lookerInstance)
  {
    $this->lookerInstance = $lookerInstance;
  }
  /**
   * @return string
   */
  public function getLookerInstance()
  {
    return $this->lookerInstance;
  }
  /**
   * Platform edition of the exported instance.
   *
   * @param string $lookerPlatformEdition
   */
  public function setLookerPlatformEdition($lookerPlatformEdition)
  {
    $this->lookerPlatformEdition = $lookerPlatformEdition;
  }
  /**
   * @return string
   */
  public function getLookerPlatformEdition()
  {
    return $this->lookerPlatformEdition;
  }
  /**
   * Version of instance when the export was created.
   *
   * @param string $lookerVersion
   */
  public function setLookerVersion($lookerVersion)
  {
    $this->lookerVersion = $lookerVersion;
  }
  /**
   * @return string
   */
  public function getLookerVersion()
  {
    return $this->lookerVersion;
  }
  /**
   * The source type of the migration.
   *
   * Accepted values: SOURCE_UNSPECIFIED, LOOKER_CORE, LOOKER_ORIGINAL
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportMetadata::class, 'Google_Service_Looker_ExportMetadata');
