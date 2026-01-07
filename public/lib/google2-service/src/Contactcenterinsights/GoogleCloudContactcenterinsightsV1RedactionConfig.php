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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1RedactionConfig extends \Google\Model
{
  /**
   * The fully-qualified DLP deidentify template resource name. Format:
   * `projects/{project}/deidentifyTemplates/{template}`
   *
   * @var string
   */
  public $deidentifyTemplate;
  /**
   * The fully-qualified DLP inspect template resource name. Format:
   * `projects/{project}/locations/{location}/inspectTemplates/{template}`
   *
   * @var string
   */
  public $inspectTemplate;

  /**
   * The fully-qualified DLP deidentify template resource name. Format:
   * `projects/{project}/deidentifyTemplates/{template}`
   *
   * @param string $deidentifyTemplate
   */
  public function setDeidentifyTemplate($deidentifyTemplate)
  {
    $this->deidentifyTemplate = $deidentifyTemplate;
  }
  /**
   * @return string
   */
  public function getDeidentifyTemplate()
  {
    return $this->deidentifyTemplate;
  }
  /**
   * The fully-qualified DLP inspect template resource name. Format:
   * `projects/{project}/locations/{location}/inspectTemplates/{template}`
   *
   * @param string $inspectTemplate
   */
  public function setInspectTemplate($inspectTemplate)
  {
    $this->inspectTemplate = $inspectTemplate;
  }
  /**
   * @return string
   */
  public function getInspectTemplate()
  {
    return $this->inspectTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1RedactionConfig::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1RedactionConfig');
