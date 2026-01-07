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

namespace Google\Service\AddressValidation;

class GoogleMapsAddressvalidationV1AddressComponent extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const CONFIRMATION_LEVEL_CONFIRMATION_LEVEL_UNSPECIFIED = 'CONFIRMATION_LEVEL_UNSPECIFIED';
  /**
   * We were able to verify that this component exists and makes sense in the
   * context of the rest of the address.
   */
  public const CONFIRMATION_LEVEL_CONFIRMED = 'CONFIRMED';
  /**
   * This component could not be confirmed, but it is plausible that it exists.
   * For example, a street number within a known valid range of numbers on a
   * street where specific house numbers are not known.
   */
  public const CONFIRMATION_LEVEL_UNCONFIRMED_BUT_PLAUSIBLE = 'UNCONFIRMED_BUT_PLAUSIBLE';
  /**
   * This component was not confirmed and is likely to be wrong. For example, a
   * neighborhood that does not fit the rest of the address.
   */
  public const CONFIRMATION_LEVEL_UNCONFIRMED_AND_SUSPICIOUS = 'UNCONFIRMED_AND_SUSPICIOUS';
  protected $componentNameType = GoogleMapsAddressvalidationV1ComponentName::class;
  protected $componentNameDataType = '';
  /**
   * The type of the address component. See [Table 2: Additional types returned
   * by the Places service](https://developers.google.com/places/web-
   * service/supported_types#table2) for a list of possible types.
   *
   * @var string
   */
  public $componentType;
  /**
   * Indicates the level of certainty that we have that the component is
   * correct.
   *
   * @var string
   */
  public $confirmationLevel;
  /**
   * Indicates that the component was not part of the input, but we inferred it
   * for the address location and believe it should be provided for a complete
   * address.
   *
   * @var bool
   */
  public $inferred;
  /**
   * Indicates the name of the component was replaced with a completely
   * different one, for example a wrong postal code being replaced with one that
   * is correct for the address. This is not a cosmetic change, the input
   * component has been changed to a different one.
   *
   * @var bool
   */
  public $replaced;
  /**
   * Indicates a correction to a misspelling in the component name. The API does
   * not always flag changes from one spelling variant to another, such as when
   * changing "centre" to "center". It also does not always flag common
   * misspellings, such as when changing "Amphitheater Pkwy" to "Amphitheatre
   * Pkwy".
   *
   * @var bool
   */
  public $spellCorrected;
  /**
   * Indicates an address component that is not expected to be present in a
   * postal address for the given region. We have retained it only because it
   * was part of the input.
   *
   * @var bool
   */
  public $unexpected;

  /**
   * The name for this component.
   *
   * @param GoogleMapsAddressvalidationV1ComponentName $componentName
   */
  public function setComponentName(GoogleMapsAddressvalidationV1ComponentName $componentName)
  {
    $this->componentName = $componentName;
  }
  /**
   * @return GoogleMapsAddressvalidationV1ComponentName
   */
  public function getComponentName()
  {
    return $this->componentName;
  }
  /**
   * The type of the address component. See [Table 2: Additional types returned
   * by the Places service](https://developers.google.com/places/web-
   * service/supported_types#table2) for a list of possible types.
   *
   * @param string $componentType
   */
  public function setComponentType($componentType)
  {
    $this->componentType = $componentType;
  }
  /**
   * @return string
   */
  public function getComponentType()
  {
    return $this->componentType;
  }
  /**
   * Indicates the level of certainty that we have that the component is
   * correct.
   *
   * Accepted values: CONFIRMATION_LEVEL_UNSPECIFIED, CONFIRMED,
   * UNCONFIRMED_BUT_PLAUSIBLE, UNCONFIRMED_AND_SUSPICIOUS
   *
   * @param self::CONFIRMATION_LEVEL_* $confirmationLevel
   */
  public function setConfirmationLevel($confirmationLevel)
  {
    $this->confirmationLevel = $confirmationLevel;
  }
  /**
   * @return self::CONFIRMATION_LEVEL_*
   */
  public function getConfirmationLevel()
  {
    return $this->confirmationLevel;
  }
  /**
   * Indicates that the component was not part of the input, but we inferred it
   * for the address location and believe it should be provided for a complete
   * address.
   *
   * @param bool $inferred
   */
  public function setInferred($inferred)
  {
    $this->inferred = $inferred;
  }
  /**
   * @return bool
   */
  public function getInferred()
  {
    return $this->inferred;
  }
  /**
   * Indicates the name of the component was replaced with a completely
   * different one, for example a wrong postal code being replaced with one that
   * is correct for the address. This is not a cosmetic change, the input
   * component has been changed to a different one.
   *
   * @param bool $replaced
   */
  public function setReplaced($replaced)
  {
    $this->replaced = $replaced;
  }
  /**
   * @return bool
   */
  public function getReplaced()
  {
    return $this->replaced;
  }
  /**
   * Indicates a correction to a misspelling in the component name. The API does
   * not always flag changes from one spelling variant to another, such as when
   * changing "centre" to "center". It also does not always flag common
   * misspellings, such as when changing "Amphitheater Pkwy" to "Amphitheatre
   * Pkwy".
   *
   * @param bool $spellCorrected
   */
  public function setSpellCorrected($spellCorrected)
  {
    $this->spellCorrected = $spellCorrected;
  }
  /**
   * @return bool
   */
  public function getSpellCorrected()
  {
    return $this->spellCorrected;
  }
  /**
   * Indicates an address component that is not expected to be present in a
   * postal address for the given region. We have retained it only because it
   * was part of the input.
   *
   * @param bool $unexpected
   */
  public function setUnexpected($unexpected)
  {
    $this->unexpected = $unexpected;
  }
  /**
   * @return bool
   */
  public function getUnexpected()
  {
    return $this->unexpected;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1AddressComponent::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1AddressComponent');
