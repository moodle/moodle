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

namespace Google\Service\SQLAdmin;

class ImportContextBakImportOptions extends \Google\Model
{
  /**
   * Default type.
   */
  public const BAK_TYPE_BAK_TYPE_UNSPECIFIED = 'BAK_TYPE_UNSPECIFIED';
  /**
   * Full backup.
   */
  public const BAK_TYPE_FULL = 'FULL';
  /**
   * Differential backup.
   */
  public const BAK_TYPE_DIFF = 'DIFF';
  /**
   * Transaction Log backup
   */
  public const BAK_TYPE_TLOG = 'TLOG';
  /**
   * Type of the bak content, FULL or DIFF
   *
   * @var string
   */
  public $bakType;
  protected $encryptionOptionsType = ImportContextBakImportOptionsEncryptionOptions::class;
  protected $encryptionOptionsDataType = '';
  /**
   * Whether or not the backup importing will restore database with NORECOVERY
   * option. Applies only to Cloud SQL for SQL Server.
   *
   * @var bool
   */
  public $noRecovery;
  /**
   * Whether or not the backup importing request will just bring database online
   * without downloading Bak content only one of "no_recovery" and
   * "recovery_only" can be true otherwise error will return. Applies only to
   * Cloud SQL for SQL Server.
   *
   * @var bool
   */
  public $recoveryOnly;
  /**
   * Optional. The timestamp when the import should stop. This timestamp is in
   * the [RFC 3339](https://tools.ietf.org/html/rfc3339) format (for example,
   * `2023-10-01T16:19:00.094`). This field is equivalent to the STOPAT keyword
   * and applies to Cloud SQL for SQL Server only.
   *
   * @var string
   */
  public $stopAt;
  /**
   * Optional. The marked transaction where the import should stop. This field
   * is equivalent to the STOPATMARK keyword and applies to Cloud SQL for SQL
   * Server only.
   *
   * @var string
   */
  public $stopAtMark;
  /**
   * Whether or not the backup set being restored is striped. Applies only to
   * Cloud SQL for SQL Server.
   *
   * @var bool
   */
  public $striped;

  /**
   * Type of the bak content, FULL or DIFF
   *
   * Accepted values: BAK_TYPE_UNSPECIFIED, FULL, DIFF, TLOG
   *
   * @param self::BAK_TYPE_* $bakType
   */
  public function setBakType($bakType)
  {
    $this->bakType = $bakType;
  }
  /**
   * @return self::BAK_TYPE_*
   */
  public function getBakType()
  {
    return $this->bakType;
  }
  /**
   * @param ImportContextBakImportOptionsEncryptionOptions $encryptionOptions
   */
  public function setEncryptionOptions(ImportContextBakImportOptionsEncryptionOptions $encryptionOptions)
  {
    $this->encryptionOptions = $encryptionOptions;
  }
  /**
   * @return ImportContextBakImportOptionsEncryptionOptions
   */
  public function getEncryptionOptions()
  {
    return $this->encryptionOptions;
  }
  /**
   * Whether or not the backup importing will restore database with NORECOVERY
   * option. Applies only to Cloud SQL for SQL Server.
   *
   * @param bool $noRecovery
   */
  public function setNoRecovery($noRecovery)
  {
    $this->noRecovery = $noRecovery;
  }
  /**
   * @return bool
   */
  public function getNoRecovery()
  {
    return $this->noRecovery;
  }
  /**
   * Whether or not the backup importing request will just bring database online
   * without downloading Bak content only one of "no_recovery" and
   * "recovery_only" can be true otherwise error will return. Applies only to
   * Cloud SQL for SQL Server.
   *
   * @param bool $recoveryOnly
   */
  public function setRecoveryOnly($recoveryOnly)
  {
    $this->recoveryOnly = $recoveryOnly;
  }
  /**
   * @return bool
   */
  public function getRecoveryOnly()
  {
    return $this->recoveryOnly;
  }
  /**
   * Optional. The timestamp when the import should stop. This timestamp is in
   * the [RFC 3339](https://tools.ietf.org/html/rfc3339) format (for example,
   * `2023-10-01T16:19:00.094`). This field is equivalent to the STOPAT keyword
   * and applies to Cloud SQL for SQL Server only.
   *
   * @param string $stopAt
   */
  public function setStopAt($stopAt)
  {
    $this->stopAt = $stopAt;
  }
  /**
   * @return string
   */
  public function getStopAt()
  {
    return $this->stopAt;
  }
  /**
   * Optional. The marked transaction where the import should stop. This field
   * is equivalent to the STOPATMARK keyword and applies to Cloud SQL for SQL
   * Server only.
   *
   * @param string $stopAtMark
   */
  public function setStopAtMark($stopAtMark)
  {
    $this->stopAtMark = $stopAtMark;
  }
  /**
   * @return string
   */
  public function getStopAtMark()
  {
    return $this->stopAtMark;
  }
  /**
   * Whether or not the backup set being restored is striped. Applies only to
   * Cloud SQL for SQL Server.
   *
   * @param bool $striped
   */
  public function setStriped($striped)
  {
    $this->striped = $striped;
  }
  /**
   * @return bool
   */
  public function getStriped()
  {
    return $this->striped;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportContextBakImportOptions::class, 'Google_Service_SQLAdmin_ImportContextBakImportOptions');
