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

namespace Google\Service\BinaryAuthorization;

class AttestationSource extends \Google\Collection
{
  protected $collection_key = 'containerAnalysisAttestationProjects';
  /**
   * The IDs of the Google Cloud projects that store the SLSA attestations as
   * Container Analysis Occurrences, in the format `projects/[PROJECT_ID]`.
   * Maximum number of `container_analysis_attestation_projects` allowed in each
   * `AttestationSource` is 10.
   *
   * @var string[]
   */
  public $containerAnalysisAttestationProjects;

  /**
   * The IDs of the Google Cloud projects that store the SLSA attestations as
   * Container Analysis Occurrences, in the format `projects/[PROJECT_ID]`.
   * Maximum number of `container_analysis_attestation_projects` allowed in each
   * `AttestationSource` is 10.
   *
   * @param string[] $containerAnalysisAttestationProjects
   */
  public function setContainerAnalysisAttestationProjects($containerAnalysisAttestationProjects)
  {
    $this->containerAnalysisAttestationProjects = $containerAnalysisAttestationProjects;
  }
  /**
   * @return string[]
   */
  public function getContainerAnalysisAttestationProjects()
  {
    return $this->containerAnalysisAttestationProjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttestationSource::class, 'Google_Service_BinaryAuthorization_AttestationSource');
