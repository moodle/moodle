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

class ExplainDataAccessConsentInfo extends \Google\Collection
{
  /**
   * Unspecified policy type.
   */
  public const TYPE_CONSENT_POLICY_TYPE_UNSPECIFIED = 'CONSENT_POLICY_TYPE_UNSPECIFIED';
  /**
   * Consent represent a patient consent.
   */
  public const TYPE_CONSENT_POLICY_TYPE_PATIENT = 'CONSENT_POLICY_TYPE_PATIENT';
  /**
   * Consent represent an admin consent.
   */
  public const TYPE_CONSENT_POLICY_TYPE_ADMIN = 'CONSENT_POLICY_TYPE_ADMIN';
  protected $collection_key = 'variants';
  /**
   * The compartment base resources that matched a cascading policy. Each
   * resource has the following format: `projects/{project_id}/locations/{locati
   * on_id}/datasets/{dataset_id}/fhirStores/{fhir_store_id}/fhir/{resource_type
   * }/{resource_id}`
   *
   * @var string[]
   */
  public $cascadeOrigins;
  /**
   * The resource name of this consent resource, in the format: `projects/{proje
   * ct_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{fhir_store_id
   * }/fhir/Consent/{resource_id}`.
   *
   * @var string
   */
  public $consentResource;
  /**
   * Last enforcement timestamp of this consent resource.
   *
   * @var string
   */
  public $enforcementTime;
  protected $matchingAccessorScopesType = ConsentAccessorScope::class;
  protected $matchingAccessorScopesDataType = 'array';
  /**
   * The patient owning the consent (only applicable for patient consents), in
   * the format: `projects/{project_id}/locations/{location_id}/datasets/{datase
   * t_id}/fhirStores/{fhir_store_id}/fhir/Patient/{patient_id}`
   *
   * @var string
   */
  public $patientConsentOwner;
  /**
   * The policy type of consent resource (e.g. PATIENT, ADMIN).
   *
   * @var string
   */
  public $type;
  /**
   * The consent's variant combinations. A single consent may have multiple
   * variants.
   *
   * @var string[]
   */
  public $variants;

  /**
   * The compartment base resources that matched a cascading policy. Each
   * resource has the following format: `projects/{project_id}/locations/{locati
   * on_id}/datasets/{dataset_id}/fhirStores/{fhir_store_id}/fhir/{resource_type
   * }/{resource_id}`
   *
   * @param string[] $cascadeOrigins
   */
  public function setCascadeOrigins($cascadeOrigins)
  {
    $this->cascadeOrigins = $cascadeOrigins;
  }
  /**
   * @return string[]
   */
  public function getCascadeOrigins()
  {
    return $this->cascadeOrigins;
  }
  /**
   * The resource name of this consent resource, in the format: `projects/{proje
   * ct_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{fhir_store_id
   * }/fhir/Consent/{resource_id}`.
   *
   * @param string $consentResource
   */
  public function setConsentResource($consentResource)
  {
    $this->consentResource = $consentResource;
  }
  /**
   * @return string
   */
  public function getConsentResource()
  {
    return $this->consentResource;
  }
  /**
   * Last enforcement timestamp of this consent resource.
   *
   * @param string $enforcementTime
   */
  public function setEnforcementTime($enforcementTime)
  {
    $this->enforcementTime = $enforcementTime;
  }
  /**
   * @return string
   */
  public function getEnforcementTime()
  {
    return $this->enforcementTime;
  }
  /**
   * A list of all the matching accessor scopes of this consent policy that
   * enforced ExplainDataAccessConsentScope.accessor_scope.
   *
   * @param ConsentAccessorScope[] $matchingAccessorScopes
   */
  public function setMatchingAccessorScopes($matchingAccessorScopes)
  {
    $this->matchingAccessorScopes = $matchingAccessorScopes;
  }
  /**
   * @return ConsentAccessorScope[]
   */
  public function getMatchingAccessorScopes()
  {
    return $this->matchingAccessorScopes;
  }
  /**
   * The patient owning the consent (only applicable for patient consents), in
   * the format: `projects/{project_id}/locations/{location_id}/datasets/{datase
   * t_id}/fhirStores/{fhir_store_id}/fhir/Patient/{patient_id}`
   *
   * @param string $patientConsentOwner
   */
  public function setPatientConsentOwner($patientConsentOwner)
  {
    $this->patientConsentOwner = $patientConsentOwner;
  }
  /**
   * @return string
   */
  public function getPatientConsentOwner()
  {
    return $this->patientConsentOwner;
  }
  /**
   * The policy type of consent resource (e.g. PATIENT, ADMIN).
   *
   * Accepted values: CONSENT_POLICY_TYPE_UNSPECIFIED,
   * CONSENT_POLICY_TYPE_PATIENT, CONSENT_POLICY_TYPE_ADMIN
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The consent's variant combinations. A single consent may have multiple
   * variants.
   *
   * @param string[] $variants
   */
  public function setVariants($variants)
  {
    $this->variants = $variants;
  }
  /**
   * @return string[]
   */
  public function getVariants()
  {
    return $this->variants;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExplainDataAccessConsentInfo::class, 'Google_Service_CloudHealthcare_ExplainDataAccessConsentInfo');
