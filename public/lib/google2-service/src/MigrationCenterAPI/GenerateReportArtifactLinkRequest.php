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

class GenerateReportArtifactLinkRequest extends \Google\Model
{
  /**
   * Unspecified (default value).
   */
  public const ARTIFACT_TYPE_ARTIFACT_TYPE_UNSPECIFIED = 'ARTIFACT_TYPE_UNSPECIFIED';
  /**
   * Google Slides presentation.
   */
  public const ARTIFACT_TYPE_PRESENTATION = 'PRESENTATION';
  /**
   * Google Sheets document.
   */
  public const ARTIFACT_TYPE_SPREADSHEET = 'SPREADSHEET';
  /**
   * Comma Separated Value Text File.
   */
  public const ARTIFACT_TYPE_CSV = 'CSV';
  /**
   * Required. Type of the artifact requested.
   *
   * @var string
   */
  public $artifactType;

  /**
   * Required. Type of the artifact requested.
   *
   * Accepted values: ARTIFACT_TYPE_UNSPECIFIED, PRESENTATION, SPREADSHEET, CSV
   *
   * @param self::ARTIFACT_TYPE_* $artifactType
   */
  public function setArtifactType($artifactType)
  {
    $this->artifactType = $artifactType;
  }
  /**
   * @return self::ARTIFACT_TYPE_*
   */
  public function getArtifactType()
  {
    return $this->artifactType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateReportArtifactLinkRequest::class, 'Google_Service_MigrationCenterAPI_GenerateReportArtifactLinkRequest');
