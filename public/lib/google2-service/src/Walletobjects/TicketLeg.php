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

namespace Google\Service\Walletobjects;

class TicketLeg extends \Google\Collection
{
  protected $collection_key = 'ticketSeats';
  /**
   * The date/time of arrival. This is an ISO 8601 extended format date/time,
   * with or without an offset. Time may be specified up to nanosecond
   * precision. Offsets may be specified with seconds precision (even though
   * offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the destination station. For example, if
   * the event occurs at the 20th hour of June 5th, 2018 at the destination
   * station, the local date/time portion should be `2018-06-05T20:00:00`. If
   * the local date/time at the destination station is 4 hours before UTC, an
   * offset of `-04:00` may be appended. Without offset information, some rich
   * features may not be available.
   *
   * @var string
   */
  public $arrivalDateTime;
  /**
   * The train or ship name/number that the passsenger needs to board.
   *
   * @var string
   */
  public $carriage;
  /**
   * The date/time of departure. This is required if there is no validity time
   * interval set on the transit object. This is an ISO 8601 extended format
   * date/time, with or without an offset. Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the origin station. For example, if the
   * departure occurs at the 20th hour of June 5th, 2018 at the origin station,
   * the local date/time portion should be `2018-06-05T20:00:00`. If the local
   * date/time at the origin station is 4 hours before UTC, an offset of
   * `-04:00` may be appended. Without offset information, some rich features
   * may not be available.
   *
   * @var string
   */
  public $departureDateTime;
  protected $destinationNameType = LocalizedString::class;
  protected $destinationNameDataType = '';
  /**
   * The destination station code.
   *
   * @var string
   */
  public $destinationStationCode;
  protected $fareNameType = LocalizedString::class;
  protected $fareNameDataType = '';
  protected $originNameType = LocalizedString::class;
  protected $originNameDataType = '';
  /**
   * The origin station code. This is required if `destinationStationCode` is
   * present or if `originName` is not present.
   *
   * @var string
   */
  public $originStationCode;
  /**
   * The platform or gate where the passenger can board the carriage.
   *
   * @var string
   */
  public $platform;
  protected $ticketSeatType = TicketSeat::class;
  protected $ticketSeatDataType = '';
  protected $ticketSeatsType = TicketSeat::class;
  protected $ticketSeatsDataType = 'array';
  protected $transitOperatorNameType = LocalizedString::class;
  protected $transitOperatorNameDataType = '';
  protected $transitTerminusNameType = LocalizedString::class;
  protected $transitTerminusNameDataType = '';
  /**
   * The zone of boarding within the platform.
   *
   * @var string
   */
  public $zone;

  /**
   * The date/time of arrival. This is an ISO 8601 extended format date/time,
   * with or without an offset. Time may be specified up to nanosecond
   * precision. Offsets may be specified with seconds precision (even though
   * offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the destination station. For example, if
   * the event occurs at the 20th hour of June 5th, 2018 at the destination
   * station, the local date/time portion should be `2018-06-05T20:00:00`. If
   * the local date/time at the destination station is 4 hours before UTC, an
   * offset of `-04:00` may be appended. Without offset information, some rich
   * features may not be available.
   *
   * @param string $arrivalDateTime
   */
  public function setArrivalDateTime($arrivalDateTime)
  {
    $this->arrivalDateTime = $arrivalDateTime;
  }
  /**
   * @return string
   */
  public function getArrivalDateTime()
  {
    return $this->arrivalDateTime;
  }
  /**
   * The train or ship name/number that the passsenger needs to board.
   *
   * @param string $carriage
   */
  public function setCarriage($carriage)
  {
    $this->carriage = $carriage;
  }
  /**
   * @return string
   */
  public function getCarriage()
  {
    return $this->carriage;
  }
  /**
   * The date/time of departure. This is required if there is no validity time
   * interval set on the transit object. This is an ISO 8601 extended format
   * date/time, with or without an offset. Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the origin station. For example, if the
   * departure occurs at the 20th hour of June 5th, 2018 at the origin station,
   * the local date/time portion should be `2018-06-05T20:00:00`. If the local
   * date/time at the origin station is 4 hours before UTC, an offset of
   * `-04:00` may be appended. Without offset information, some rich features
   * may not be available.
   *
   * @param string $departureDateTime
   */
  public function setDepartureDateTime($departureDateTime)
  {
    $this->departureDateTime = $departureDateTime;
  }
  /**
   * @return string
   */
  public function getDepartureDateTime()
  {
    return $this->departureDateTime;
  }
  /**
   * The destination name.
   *
   * @param LocalizedString $destinationName
   */
  public function setDestinationName(LocalizedString $destinationName)
  {
    $this->destinationName = $destinationName;
  }
  /**
   * @return LocalizedString
   */
  public function getDestinationName()
  {
    return $this->destinationName;
  }
  /**
   * The destination station code.
   *
   * @param string $destinationStationCode
   */
  public function setDestinationStationCode($destinationStationCode)
  {
    $this->destinationStationCode = $destinationStationCode;
  }
  /**
   * @return string
   */
  public function getDestinationStationCode()
  {
    return $this->destinationStationCode;
  }
  /**
   * Short description/name of the fare for this leg of travel. Eg "Anytime
   * Single Use".
   *
   * @param LocalizedString $fareName
   */
  public function setFareName(LocalizedString $fareName)
  {
    $this->fareName = $fareName;
  }
  /**
   * @return LocalizedString
   */
  public function getFareName()
  {
    return $this->fareName;
  }
  /**
   * The name of the origin station. This is required if `desinationName` is
   * present or if `originStationCode` is not present.
   *
   * @param LocalizedString $originName
   */
  public function setOriginName(LocalizedString $originName)
  {
    $this->originName = $originName;
  }
  /**
   * @return LocalizedString
   */
  public function getOriginName()
  {
    return $this->originName;
  }
  /**
   * The origin station code. This is required if `destinationStationCode` is
   * present or if `originName` is not present.
   *
   * @param string $originStationCode
   */
  public function setOriginStationCode($originStationCode)
  {
    $this->originStationCode = $originStationCode;
  }
  /**
   * @return string
   */
  public function getOriginStationCode()
  {
    return $this->originStationCode;
  }
  /**
   * The platform or gate where the passenger can board the carriage.
   *
   * @param string $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return string
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * The reserved seat for the passenger(s). If more than one seat is to be
   * specified then use the `ticketSeats` field instead. Both `ticketSeat` and
   * `ticketSeats` may not be set.
   *
   * @param TicketSeat $ticketSeat
   */
  public function setTicketSeat(TicketSeat $ticketSeat)
  {
    $this->ticketSeat = $ticketSeat;
  }
  /**
   * @return TicketSeat
   */
  public function getTicketSeat()
  {
    return $this->ticketSeat;
  }
  /**
   * The reserved seat for the passenger(s). If only one seat is to be specified
   * then use the `ticketSeat` field instead. Both `ticketSeat` and
   * `ticketSeats` may not be set.
   *
   * @param TicketSeat[] $ticketSeats
   */
  public function setTicketSeats($ticketSeats)
  {
    $this->ticketSeats = $ticketSeats;
  }
  /**
   * @return TicketSeat[]
   */
  public function getTicketSeats()
  {
    return $this->ticketSeats;
  }
  /**
   * The name of the transit operator that is operating this leg of a trip.
   *
   * @param LocalizedString $transitOperatorName
   */
  public function setTransitOperatorName(LocalizedString $transitOperatorName)
  {
    $this->transitOperatorName = $transitOperatorName;
  }
  /**
   * @return LocalizedString
   */
  public function getTransitOperatorName()
  {
    return $this->transitOperatorName;
  }
  /**
   * Terminus station or destination of the train/bus/etc.
   *
   * @param LocalizedString $transitTerminusName
   */
  public function setTransitTerminusName(LocalizedString $transitTerminusName)
  {
    $this->transitTerminusName = $transitTerminusName;
  }
  /**
   * @return LocalizedString
   */
  public function getTransitTerminusName()
  {
    return $this->transitTerminusName;
  }
  /**
   * The zone of boarding within the platform.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TicketLeg::class, 'Google_Service_Walletobjects_TicketLeg');
