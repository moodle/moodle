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

class ValidationConfig extends \Google\Collection
{
  protected $collection_key = 'enabledImplementationGuides';
  /**
   * Optional. Whether to disable FHIRPath validation for incoming resources.
   * The default value is false. Set this to true to disable checking incoming
   * resources for conformance against FHIRPath requirement defined in the FHIR
   * specification. This property only affects resource types that do not have
   * profiles configured for them, any rules in enabled implementation guides
   * will still be enforced.
   *
   * @var bool
   */
  public $disableFhirpathValidation;
  /**
   * Optional. Whether to disable profile validation for this FHIR store. The
   * default value is false. Set this to true to disable checking incoming
   * resources for conformance against structure definitions in this FHIR store.
   *
   * @var bool
   */
  public $disableProfileValidation;
  /**
   * Optional. Whether to disable reference type validation for incoming
   * resources. The default value is false. Set this to true to disable checking
   * incoming resources for conformance against reference type requirement
   * defined in the FHIR specification. This property only affects resource
   * types that do not have profiles configured for them, any rules in enabled
   * implementation guides will still be enforced.
   *
   * @var bool
   */
  public $disableReferenceTypeValidation;
  /**
   * Optional. Whether to disable required fields validation for incoming
   * resources. The default value is false. Set this to true to disable checking
   * incoming resources for conformance against required fields requirement
   * defined in the FHIR specification. This property only affects resource
   * types that do not have profiles configured for them, any rules in enabled
   * implementation guides will still be enforced.
   *
   * @var bool
   */
  public $disableRequiredFieldValidation;
  /**
   * Optional. A list of implementation guide URLs in this FHIR store that are
   * used to configure the profiles to use for validation. For example, to use
   * the US Core profiles for validation, set `enabled_implementation_guides` to
   * `["http://hl7.org/fhir/us/core/ImplementationGuide/ig"]`. If
   * `enabled_implementation_guides` is empty or omitted, then incoming
   * resources are only required to conform to the base FHIR profiles.
   * Otherwise, a resource must conform to at least one profile listed in the
   * `global` property of one of the enabled ImplementationGuides. The Cloud
   * Healthcare API does not currently enforce all of the rules in a
   * StructureDefinition. The following rules are supported: - min/max -
   * minValue/maxValue - maxLength - type - fixed[x] - pattern[x] on simple
   * types - slicing, when using "value" as the discriminator type - FHIRPath
   * constraints (only when `enable_fhirpath_profile_validation` is true) When a
   * URL cannot be resolved (for example, in a type assertion), the server does
   * not return an error.
   *
   * @var string[]
   */
  public $enabledImplementationGuides;

  /**
   * Optional. Whether to disable FHIRPath validation for incoming resources.
   * The default value is false. Set this to true to disable checking incoming
   * resources for conformance against FHIRPath requirement defined in the FHIR
   * specification. This property only affects resource types that do not have
   * profiles configured for them, any rules in enabled implementation guides
   * will still be enforced.
   *
   * @param bool $disableFhirpathValidation
   */
  public function setDisableFhirpathValidation($disableFhirpathValidation)
  {
    $this->disableFhirpathValidation = $disableFhirpathValidation;
  }
  /**
   * @return bool
   */
  public function getDisableFhirpathValidation()
  {
    return $this->disableFhirpathValidation;
  }
  /**
   * Optional. Whether to disable profile validation for this FHIR store. The
   * default value is false. Set this to true to disable checking incoming
   * resources for conformance against structure definitions in this FHIR store.
   *
   * @param bool $disableProfileValidation
   */
  public function setDisableProfileValidation($disableProfileValidation)
  {
    $this->disableProfileValidation = $disableProfileValidation;
  }
  /**
   * @return bool
   */
  public function getDisableProfileValidation()
  {
    return $this->disableProfileValidation;
  }
  /**
   * Optional. Whether to disable reference type validation for incoming
   * resources. The default value is false. Set this to true to disable checking
   * incoming resources for conformance against reference type requirement
   * defined in the FHIR specification. This property only affects resource
   * types that do not have profiles configured for them, any rules in enabled
   * implementation guides will still be enforced.
   *
   * @param bool $disableReferenceTypeValidation
   */
  public function setDisableReferenceTypeValidation($disableReferenceTypeValidation)
  {
    $this->disableReferenceTypeValidation = $disableReferenceTypeValidation;
  }
  /**
   * @return bool
   */
  public function getDisableReferenceTypeValidation()
  {
    return $this->disableReferenceTypeValidation;
  }
  /**
   * Optional. Whether to disable required fields validation for incoming
   * resources. The default value is false. Set this to true to disable checking
   * incoming resources for conformance against required fields requirement
   * defined in the FHIR specification. This property only affects resource
   * types that do not have profiles configured for them, any rules in enabled
   * implementation guides will still be enforced.
   *
   * @param bool $disableRequiredFieldValidation
   */
  public function setDisableRequiredFieldValidation($disableRequiredFieldValidation)
  {
    $this->disableRequiredFieldValidation = $disableRequiredFieldValidation;
  }
  /**
   * @return bool
   */
  public function getDisableRequiredFieldValidation()
  {
    return $this->disableRequiredFieldValidation;
  }
  /**
   * Optional. A list of implementation guide URLs in this FHIR store that are
   * used to configure the profiles to use for validation. For example, to use
   * the US Core profiles for validation, set `enabled_implementation_guides` to
   * `["http://hl7.org/fhir/us/core/ImplementationGuide/ig"]`. If
   * `enabled_implementation_guides` is empty or omitted, then incoming
   * resources are only required to conform to the base FHIR profiles.
   * Otherwise, a resource must conform to at least one profile listed in the
   * `global` property of one of the enabled ImplementationGuides. The Cloud
   * Healthcare API does not currently enforce all of the rules in a
   * StructureDefinition. The following rules are supported: - min/max -
   * minValue/maxValue - maxLength - type - fixed[x] - pattern[x] on simple
   * types - slicing, when using "value" as the discriminator type - FHIRPath
   * constraints (only when `enable_fhirpath_profile_validation` is true) When a
   * URL cannot be resolved (for example, in a type assertion), the server does
   * not return an error.
   *
   * @param string[] $enabledImplementationGuides
   */
  public function setEnabledImplementationGuides($enabledImplementationGuides)
  {
    $this->enabledImplementationGuides = $enabledImplementationGuides;
  }
  /**
   * @return string[]
   */
  public function getEnabledImplementationGuides()
  {
    return $this->enabledImplementationGuides;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidationConfig::class, 'Google_Service_CloudHealthcare_ValidationConfig');
