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

namespace Google\Service\CloudHealthcare;

class ConsentErrors extends \Google\Model
{
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * The versioned name of the admin Consent resource, in the format `projects/{
   * project_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{fhir_sto
   * re_id}/fhir/Consent/{resource_id}/_history/{version_id}`. For FHIR stores
   * with `disable_resource_versioning=true`, the format is `projects/{project_i
   * d}/locations/{location}/datasets/{dataset_id}/fhirStores/{fhir_store_id}/fh
   * ir/Consent/{resource_id}`.
   *
   * @var string
   */
  public $name;

  /**
   * The error code and message.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The versioned name of the admin Consent resource, in the format `projects/{
   * project_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{fhir_sto
   * re_id}/fhir/Consent/{resource_id}/_history/{version_id}`. For FHIR stores
   * with `disable_resource_versioning=true`, the format is `projects/{project_i
   * d}/locations/{location}/datasets/{dataset_id}/fhirStores/{fhir_store_id}/fh
   * ir/Consent/{resource_id}`.
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
class_alias(ConsentErrors::class, 'Google_Service_CloudHealthcare_ConsentErrors');
