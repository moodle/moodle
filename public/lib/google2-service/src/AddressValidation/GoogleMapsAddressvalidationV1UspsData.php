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

class GoogleMapsAddressvalidationV1UspsData extends \Google\Model
{
  /**
   * Abbreviated city.
   *
   * @var string
   */
  public $abbreviatedCity;
  /**
   * Type of the address record that matches the input address. * `F`: FIRM.
   * This is a match to a Firm Record, which is the finest level of match
   * available for an address. * `G`: GENERAL DELIVERY. This is a match to a
   * General Delivery record. * `H`: BUILDING / APARTMENT. This is a match to a
   * Building or Apartment record. * `P`: POST OFFICE BOX. This is a match to a
   * Post Office Box. * `R`: RURAL ROUTE or HIGHWAY CONTRACT: This is a match to
   * either a Rural Route or a Highway Contract record, both of which may have
   * associated Box Number ranges. * `S`: STREET RECORD: This is a match to a
   * Street record containing a valid primary number range.
   *
   * @var string
   */
  public $addressRecordType;
  /**
   * The carrier route code. A four character code consisting of a one letter
   * prefix and a three digit route designator. Prefixes: * `C`: Carrier route
   * (or city route) * `R`: Rural route * `H`: Highway Contract Route * `B`:
   * Post Office Box Section * `G`: General delivery unit
   *
   * @var string
   */
  public $carrierRoute;
  /**
   * Carrier route rate sort indicator.
   *
   * @var string
   */
  public $carrierRouteIndicator;
  /**
   * Indicator that the request has been CASS processed.
   *
   * @var bool
   */
  public $cassProcessed;
  /**
   * County name.
   *
   * @var string
   */
  public $county;
  /**
   * Indicator that a default address was found, but more specific addresses
   * exists.
   *
   * @var bool
   */
  public $defaultAddress;
  /**
   * The delivery point check digit. This number is added to the end of the
   * delivery_point_barcode for mechanically scanned mail. Adding all the digits
   * of the delivery_point_barcode, delivery_point_check_digit, postal code, and
   * ZIP+4 together should yield a number divisible by 10.
   *
   * @var string
   */
  public $deliveryPointCheckDigit;
  /**
   * 2 digit delivery point code
   *
   * @var string
   */
  public $deliveryPointCode;
  /**
   * Indicates if the address is a CMRA (Commercial Mail Receiving Agency)--a
   * private business receiving mail for clients. Returns a single character. *
   * `Y`: The address is a CMRA * `N`: The address is not a CMRA
   *
   * @var string
   */
  public $dpvCmra;
  /**
   * The possible values for DPV confirmation. Returns a single character or
   * returns no value. * `N`: Primary and any secondary number information
   * failed to DPV confirm. * `D`: Address was DPV confirmed for the primary
   * number only, and the secondary number information was missing. * `S`:
   * Address was DPV confirmed for the primary number only, and the secondary
   * number information was present but not confirmed. * `Y`: Address was DPV
   * confirmed for primary and any secondary numbers. * Empty: If the response
   * does not contain a `dpv_confirmation` value, the address was not submitted
   * for DPV confirmation.
   *
   * @var string
   */
  public $dpvConfirmation;
  /**
   * Flag indicates addresses where USPS cannot knock on a door to deliver mail.
   * Returns a single character. * `Y`: The door is not accessible. * `N`: No
   * indication the door is not accessible.
   *
   * @var string
   */
  public $dpvDoorNotAccessible;
  /**
   * Flag indicates mail is delivered to a single receptable at a site. Returns
   * a single character. * `Y`: The mail is delivered to a single receptable at
   * a site. * `N`: The mail is not delivered to a single receptable at a site.
   *
   * @var string
   */
  public $dpvDrop;
  /**
   * Indicates that more than one DPV return code is valid for the address.
   * Returns a single character. * `Y`: Address was DPV confirmed for primary
   * and any secondary numbers. * `N`: Primary and any secondary number
   * information failed to DPV confirm. * `S`: Address was DPV confirmed for the
   * primary number only, and the secondary number information was present but
   * not confirmed, or a single trailing alpha on a primary number was dropped
   * to make a DPV match and secondary information required. * `D`: Address was
   * DPV confirmed for the primary number only, and the secondary number
   * information was missing. * `R`: Address confirmed but assigned to phantom
   * route R777 and R779 and USPS delivery is not provided.
   *
   * @var string
   */
  public $dpvEnhancedDeliveryCode;
  /**
   * The footnotes from delivery point validation. Multiple footnotes may be
   * strung together in the same string. * `AA`: Input address matched to the
   * ZIP+4 file * `A1`: Input address was not matched to the ZIP+4 file * `BB`:
   * Matched to DPV (all components) * `CC`: Secondary number not matched and
   * not required * `C1`: Secondary number not matched but required * `N1`:
   * High-rise address missing secondary number * `M1`: Primary number missing *
   * `M3`: Primary number invalid * `P1`: Input address PO, RR or HC box number
   * missing * `P3`: Input address PO, RR, or HC Box number invalid * `F1`:
   * Input address matched to a military address * `G1`: Input address matched
   * to a general delivery address * `U1`: Input address matched to a unique ZIP
   * code * `PB`: Input address matched to PBSA record * `RR`: DPV confirmed
   * address with PMB information * `R1`: DPV confirmed address without PMB
   * information * `R7`: Carrier Route R777 or R779 record * `IA`: Informed
   * Address identified * `TA`: Primary number matched by dropping a trailing
   * alpha
   *
   * @var string
   */
  public $dpvFootnote;
  /**
   * Flag indicates door is accessible, but package will not be left due to
   * security concerns. Returns a single character. * `Y`: The package will not
   * be left due to security concerns. * `N`: No indication the package will not
   * be left due to security concerns.
   *
   * @var string
   */
  public $dpvNoSecureLocation;
  /**
   * Is this a no stat address or an active address? No stat addresses are ones
   * which are not continuously occupied or addresses that the USPS does not
   * service. Returns a single character. * `Y`: The address is not active *
   * `N`: The address is active
   *
   * @var string
   */
  public $dpvNoStat;
  /**
   * Indicates the NoStat type. Returns a reason code as int. * `1`: IDA
   * (Internal Drop Address) – Addresses that do not receive mail directly from
   * the USPS but are delivered to a drop address that services them. * `2`: CDS
   * - Addresses that have not yet become deliverable. For example, a new
   * subdivision where lots and primary numbers have been determined, but no
   * structure exists yet for occupancy. * `3`: Collision - Addresses that do
   * not actually DPV confirm. * `4`: CMZ (College, Military and Other Types) -
   * ZIP + 4 records USPS has incorporated into the data. * `5`: Regular -
   * Indicates addresses not receiving delivery and the addresses are not
   * counted as possible deliveries. * `6`: Secondary Required - The address
   * requires secondary information.
   *
   * @var int
   */
  public $dpvNoStatReasonCode;
  /**
   * Flag indicates mail delivery is not performed every day of the week.
   * Returns a single character. * `Y`: The mail delivery is not performed every
   * day of the week. * `N`: No indication the mail delivery is not performed
   * every day of the week.
   *
   * @var string
   */
  public $dpvNonDeliveryDays;
  /**
   * Integer identifying non-delivery days. It can be interrogated using bit
   * flags: 0x40 – Sunday is a non-delivery day 0x20 – Monday is a non-delivery
   * day 0x10 – Tuesday is a non-delivery day 0x08 – Wednesday is a non-delivery
   * day 0x04 – Thursday is a non-delivery day 0x02 – Friday is a non-delivery
   * day 0x01 – Saturday is a non-delivery day
   *
   * @var int
   */
  public $dpvNonDeliveryDaysValues;
  /**
   * Indicates the address was matched to PBSA record. Returns a single
   * character. * `Y`: The address was matched to PBSA record. * `N`: The
   * address was not matched to PBSA record.
   *
   * @var string
   */
  public $dpvPbsa;
  /**
   * Indicates that mail is not delivered to the street address. Returns a
   * single character. * `Y`: The mail is not delivered to the street address. *
   * `N`: The mail is delivered to the street address.
   *
   * @var string
   */
  public $dpvThrowback;
  /**
   * Is this place vacant? Returns a single character. * `Y`: The address is
   * vacant * `N`: The address is not vacant
   *
   * @var string
   */
  public $dpvVacant;
  /**
   * eLOT Ascending/Descending Flag (A/D).
   *
   * @var string
   */
  public $elotFlag;
  /**
   * Enhanced Line of Travel (eLOT) number.
   *
   * @var string
   */
  public $elotNumber;
  /**
   * Error message for USPS data retrieval. This is populated when USPS
   * processing is suspended because of the detection of artificially created
   * addresses. The USPS data fields might not be populated when this error is
   * present.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The delivery address is matchable, but the EWS file indicates that an exact
   * match will be available soon.
   *
   * @var bool
   */
  public $ewsNoMatch;
  /**
   * FIPS county code.
   *
   * @var string
   */
  public $fipsCountyCode;
  /**
   * LACSLink indicator.
   *
   * @var string
   */
  public $lacsLinkIndicator;
  /**
   * LACSLink return code.
   *
   * @var string
   */
  public $lacsLinkReturnCode;
  /**
   * PMB (Private Mail Box) unit designator.
   *
   * @var string
   */
  public $pmbDesignator;
  /**
   * PMB (Private Mail Box) number;
   *
   * @var string
   */
  public $pmbNumber;
  /**
   * PO Box only postal code.
   *
   * @var bool
   */
  public $poBoxOnlyPostalCode;
  /**
   * Main post office city.
   *
   * @var string
   */
  public $postOfficeCity;
  /**
   * Main post office state.
   *
   * @var string
   */
  public $postOfficeState;
  protected $standardizedAddressType = GoogleMapsAddressvalidationV1UspsAddress::class;
  protected $standardizedAddressDataType = '';
  /**
   * Footnotes from matching a street or highrise record to suite information.
   * If business name match is found, the secondary number is returned. * `A`:
   * SuiteLink record match, business address improved. * `00`: No match,
   * business address is not improved.
   *
   * @var string
   */
  public $suitelinkFootnote;

  /**
   * Abbreviated city.
   *
   * @param string $abbreviatedCity
   */
  public function setAbbreviatedCity($abbreviatedCity)
  {
    $this->abbreviatedCity = $abbreviatedCity;
  }
  /**
   * @return string
   */
  public function getAbbreviatedCity()
  {
    return $this->abbreviatedCity;
  }
  /**
   * Type of the address record that matches the input address. * `F`: FIRM.
   * This is a match to a Firm Record, which is the finest level of match
   * available for an address. * `G`: GENERAL DELIVERY. This is a match to a
   * General Delivery record. * `H`: BUILDING / APARTMENT. This is a match to a
   * Building or Apartment record. * `P`: POST OFFICE BOX. This is a match to a
   * Post Office Box. * `R`: RURAL ROUTE or HIGHWAY CONTRACT: This is a match to
   * either a Rural Route or a Highway Contract record, both of which may have
   * associated Box Number ranges. * `S`: STREET RECORD: This is a match to a
   * Street record containing a valid primary number range.
   *
   * @param string $addressRecordType
   */
  public function setAddressRecordType($addressRecordType)
  {
    $this->addressRecordType = $addressRecordType;
  }
  /**
   * @return string
   */
  public function getAddressRecordType()
  {
    return $this->addressRecordType;
  }
  /**
   * The carrier route code. A four character code consisting of a one letter
   * prefix and a three digit route designator. Prefixes: * `C`: Carrier route
   * (or city route) * `R`: Rural route * `H`: Highway Contract Route * `B`:
   * Post Office Box Section * `G`: General delivery unit
   *
   * @param string $carrierRoute
   */
  public function setCarrierRoute($carrierRoute)
  {
    $this->carrierRoute = $carrierRoute;
  }
  /**
   * @return string
   */
  public function getCarrierRoute()
  {
    return $this->carrierRoute;
  }
  /**
   * Carrier route rate sort indicator.
   *
   * @param string $carrierRouteIndicator
   */
  public function setCarrierRouteIndicator($carrierRouteIndicator)
  {
    $this->carrierRouteIndicator = $carrierRouteIndicator;
  }
  /**
   * @return string
   */
  public function getCarrierRouteIndicator()
  {
    return $this->carrierRouteIndicator;
  }
  /**
   * Indicator that the request has been CASS processed.
   *
   * @param bool $cassProcessed
   */
  public function setCassProcessed($cassProcessed)
  {
    $this->cassProcessed = $cassProcessed;
  }
  /**
   * @return bool
   */
  public function getCassProcessed()
  {
    return $this->cassProcessed;
  }
  /**
   * County name.
   *
   * @param string $county
   */
  public function setCounty($county)
  {
    $this->county = $county;
  }
  /**
   * @return string
   */
  public function getCounty()
  {
    return $this->county;
  }
  /**
   * Indicator that a default address was found, but more specific addresses
   * exists.
   *
   * @param bool $defaultAddress
   */
  public function setDefaultAddress($defaultAddress)
  {
    $this->defaultAddress = $defaultAddress;
  }
  /**
   * @return bool
   */
  public function getDefaultAddress()
  {
    return $this->defaultAddress;
  }
  /**
   * The delivery point check digit. This number is added to the end of the
   * delivery_point_barcode for mechanically scanned mail. Adding all the digits
   * of the delivery_point_barcode, delivery_point_check_digit, postal code, and
   * ZIP+4 together should yield a number divisible by 10.
   *
   * @param string $deliveryPointCheckDigit
   */
  public function setDeliveryPointCheckDigit($deliveryPointCheckDigit)
  {
    $this->deliveryPointCheckDigit = $deliveryPointCheckDigit;
  }
  /**
   * @return string
   */
  public function getDeliveryPointCheckDigit()
  {
    return $this->deliveryPointCheckDigit;
  }
  /**
   * 2 digit delivery point code
   *
   * @param string $deliveryPointCode
   */
  public function setDeliveryPointCode($deliveryPointCode)
  {
    $this->deliveryPointCode = $deliveryPointCode;
  }
  /**
   * @return string
   */
  public function getDeliveryPointCode()
  {
    return $this->deliveryPointCode;
  }
  /**
   * Indicates if the address is a CMRA (Commercial Mail Receiving Agency)--a
   * private business receiving mail for clients. Returns a single character. *
   * `Y`: The address is a CMRA * `N`: The address is not a CMRA
   *
   * @param string $dpvCmra
   */
  public function setDpvCmra($dpvCmra)
  {
    $this->dpvCmra = $dpvCmra;
  }
  /**
   * @return string
   */
  public function getDpvCmra()
  {
    return $this->dpvCmra;
  }
  /**
   * The possible values for DPV confirmation. Returns a single character or
   * returns no value. * `N`: Primary and any secondary number information
   * failed to DPV confirm. * `D`: Address was DPV confirmed for the primary
   * number only, and the secondary number information was missing. * `S`:
   * Address was DPV confirmed for the primary number only, and the secondary
   * number information was present but not confirmed. * `Y`: Address was DPV
   * confirmed for primary and any secondary numbers. * Empty: If the response
   * does not contain a `dpv_confirmation` value, the address was not submitted
   * for DPV confirmation.
   *
   * @param string $dpvConfirmation
   */
  public function setDpvConfirmation($dpvConfirmation)
  {
    $this->dpvConfirmation = $dpvConfirmation;
  }
  /**
   * @return string
   */
  public function getDpvConfirmation()
  {
    return $this->dpvConfirmation;
  }
  /**
   * Flag indicates addresses where USPS cannot knock on a door to deliver mail.
   * Returns a single character. * `Y`: The door is not accessible. * `N`: No
   * indication the door is not accessible.
   *
   * @param string $dpvDoorNotAccessible
   */
  public function setDpvDoorNotAccessible($dpvDoorNotAccessible)
  {
    $this->dpvDoorNotAccessible = $dpvDoorNotAccessible;
  }
  /**
   * @return string
   */
  public function getDpvDoorNotAccessible()
  {
    return $this->dpvDoorNotAccessible;
  }
  /**
   * Flag indicates mail is delivered to a single receptable at a site. Returns
   * a single character. * `Y`: The mail is delivered to a single receptable at
   * a site. * `N`: The mail is not delivered to a single receptable at a site.
   *
   * @param string $dpvDrop
   */
  public function setDpvDrop($dpvDrop)
  {
    $this->dpvDrop = $dpvDrop;
  }
  /**
   * @return string
   */
  public function getDpvDrop()
  {
    return $this->dpvDrop;
  }
  /**
   * Indicates that more than one DPV return code is valid for the address.
   * Returns a single character. * `Y`: Address was DPV confirmed for primary
   * and any secondary numbers. * `N`: Primary and any secondary number
   * information failed to DPV confirm. * `S`: Address was DPV confirmed for the
   * primary number only, and the secondary number information was present but
   * not confirmed, or a single trailing alpha on a primary number was dropped
   * to make a DPV match and secondary information required. * `D`: Address was
   * DPV confirmed for the primary number only, and the secondary number
   * information was missing. * `R`: Address confirmed but assigned to phantom
   * route R777 and R779 and USPS delivery is not provided.
   *
   * @param string $dpvEnhancedDeliveryCode
   */
  public function setDpvEnhancedDeliveryCode($dpvEnhancedDeliveryCode)
  {
    $this->dpvEnhancedDeliveryCode = $dpvEnhancedDeliveryCode;
  }
  /**
   * @return string
   */
  public function getDpvEnhancedDeliveryCode()
  {
    return $this->dpvEnhancedDeliveryCode;
  }
  /**
   * The footnotes from delivery point validation. Multiple footnotes may be
   * strung together in the same string. * `AA`: Input address matched to the
   * ZIP+4 file * `A1`: Input address was not matched to the ZIP+4 file * `BB`:
   * Matched to DPV (all components) * `CC`: Secondary number not matched and
   * not required * `C1`: Secondary number not matched but required * `N1`:
   * High-rise address missing secondary number * `M1`: Primary number missing *
   * `M3`: Primary number invalid * `P1`: Input address PO, RR or HC box number
   * missing * `P3`: Input address PO, RR, or HC Box number invalid * `F1`:
   * Input address matched to a military address * `G1`: Input address matched
   * to a general delivery address * `U1`: Input address matched to a unique ZIP
   * code * `PB`: Input address matched to PBSA record * `RR`: DPV confirmed
   * address with PMB information * `R1`: DPV confirmed address without PMB
   * information * `R7`: Carrier Route R777 or R779 record * `IA`: Informed
   * Address identified * `TA`: Primary number matched by dropping a trailing
   * alpha
   *
   * @param string $dpvFootnote
   */
  public function setDpvFootnote($dpvFootnote)
  {
    $this->dpvFootnote = $dpvFootnote;
  }
  /**
   * @return string
   */
  public function getDpvFootnote()
  {
    return $this->dpvFootnote;
  }
  /**
   * Flag indicates door is accessible, but package will not be left due to
   * security concerns. Returns a single character. * `Y`: The package will not
   * be left due to security concerns. * `N`: No indication the package will not
   * be left due to security concerns.
   *
   * @param string $dpvNoSecureLocation
   */
  public function setDpvNoSecureLocation($dpvNoSecureLocation)
  {
    $this->dpvNoSecureLocation = $dpvNoSecureLocation;
  }
  /**
   * @return string
   */
  public function getDpvNoSecureLocation()
  {
    return $this->dpvNoSecureLocation;
  }
  /**
   * Is this a no stat address or an active address? No stat addresses are ones
   * which are not continuously occupied or addresses that the USPS does not
   * service. Returns a single character. * `Y`: The address is not active *
   * `N`: The address is active
   *
   * @param string $dpvNoStat
   */
  public function setDpvNoStat($dpvNoStat)
  {
    $this->dpvNoStat = $dpvNoStat;
  }
  /**
   * @return string
   */
  public function getDpvNoStat()
  {
    return $this->dpvNoStat;
  }
  /**
   * Indicates the NoStat type. Returns a reason code as int. * `1`: IDA
   * (Internal Drop Address) – Addresses that do not receive mail directly from
   * the USPS but are delivered to a drop address that services them. * `2`: CDS
   * - Addresses that have not yet become deliverable. For example, a new
   * subdivision where lots and primary numbers have been determined, but no
   * structure exists yet for occupancy. * `3`: Collision - Addresses that do
   * not actually DPV confirm. * `4`: CMZ (College, Military and Other Types) -
   * ZIP + 4 records USPS has incorporated into the data. * `5`: Regular -
   * Indicates addresses not receiving delivery and the addresses are not
   * counted as possible deliveries. * `6`: Secondary Required - The address
   * requires secondary information.
   *
   * @param int $dpvNoStatReasonCode
   */
  public function setDpvNoStatReasonCode($dpvNoStatReasonCode)
  {
    $this->dpvNoStatReasonCode = $dpvNoStatReasonCode;
  }
  /**
   * @return int
   */
  public function getDpvNoStatReasonCode()
  {
    return $this->dpvNoStatReasonCode;
  }
  /**
   * Flag indicates mail delivery is not performed every day of the week.
   * Returns a single character. * `Y`: The mail delivery is not performed every
   * day of the week. * `N`: No indication the mail delivery is not performed
   * every day of the week.
   *
   * @param string $dpvNonDeliveryDays
   */
  public function setDpvNonDeliveryDays($dpvNonDeliveryDays)
  {
    $this->dpvNonDeliveryDays = $dpvNonDeliveryDays;
  }
  /**
   * @return string
   */
  public function getDpvNonDeliveryDays()
  {
    return $this->dpvNonDeliveryDays;
  }
  /**
   * Integer identifying non-delivery days. It can be interrogated using bit
   * flags: 0x40 – Sunday is a non-delivery day 0x20 – Monday is a non-delivery
   * day 0x10 – Tuesday is a non-delivery day 0x08 – Wednesday is a non-delivery
   * day 0x04 – Thursday is a non-delivery day 0x02 – Friday is a non-delivery
   * day 0x01 – Saturday is a non-delivery day
   *
   * @param int $dpvNonDeliveryDaysValues
   */
  public function setDpvNonDeliveryDaysValues($dpvNonDeliveryDaysValues)
  {
    $this->dpvNonDeliveryDaysValues = $dpvNonDeliveryDaysValues;
  }
  /**
   * @return int
   */
  public function getDpvNonDeliveryDaysValues()
  {
    return $this->dpvNonDeliveryDaysValues;
  }
  /**
   * Indicates the address was matched to PBSA record. Returns a single
   * character. * `Y`: The address was matched to PBSA record. * `N`: The
   * address was not matched to PBSA record.
   *
   * @param string $dpvPbsa
   */
  public function setDpvPbsa($dpvPbsa)
  {
    $this->dpvPbsa = $dpvPbsa;
  }
  /**
   * @return string
   */
  public function getDpvPbsa()
  {
    return $this->dpvPbsa;
  }
  /**
   * Indicates that mail is not delivered to the street address. Returns a
   * single character. * `Y`: The mail is not delivered to the street address. *
   * `N`: The mail is delivered to the street address.
   *
   * @param string $dpvThrowback
   */
  public function setDpvThrowback($dpvThrowback)
  {
    $this->dpvThrowback = $dpvThrowback;
  }
  /**
   * @return string
   */
  public function getDpvThrowback()
  {
    return $this->dpvThrowback;
  }
  /**
   * Is this place vacant? Returns a single character. * `Y`: The address is
   * vacant * `N`: The address is not vacant
   *
   * @param string $dpvVacant
   */
  public function setDpvVacant($dpvVacant)
  {
    $this->dpvVacant = $dpvVacant;
  }
  /**
   * @return string
   */
  public function getDpvVacant()
  {
    return $this->dpvVacant;
  }
  /**
   * eLOT Ascending/Descending Flag (A/D).
   *
   * @param string $elotFlag
   */
  public function setElotFlag($elotFlag)
  {
    $this->elotFlag = $elotFlag;
  }
  /**
   * @return string
   */
  public function getElotFlag()
  {
    return $this->elotFlag;
  }
  /**
   * Enhanced Line of Travel (eLOT) number.
   *
   * @param string $elotNumber
   */
  public function setElotNumber($elotNumber)
  {
    $this->elotNumber = $elotNumber;
  }
  /**
   * @return string
   */
  public function getElotNumber()
  {
    return $this->elotNumber;
  }
  /**
   * Error message for USPS data retrieval. This is populated when USPS
   * processing is suspended because of the detection of artificially created
   * addresses. The USPS data fields might not be populated when this error is
   * present.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * The delivery address is matchable, but the EWS file indicates that an exact
   * match will be available soon.
   *
   * @param bool $ewsNoMatch
   */
  public function setEwsNoMatch($ewsNoMatch)
  {
    $this->ewsNoMatch = $ewsNoMatch;
  }
  /**
   * @return bool
   */
  public function getEwsNoMatch()
  {
    return $this->ewsNoMatch;
  }
  /**
   * FIPS county code.
   *
   * @param string $fipsCountyCode
   */
  public function setFipsCountyCode($fipsCountyCode)
  {
    $this->fipsCountyCode = $fipsCountyCode;
  }
  /**
   * @return string
   */
  public function getFipsCountyCode()
  {
    return $this->fipsCountyCode;
  }
  /**
   * LACSLink indicator.
   *
   * @param string $lacsLinkIndicator
   */
  public function setLacsLinkIndicator($lacsLinkIndicator)
  {
    $this->lacsLinkIndicator = $lacsLinkIndicator;
  }
  /**
   * @return string
   */
  public function getLacsLinkIndicator()
  {
    return $this->lacsLinkIndicator;
  }
  /**
   * LACSLink return code.
   *
   * @param string $lacsLinkReturnCode
   */
  public function setLacsLinkReturnCode($lacsLinkReturnCode)
  {
    $this->lacsLinkReturnCode = $lacsLinkReturnCode;
  }
  /**
   * @return string
   */
  public function getLacsLinkReturnCode()
  {
    return $this->lacsLinkReturnCode;
  }
  /**
   * PMB (Private Mail Box) unit designator.
   *
   * @param string $pmbDesignator
   */
  public function setPmbDesignator($pmbDesignator)
  {
    $this->pmbDesignator = $pmbDesignator;
  }
  /**
   * @return string
   */
  public function getPmbDesignator()
  {
    return $this->pmbDesignator;
  }
  /**
   * PMB (Private Mail Box) number;
   *
   * @param string $pmbNumber
   */
  public function setPmbNumber($pmbNumber)
  {
    $this->pmbNumber = $pmbNumber;
  }
  /**
   * @return string
   */
  public function getPmbNumber()
  {
    return $this->pmbNumber;
  }
  /**
   * PO Box only postal code.
   *
   * @param bool $poBoxOnlyPostalCode
   */
  public function setPoBoxOnlyPostalCode($poBoxOnlyPostalCode)
  {
    $this->poBoxOnlyPostalCode = $poBoxOnlyPostalCode;
  }
  /**
   * @return bool
   */
  public function getPoBoxOnlyPostalCode()
  {
    return $this->poBoxOnlyPostalCode;
  }
  /**
   * Main post office city.
   *
   * @param string $postOfficeCity
   */
  public function setPostOfficeCity($postOfficeCity)
  {
    $this->postOfficeCity = $postOfficeCity;
  }
  /**
   * @return string
   */
  public function getPostOfficeCity()
  {
    return $this->postOfficeCity;
  }
  /**
   * Main post office state.
   *
   * @param string $postOfficeState
   */
  public function setPostOfficeState($postOfficeState)
  {
    $this->postOfficeState = $postOfficeState;
  }
  /**
   * @return string
   */
  public function getPostOfficeState()
  {
    return $this->postOfficeState;
  }
  /**
   * USPS standardized address.
   *
   * @param GoogleMapsAddressvalidationV1UspsAddress $standardizedAddress
   */
  public function setStandardizedAddress(GoogleMapsAddressvalidationV1UspsAddress $standardizedAddress)
  {
    $this->standardizedAddress = $standardizedAddress;
  }
  /**
   * @return GoogleMapsAddressvalidationV1UspsAddress
   */
  public function getStandardizedAddress()
  {
    return $this->standardizedAddress;
  }
  /**
   * Footnotes from matching a street or highrise record to suite information.
   * If business name match is found, the secondary number is returned. * `A`:
   * SuiteLink record match, business address improved. * `00`: No match,
   * business address is not improved.
   *
   * @param string $suitelinkFootnote
   */
  public function setSuitelinkFootnote($suitelinkFootnote)
  {
    $this->suitelinkFootnote = $suitelinkFootnote;
  }
  /**
   * @return string
   */
  public function getSuitelinkFootnote()
  {
    return $this->suitelinkFootnote;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1UspsData::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1UspsData');
