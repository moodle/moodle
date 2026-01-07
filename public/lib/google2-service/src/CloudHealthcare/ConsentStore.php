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

class ConsentStore extends \Google\Model
{
  /**
   * Optional. Default time to live for Consents created in this store. Must be
   * at least 24 hours. Updating this field will not affect the expiration time
   * of existing consents.
   *
   * @var string
   */
  public $defaultConsentTtl;
  /**
   * Optional. If `true`, UpdateConsent creates the Consent if it does not
   * already exist. If unspecified, defaults to `false`.
   *
   * @var bool
   */
  public $enableConsentCreateOnUpdate;
  /**
   * Optional. User-supplied key-value pairs used to organize consent stores.
   * Label keys must be between 1 and 63 characters long, have a UTF-8 encoding
   * of maximum 128 bytes, and must conform to the following PCRE regular
   * expression: \p{Ll}\p{Lo}{0,62}. Label values must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63}. No more than 64 labels can be associated with
   * a given store. For more information:
   * https://cloud.google.com/healthcare/docs/how-tos/labeling-resources
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Resource name of the consent store, of the form `projects/{proj
   * ect_id}/locations/{location_id}/datasets/{dataset_id}/consentStores/{consen
   * t_store_id}`. Cannot be changed after creation.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Default time to live for Consents created in this store. Must be
   * at least 24 hours. Updating this field will not affect the expiration time
   * of existing consents.
   *
   * @param string $defaultConsentTtl
   */
  public function setDefaultConsentTtl($defaultConsentTtl)
  {
    $this->defaultConsentTtl = $defaultConsentTtl;
  }
  /**
   * @return string
   */
  public function getDefaultConsentTtl()
  {
    return $this->defaultConsentTtl;
  }
  /**
   * Optional. If `true`, UpdateConsent creates the Consent if it does not
   * already exist. If unspecified, defaults to `false`.
   *
   * @param bool $enableConsentCreateOnUpdate
   */
  public function setEnableConsentCreateOnUpdate($enableConsentCreateOnUpdate)
  {
    $this->enableConsentCreateOnUpdate = $enableConsentCreateOnUpdate;
  }
  /**
   * @return bool
   */
  public function getEnableConsentCreateOnUpdate()
  {
    return $this->enableConsentCreateOnUpdate;
  }
  /**
   * Optional. User-supplied key-value pairs used to organize consent stores.
   * Label keys must be between 1 and 63 characters long, have a UTF-8 encoding
   * of maximum 128 bytes, and must conform to the following PCRE regular
   * expression: \p{Ll}\p{Lo}{0,62}. Label values must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63}. No more than 64 labels can be associated with
   * a given store. For more information:
   * https://cloud.google.com/healthcare/docs/how-tos/labeling-resources
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Resource name of the consent store, of the form `projects/{proj
   * ect_id}/locations/{location_id}/datasets/{dataset_id}/consentStores/{consen
   * t_store_id}`. Cannot be changed after creation.
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
class_alias(ConsentStore::class, 'Google_Service_CloudHealthcare_ConsentStore');
