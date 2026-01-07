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

class GooglePrivacyDlpV2FileClusterType extends \Google\Model
{
  /**
   * Unused.
   */
  public const CLUSTER_CLUSTER_UNSPECIFIED = 'CLUSTER_UNSPECIFIED';
  /**
   * Unsupported files.
   */
  public const CLUSTER_CLUSTER_UNKNOWN = 'CLUSTER_UNKNOWN';
  /**
   * Plain text.
   */
  public const CLUSTER_CLUSTER_TEXT = 'CLUSTER_TEXT';
  /**
   * Structured data like CSV, TSV etc.
   */
  public const CLUSTER_CLUSTER_STRUCTURED_DATA = 'CLUSTER_STRUCTURED_DATA';
  /**
   * Source code.
   */
  public const CLUSTER_CLUSTER_SOURCE_CODE = 'CLUSTER_SOURCE_CODE';
  /**
   * Rich document like docx, xlsx etc.
   */
  public const CLUSTER_CLUSTER_RICH_DOCUMENT = 'CLUSTER_RICH_DOCUMENT';
  /**
   * Images like jpeg, bmp.
   */
  public const CLUSTER_CLUSTER_IMAGE = 'CLUSTER_IMAGE';
  /**
   * Archives and containers like .zip, .tar etc.
   */
  public const CLUSTER_CLUSTER_ARCHIVE = 'CLUSTER_ARCHIVE';
  /**
   * Multimedia like .mp4, .avi etc.
   */
  public const CLUSTER_CLUSTER_MULTIMEDIA = 'CLUSTER_MULTIMEDIA';
  /**
   * Executable files like .exe, .class, .apk etc.
   */
  public const CLUSTER_CLUSTER_EXECUTABLE = 'CLUSTER_EXECUTABLE';
  /**
   * AI models like .tflite etc.
   */
  public const CLUSTER_CLUSTER_AI_MODEL = 'CLUSTER_AI_MODEL';
  /**
   * Cluster type.
   *
   * @var string
   */
  public $cluster;

  /**
   * Cluster type.
   *
   * Accepted values: CLUSTER_UNSPECIFIED, CLUSTER_UNKNOWN, CLUSTER_TEXT,
   * CLUSTER_STRUCTURED_DATA, CLUSTER_SOURCE_CODE, CLUSTER_RICH_DOCUMENT,
   * CLUSTER_IMAGE, CLUSTER_ARCHIVE, CLUSTER_MULTIMEDIA, CLUSTER_EXECUTABLE,
   * CLUSTER_AI_MODEL
   *
   * @param self::CLUSTER_* $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return self::CLUSTER_*
   */
  public function getCluster()
  {
    return $this->cluster;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2FileClusterType::class, 'Google_Service_DLP_GooglePrivacyDlpV2FileClusterType');
