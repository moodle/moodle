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

class ApplyAdminConsentsRequest extends \Google\Model
{
  protected $newConsentsListType = AdminConsents::class;
  protected $newConsentsListDataType = '';
  /**
   * Optional. If true, the method only validates Consent resources to make sure
   * they are supported. Otherwise, the method applies the aggregate consent
   * information to update the enforcement model and reindex the FHIR resources.
   * If all Consent resources can be applied successfully, the
   * ApplyAdminConsentsResponse is returned containing the following fields: *
   * `consent_apply_success` to indicate the number of Consent resources
   * applied. * `affected_resources` to indicate the number of resources that
   * might have had their consent access changed. If, however, one or more
   * Consent resources are unsupported or cannot be applied, the method fails
   * and ApplyAdminConsentsErrorDetail is is returned with details about the
   * unsupported Consent resources.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * A new list of admin Consent resources to be applied. Any existing enforced
   * Consents, which are specified in `consent_config.enforced_admin_consents`
   * of the FhirStore, that are not part of this list will be disabled. An empty
   * list is equivalent to clearing or disabling all Consents enforced on the
   * FHIR store. When a FHIR store has `disable_resource_versioning=true` and
   * this list contains a Consent resource that exists in
   * `consent_config.enforced_admin_consents`, the method enforces any updates
   * to the existing resource since the last enforcement. If the existing
   * resource hasn't been updated since the last enforcement, the resource is
   * unaffected. After the method finishes, the resulting consent enforcement
   * model is determined by the contents of the Consent resource(s) when the
   * method was called: * When `disable_resource_versioning=true`, the result is
   * identical to the current resource(s) in the FHIR store. * When
   * `disable_resource_versioning=false`, the result is based on the historical
   * version(s) of the Consent resource(s) at the point in time when the method
   * was called. At most 200 Consents can be specified.
   *
   * @param AdminConsents $newConsentsList
   */
  public function setNewConsentsList(AdminConsents $newConsentsList)
  {
    $this->newConsentsList = $newConsentsList;
  }
  /**
   * @return AdminConsents
   */
  public function getNewConsentsList()
  {
    return $this->newConsentsList;
  }
  /**
   * Optional. If true, the method only validates Consent resources to make sure
   * they are supported. Otherwise, the method applies the aggregate consent
   * information to update the enforcement model and reindex the FHIR resources.
   * If all Consent resources can be applied successfully, the
   * ApplyAdminConsentsResponse is returned containing the following fields: *
   * `consent_apply_success` to indicate the number of Consent resources
   * applied. * `affected_resources` to indicate the number of resources that
   * might have had their consent access changed. If, however, one or more
   * Consent resources are unsupported or cannot be applied, the method fails
   * and ApplyAdminConsentsErrorDetail is is returned with details about the
   * unsupported Consent resources.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplyAdminConsentsRequest::class, 'Google_Service_CloudHealthcare_ApplyAdminConsentsRequest');
