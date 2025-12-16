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

class ConsentConfig extends \Google\Collection
{
  /**
   * Users must specify an enforcement version or an error is returned.
   */
  public const VERSION_CONSENT_ENFORCEMENT_VERSION_UNSPECIFIED = 'CONSENT_ENFORCEMENT_VERSION_UNSPECIFIED';
  /**
   * Enforcement version 1. See the [FHIR Consent resources in the Cloud
   * Healthcare API](https://cloud.google.com/healthcare-api/docs/fhir-consent)
   * guide for more details.
   */
  public const VERSION_V1 = 'V1';
  protected $collection_key = 'enforcedAdminConsents';
  protected $accessDeterminationLogConfigType = AccessDeterminationLogConfig::class;
  protected $accessDeterminationLogConfigDataType = '';
  /**
   * Optional. The default value is false. If set to true, when accessing FHIR
   * resources, the consent headers will be verified against consents given by
   * patients. See the ConsentEnforcementVersion for the supported consent
   * headers.
   *
   * @var bool
   */
  public $accessEnforced;
  protected $consentHeaderHandlingType = ConsentHeaderHandling::class;
  protected $consentHeaderHandlingDataType = '';
  /**
   * Output only. The versioned names of the enforced admin Consent resource(s),
   * in the format `projects/{project_id}/locations/{location}/datasets/{dataset
   * _id}/fhirStores/{fhir_store_id}/fhir/Consent/{resource_id}/_history/{versio
   * n_id}`. For FHIR stores with `disable_resource_versioning=true`, the format
   * is `projects/{project_id}/locations/{location}/datasets/{dataset_id}/fhirSt
   * ores/{fhir_store_id}/fhir/Consent/{resource_id}`. This field can only be
   * updated using ApplyAdminConsents.
   *
   * @var string[]
   */
  public $enforcedAdminConsents;
  /**
   * Required. Specifies which consent enforcement version is being used for
   * this FHIR store. This field can only be set once by either CreateFhirStore
   * or UpdateFhirStore. After that, you must call ApplyConsents to change the
   * version.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. Specifies how the server logs the consent-aware requests. If not
   * specified, the `AccessDeterminationLogConfig.LogLevel.MINIMUM` option is
   * used.
   *
   * @param AccessDeterminationLogConfig $accessDeterminationLogConfig
   */
  public function setAccessDeterminationLogConfig(AccessDeterminationLogConfig $accessDeterminationLogConfig)
  {
    $this->accessDeterminationLogConfig = $accessDeterminationLogConfig;
  }
  /**
   * @return AccessDeterminationLogConfig
   */
  public function getAccessDeterminationLogConfig()
  {
    return $this->accessDeterminationLogConfig;
  }
  /**
   * Optional. The default value is false. If set to true, when accessing FHIR
   * resources, the consent headers will be verified against consents given by
   * patients. See the ConsentEnforcementVersion for the supported consent
   * headers.
   *
   * @param bool $accessEnforced
   */
  public function setAccessEnforced($accessEnforced)
  {
    $this->accessEnforced = $accessEnforced;
  }
  /**
   * @return bool
   */
  public function getAccessEnforced()
  {
    return $this->accessEnforced;
  }
  /**
   * Optional. Different options to configure the behaviour of the server when
   * handling the `X-Consent-Scope` header.
   *
   * @param ConsentHeaderHandling $consentHeaderHandling
   */
  public function setConsentHeaderHandling(ConsentHeaderHandling $consentHeaderHandling)
  {
    $this->consentHeaderHandling = $consentHeaderHandling;
  }
  /**
   * @return ConsentHeaderHandling
   */
  public function getConsentHeaderHandling()
  {
    return $this->consentHeaderHandling;
  }
  /**
   * Output only. The versioned names of the enforced admin Consent resource(s),
   * in the format `projects/{project_id}/locations/{location}/datasets/{dataset
   * _id}/fhirStores/{fhir_store_id}/fhir/Consent/{resource_id}/_history/{versio
   * n_id}`. For FHIR stores with `disable_resource_versioning=true`, the format
   * is `projects/{project_id}/locations/{location}/datasets/{dataset_id}/fhirSt
   * ores/{fhir_store_id}/fhir/Consent/{resource_id}`. This field can only be
   * updated using ApplyAdminConsents.
   *
   * @param string[] $enforcedAdminConsents
   */
  public function setEnforcedAdminConsents($enforcedAdminConsents)
  {
    $this->enforcedAdminConsents = $enforcedAdminConsents;
  }
  /**
   * @return string[]
   */
  public function getEnforcedAdminConsents()
  {
    return $this->enforcedAdminConsents;
  }
  /**
   * Required. Specifies which consent enforcement version is being used for
   * this FHIR store. This field can only be set once by either CreateFhirStore
   * or UpdateFhirStore. After that, you must call ApplyConsents to change the
   * version.
   *
   * Accepted values: CONSENT_ENFORCEMENT_VERSION_UNSPECIFIED, V1
   *
   * @param self::VERSION_* $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return self::VERSION_*
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsentConfig::class, 'Google_Service_CloudHealthcare_ConsentConfig');
