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

class Resources extends \Google\Collection
{
  protected $collection_key = 'transitObjects';
  protected $eventTicketClassesType = EventTicketClass::class;
  protected $eventTicketClassesDataType = 'array';
  protected $eventTicketObjectsType = EventTicketObject::class;
  protected $eventTicketObjectsDataType = 'array';
  protected $flightClassesType = FlightClass::class;
  protected $flightClassesDataType = 'array';
  protected $flightObjectsType = FlightObject::class;
  protected $flightObjectsDataType = 'array';
  protected $genericClassesType = GenericClass::class;
  protected $genericClassesDataType = 'array';
  protected $genericObjectsType = GenericObject::class;
  protected $genericObjectsDataType = 'array';
  protected $giftCardClassesType = GiftCardClass::class;
  protected $giftCardClassesDataType = 'array';
  protected $giftCardObjectsType = GiftCardObject::class;
  protected $giftCardObjectsDataType = 'array';
  protected $loyaltyClassesType = LoyaltyClass::class;
  protected $loyaltyClassesDataType = 'array';
  protected $loyaltyObjectsType = LoyaltyObject::class;
  protected $loyaltyObjectsDataType = 'array';
  protected $offerClassesType = OfferClass::class;
  protected $offerClassesDataType = 'array';
  protected $offerObjectsType = OfferObject::class;
  protected $offerObjectsDataType = 'array';
  protected $transitClassesType = TransitClass::class;
  protected $transitClassesDataType = 'array';
  protected $transitObjectsType = TransitObject::class;
  protected $transitObjectsDataType = 'array';

  /**
   * A list of event ticket classes.
   *
   * @param EventTicketClass[] $eventTicketClasses
   */
  public function setEventTicketClasses($eventTicketClasses)
  {
    $this->eventTicketClasses = $eventTicketClasses;
  }
  /**
   * @return EventTicketClass[]
   */
  public function getEventTicketClasses()
  {
    return $this->eventTicketClasses;
  }
  /**
   * A list of event ticket objects.
   *
   * @param EventTicketObject[] $eventTicketObjects
   */
  public function setEventTicketObjects($eventTicketObjects)
  {
    $this->eventTicketObjects = $eventTicketObjects;
  }
  /**
   * @return EventTicketObject[]
   */
  public function getEventTicketObjects()
  {
    return $this->eventTicketObjects;
  }
  /**
   * A list of flight classes.
   *
   * @param FlightClass[] $flightClasses
   */
  public function setFlightClasses($flightClasses)
  {
    $this->flightClasses = $flightClasses;
  }
  /**
   * @return FlightClass[]
   */
  public function getFlightClasses()
  {
    return $this->flightClasses;
  }
  /**
   * A list of flight objects.
   *
   * @param FlightObject[] $flightObjects
   */
  public function setFlightObjects($flightObjects)
  {
    $this->flightObjects = $flightObjects;
  }
  /**
   * @return FlightObject[]
   */
  public function getFlightObjects()
  {
    return $this->flightObjects;
  }
  /**
   * A list of generic classes.
   *
   * @param GenericClass[] $genericClasses
   */
  public function setGenericClasses($genericClasses)
  {
    $this->genericClasses = $genericClasses;
  }
  /**
   * @return GenericClass[]
   */
  public function getGenericClasses()
  {
    return $this->genericClasses;
  }
  /**
   * A list of generic objects.
   *
   * @param GenericObject[] $genericObjects
   */
  public function setGenericObjects($genericObjects)
  {
    $this->genericObjects = $genericObjects;
  }
  /**
   * @return GenericObject[]
   */
  public function getGenericObjects()
  {
    return $this->genericObjects;
  }
  /**
   * A list of gift card classes.
   *
   * @param GiftCardClass[] $giftCardClasses
   */
  public function setGiftCardClasses($giftCardClasses)
  {
    $this->giftCardClasses = $giftCardClasses;
  }
  /**
   * @return GiftCardClass[]
   */
  public function getGiftCardClasses()
  {
    return $this->giftCardClasses;
  }
  /**
   * A list of gift card objects.
   *
   * @param GiftCardObject[] $giftCardObjects
   */
  public function setGiftCardObjects($giftCardObjects)
  {
    $this->giftCardObjects = $giftCardObjects;
  }
  /**
   * @return GiftCardObject[]
   */
  public function getGiftCardObjects()
  {
    return $this->giftCardObjects;
  }
  /**
   * A list of loyalty classes.
   *
   * @param LoyaltyClass[] $loyaltyClasses
   */
  public function setLoyaltyClasses($loyaltyClasses)
  {
    $this->loyaltyClasses = $loyaltyClasses;
  }
  /**
   * @return LoyaltyClass[]
   */
  public function getLoyaltyClasses()
  {
    return $this->loyaltyClasses;
  }
  /**
   * A list of loyalty objects.
   *
   * @param LoyaltyObject[] $loyaltyObjects
   */
  public function setLoyaltyObjects($loyaltyObjects)
  {
    $this->loyaltyObjects = $loyaltyObjects;
  }
  /**
   * @return LoyaltyObject[]
   */
  public function getLoyaltyObjects()
  {
    return $this->loyaltyObjects;
  }
  /**
   * A list of offer classes.
   *
   * @param OfferClass[] $offerClasses
   */
  public function setOfferClasses($offerClasses)
  {
    $this->offerClasses = $offerClasses;
  }
  /**
   * @return OfferClass[]
   */
  public function getOfferClasses()
  {
    return $this->offerClasses;
  }
  /**
   * A list of offer objects.
   *
   * @param OfferObject[] $offerObjects
   */
  public function setOfferObjects($offerObjects)
  {
    $this->offerObjects = $offerObjects;
  }
  /**
   * @return OfferObject[]
   */
  public function getOfferObjects()
  {
    return $this->offerObjects;
  }
  /**
   * A list of transit classes.
   *
   * @param TransitClass[] $transitClasses
   */
  public function setTransitClasses($transitClasses)
  {
    $this->transitClasses = $transitClasses;
  }
  /**
   * @return TransitClass[]
   */
  public function getTransitClasses()
  {
    return $this->transitClasses;
  }
  /**
   * A list of transit objects.
   *
   * @param TransitObject[] $transitObjects
   */
  public function setTransitObjects($transitObjects)
  {
    $this->transitObjects = $transitObjects;
  }
  /**
   * @return TransitObject[]
   */
  public function getTransitObjects()
  {
    return $this->transitObjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Resources::class, 'Google_Service_Walletobjects_Resources');
