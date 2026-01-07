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

namespace Google\Service\Localservices;

class GoogleAdsHomeservicesLocalservicesV1BookingLead extends \Google\Model
{
  /**
   * Timestamp of when service is provided by advertiser.
   *
   * @var string
   */
  public $bookingAppointmentTimestamp;
  /**
   * Consumer email associated with the booking lead.
   *
   * @var string
   */
  public $consumerEmail;
  /**
   * Consumer phone number associated with the booking lead.
   *
   * @var string
   */
  public $consumerPhoneNumber;
  /**
   * Name of the customer who created the lead.
   *
   * @var string
   */
  public $customerName;
  /**
   * The job type of the specified lead.
   *
   * @var string
   */
  public $jobType;

  /**
   * Timestamp of when service is provided by advertiser.
   *
   * @param string $bookingAppointmentTimestamp
   */
  public function setBookingAppointmentTimestamp($bookingAppointmentTimestamp)
  {
    $this->bookingAppointmentTimestamp = $bookingAppointmentTimestamp;
  }
  /**
   * @return string
   */
  public function getBookingAppointmentTimestamp()
  {
    return $this->bookingAppointmentTimestamp;
  }
  /**
   * Consumer email associated with the booking lead.
   *
   * @param string $consumerEmail
   */
  public function setConsumerEmail($consumerEmail)
  {
    $this->consumerEmail = $consumerEmail;
  }
  /**
   * @return string
   */
  public function getConsumerEmail()
  {
    return $this->consumerEmail;
  }
  /**
   * Consumer phone number associated with the booking lead.
   *
   * @param string $consumerPhoneNumber
   */
  public function setConsumerPhoneNumber($consumerPhoneNumber)
  {
    $this->consumerPhoneNumber = $consumerPhoneNumber;
  }
  /**
   * @return string
   */
  public function getConsumerPhoneNumber()
  {
    return $this->consumerPhoneNumber;
  }
  /**
   * Name of the customer who created the lead.
   *
   * @param string $customerName
   */
  public function setCustomerName($customerName)
  {
    $this->customerName = $customerName;
  }
  /**
   * @return string
   */
  public function getCustomerName()
  {
    return $this->customerName;
  }
  /**
   * The job type of the specified lead.
   *
   * @param string $jobType
   */
  public function setJobType($jobType)
  {
    $this->jobType = $jobType;
  }
  /**
   * @return string
   */
  public function getJobType()
  {
    return $this->jobType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsHomeservicesLocalservicesV1BookingLead::class, 'Google_Service_Localservices_GoogleAdsHomeservicesLocalservicesV1BookingLead');
