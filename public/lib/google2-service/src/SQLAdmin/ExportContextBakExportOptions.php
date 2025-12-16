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

class ExportContextBakExportOptions extends \Google\Model
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
   * Type of this bak file will be export, FULL or DIFF, SQL Server only
   *
   * @var string
   */
  public $bakType;
  /**
   * Deprecated: copy_only is deprecated. Use differential_base instead
   *
   * @deprecated
   * @var bool
   */
  public $copyOnly;
  /**
   * Whether or not the backup can be used as a differential base copy_only
   * backup can not be served as differential base
   *
   * @var bool
   */
  public $differentialBase;
  /**
   * Optional. The end timestamp when transaction log will be included in the
   * export operation. [RFC 3339](https://tools.ietf.org/html/rfc3339) format
   * (for example, `2023-10-01T16:19:00.094`) in UTC. When omitted, all
   * available logs until current time will be included. Only applied to Cloud
   * SQL for SQL Server.
   *
   * @var string
   */
  public $exportLogEndTime;
  /**
   * Optional. The begin timestamp when transaction log will be included in the
   * export operation. [RFC 3339](https://tools.ietf.org/html/rfc3339) format
   * (for example, `2023-10-01T16:19:00.094`) in UTC. When omitted, all
   * available logs from the beginning of retention period will be included.
   * Only applied to Cloud SQL for SQL Server.
   *
   * @var string
   */
  public $exportLogStartTime;
  /**
   * Option for specifying how many stripes to use for the export. If blank, and
   * the value of the striped field is true, the number of stripes is
   * automatically chosen.
   *
   * @var int
   */
  public $stripeCount;
  /**
   * Whether or not the export should be striped.
   *
   * @var bool
   */
  public $striped;

  /**
   * Type of this bak file will be export, FULL or DIFF, SQL Server only
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
   * Deprecated: copy_only is deprecated. Use differential_base instead
   *
   * @deprecated
   * @param bool $copyOnly
   */
  public function setCopyOnly($copyOnly)
  {
    $this->copyOnly = $copyOnly;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCopyOnly()
  {
    return $this->copyOnly;
  }
  /**
   * Whether or not the backup can be used as a differential base copy_only
   * backup can not be served as differential base
   *
   * @param bool $differentialBase
   */
  public function setDifferentialBase($differentialBase)
  {
    $this->differentialBase = $differentialBase;
  }
  /**
   * @return bool
   */
  public function getDifferentialBase()
  {
    return $this->differentialBase;
  }
  /**
   * Optional. The end timestamp when transaction log will be included in the
   * export operation. [RFC 3339](https://tools.ietf.org/html/rfc3339) format
   * (for example, `2023-10-01T16:19:00.094`) in UTC. When omitted, all
   * available logs until current time will be included. Only applied to Cloud
   * SQL for SQL Server.
   *
   * @param string $exportLogEndTime
   */
  public function setExportLogEndTime($exportLogEndTime)
  {
    $this->exportLogEndTime = $exportLogEndTime;
  }
  /**
   * @return string
   */
  public function getExportLogEndTime()
  {
    return $this->exportLogEndTime;
  }
  /**
   * Optional. The begin timestamp when transaction log will be included in the
   * export operation. [RFC 3339](https://tools.ietf.org/html/rfc3339) format
   * (for example, `2023-10-01T16:19:00.094`) in UTC. When omitted, all
   * available logs from the beginning of retention period will be included.
   * Only applied to Cloud SQL for SQL Server.
   *
   * @param string $exportLogStartTime
   */
  public function setExportLogStartTime($exportLogStartTime)
  {
    $this->exportLogStartTime = $exportLogStartTime;
  }
  /**
   * @return string
   */
  public function getExportLogStartTime()
  {
    return $this->exportLogStartTime;
  }
  /**
   * Option for specifying how many stripes to use for the export. If blank, and
   * the value of the striped field is true, the number of stripes is
   * automatically chosen.
   *
   * @param int $stripeCount
   */
  public function setStripeCount($stripeCount)
  {
    $this->stripeCount = $stripeCount;
  }
  /**
   * @return int
   */
  public function getStripeCount()
  {
    return $this->stripeCount;
  }
  /**
   * Whether or not the export should be striped.
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
class_alias(ExportContextBakExportOptions::class, 'Google_Service_SQLAdmin_ExportContextBakExportOptions');
