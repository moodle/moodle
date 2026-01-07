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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2SecurityMarks extends \Google\Model
{
  /**
   * The canonical name of the marks. The following list shows some examples: +
   * `organizations/{organization_id}/assets/{asset_id}/securityMarks` + `organi
   * zations/{organization_id}/sources/{source_id}/findings/{finding_id}/securit
   * yMarks` + `organizations/{organization_id}/sources/{source_id}/locations/{l
   * ocation}/findings/{finding_id}/securityMarks` +
   * `folders/{folder_id}/assets/{asset_id}/securityMarks` + `folders/{folder_id
   * }/sources/{source_id}/findings/{finding_id}/securityMarks` + `folders/{fold
   * er_id}/sources/{source_id}/locations/{location}/findings/{finding_id}/secur
   * ityMarks` + `projects/{project_number}/assets/{asset_id}/securityMarks` + `
   * projects/{project_number}/sources/{source_id}/findings/{finding_id}/securit
   * yMarks` + `projects/{project_number}/sources/{source_id}/locations/{locatio
   * n}/findings/{finding_id}/securityMarks`
   *
   * @var string
   */
  public $canonicalName;
  /**
   * Mutable user specified security marks belonging to the parent resource.
   * Constraints are as follows: * Keys and values are treated as case
   * insensitive * Keys must be between 1 - 256 characters (inclusive) * Keys
   * must be letters, numbers, underscores, or dashes * Values have leading and
   * trailing whitespace trimmed, remaining characters must be between 1 - 4096
   * characters (inclusive)
   *
   * @var string[]
   */
  public $marks;
  /**
   * The relative resource name of the SecurityMarks. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * The following list shows some examples: +
   * `organizations/{organization_id}/assets/{asset_id}/securityMarks` + `organi
   * zations/{organization_id}/sources/{source_id}/findings/{finding_id}/securit
   * yMarks` + `organizations/{organization_id}/sources/{source_id}/locations/{l
   * ocation}/findings/{finding_id}/securityMarks`
   *
   * @var string
   */
  public $name;

  /**
   * The canonical name of the marks. The following list shows some examples: +
   * `organizations/{organization_id}/assets/{asset_id}/securityMarks` + `organi
   * zations/{organization_id}/sources/{source_id}/findings/{finding_id}/securit
   * yMarks` + `organizations/{organization_id}/sources/{source_id}/locations/{l
   * ocation}/findings/{finding_id}/securityMarks` +
   * `folders/{folder_id}/assets/{asset_id}/securityMarks` + `folders/{folder_id
   * }/sources/{source_id}/findings/{finding_id}/securityMarks` + `folders/{fold
   * er_id}/sources/{source_id}/locations/{location}/findings/{finding_id}/secur
   * ityMarks` + `projects/{project_number}/assets/{asset_id}/securityMarks` + `
   * projects/{project_number}/sources/{source_id}/findings/{finding_id}/securit
   * yMarks` + `projects/{project_number}/sources/{source_id}/locations/{locatio
   * n}/findings/{finding_id}/securityMarks`
   *
   * @param string $canonicalName
   */
  public function setCanonicalName($canonicalName)
  {
    $this->canonicalName = $canonicalName;
  }
  /**
   * @return string
   */
  public function getCanonicalName()
  {
    return $this->canonicalName;
  }
  /**
   * Mutable user specified security marks belonging to the parent resource.
   * Constraints are as follows: * Keys and values are treated as case
   * insensitive * Keys must be between 1 - 256 characters (inclusive) * Keys
   * must be letters, numbers, underscores, or dashes * Values have leading and
   * trailing whitespace trimmed, remaining characters must be between 1 - 4096
   * characters (inclusive)
   *
   * @param string[] $marks
   */
  public function setMarks($marks)
  {
    $this->marks = $marks;
  }
  /**
   * @return string[]
   */
  public function getMarks()
  {
    return $this->marks;
  }
  /**
   * The relative resource name of the SecurityMarks. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * The following list shows some examples: +
   * `organizations/{organization_id}/assets/{asset_id}/securityMarks` + `organi
   * zations/{organization_id}/sources/{source_id}/findings/{finding_id}/securit
   * yMarks` + `organizations/{organization_id}/sources/{source_id}/locations/{l
   * ocation}/findings/{finding_id}/securityMarks`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2SecurityMarks::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2SecurityMarks');
