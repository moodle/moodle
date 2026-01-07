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

namespace Google\Service\MyBusinessLodging;

class Lodging extends \Google\Collection
{
  protected $collection_key = 'guestUnits';
  protected $accessibilityType = Accessibility::class;
  protected $accessibilityDataType = '';
  protected $activitiesType = Activities::class;
  protected $activitiesDataType = '';
  protected $allUnitsType = GuestUnitFeatures::class;
  protected $allUnitsDataType = '';
  protected $businessType = Business::class;
  protected $businessDataType = '';
  protected $commonLivingAreaType = LivingArea::class;
  protected $commonLivingAreaDataType = '';
  protected $connectivityType = Connectivity::class;
  protected $connectivityDataType = '';
  protected $familiesType = Families::class;
  protected $familiesDataType = '';
  protected $foodAndDrinkType = FoodAndDrink::class;
  protected $foodAndDrinkDataType = '';
  protected $guestUnitsType = GuestUnitType::class;
  protected $guestUnitsDataType = 'array';
  protected $healthAndSafetyType = HealthAndSafety::class;
  protected $healthAndSafetyDataType = '';
  protected $housekeepingType = Housekeeping::class;
  protected $housekeepingDataType = '';
  protected $metadataType = LodgingMetadata::class;
  protected $metadataDataType = '';
  /**
   * Required. Google identifier for this location in the form:
   * `locations/{location_id}/lodging`
   *
   * @var string
   */
  public $name;
  protected $parkingType = Parking::class;
  protected $parkingDataType = '';
  protected $petsType = Pets::class;
  protected $petsDataType = '';
  protected $policiesType = Policies::class;
  protected $policiesDataType = '';
  protected $poolsType = Pools::class;
  protected $poolsDataType = '';
  protected $propertyType = Property::class;
  protected $propertyDataType = '';
  protected $servicesType = Services::class;
  protected $servicesDataType = '';
  protected $someUnitsType = GuestUnitFeatures::class;
  protected $someUnitsDataType = '';
  protected $sustainabilityType = Sustainability::class;
  protected $sustainabilityDataType = '';
  protected $transportationType = Transportation::class;
  protected $transportationDataType = '';
  protected $wellnessType = Wellness::class;
  protected $wellnessDataType = '';

  /**
   * Physical adaptations made to the property in consideration of varying
   * levels of human physical ability.
   *
   * @param Accessibility $accessibility
   */
  public function setAccessibility(Accessibility $accessibility)
  {
    $this->accessibility = $accessibility;
  }
  /**
   * @return Accessibility
   */
  public function getAccessibility()
  {
    return $this->accessibility;
  }
  /**
   * Amenities and features related to leisure and play.
   *
   * @param Activities $activities
   */
  public function setActivities(Activities $activities)
  {
    $this->activities = $activities;
  }
  /**
   * @return Activities
   */
  public function getActivities()
  {
    return $this->activities;
  }
  /**
   * Output only. All units on the property have at least these attributes.
   *
   * @param GuestUnitFeatures $allUnits
   */
  public function setAllUnits(GuestUnitFeatures $allUnits)
  {
    $this->allUnits = $allUnits;
  }
  /**
   * @return GuestUnitFeatures
   */
  public function getAllUnits()
  {
    return $this->allUnits;
  }
  /**
   * Features of the property of specific interest to the business traveler.
   *
   * @param Business $business
   */
  public function setBusiness(Business $business)
  {
    $this->business = $business;
  }
  /**
   * @return Business
   */
  public function getBusiness()
  {
    return $this->business;
  }
  /**
   * Features of the shared living areas available in this Lodging.
   *
   * @param LivingArea $commonLivingArea
   */
  public function setCommonLivingArea(LivingArea $commonLivingArea)
  {
    $this->commonLivingArea = $commonLivingArea;
  }
  /**
   * @return LivingArea
   */
  public function getCommonLivingArea()
  {
    return $this->commonLivingArea;
  }
  /**
   * The ways in which the property provides guests with the ability to access
   * the internet.
   *
   * @param Connectivity $connectivity
   */
  public function setConnectivity(Connectivity $connectivity)
  {
    $this->connectivity = $connectivity;
  }
  /**
   * @return Connectivity
   */
  public function getConnectivity()
  {
    return $this->connectivity;
  }
  /**
   * Services and amenities for families and young guests.
   *
   * @param Families $families
   */
  public function setFamilies(Families $families)
  {
    $this->families = $families;
  }
  /**
   * @return Families
   */
  public function getFamilies()
  {
    return $this->families;
  }
  /**
   * Meals, snacks, and beverages available at the property.
   *
   * @param FoodAndDrink $foodAndDrink
   */
  public function setFoodAndDrink(FoodAndDrink $foodAndDrink)
  {
    $this->foodAndDrink = $foodAndDrink;
  }
  /**
   * @return FoodAndDrink
   */
  public function getFoodAndDrink()
  {
    return $this->foodAndDrink;
  }
  /**
   * Individual GuestUnitTypes that are available in this Lodging.
   *
   * @param GuestUnitType[] $guestUnits
   */
  public function setGuestUnits($guestUnits)
  {
    $this->guestUnits = $guestUnits;
  }
  /**
   * @return GuestUnitType[]
   */
  public function getGuestUnits()
  {
    return $this->guestUnits;
  }
  /**
   * Health and safety measures implemented by the hotel during COVID-19.
   *
   * @param HealthAndSafety $healthAndSafety
   */
  public function setHealthAndSafety(HealthAndSafety $healthAndSafety)
  {
    $this->healthAndSafety = $healthAndSafety;
  }
  /**
   * @return HealthAndSafety
   */
  public function getHealthAndSafety()
  {
    return $this->healthAndSafety;
  }
  /**
   * Conveniences provided in guest units to facilitate an easier, more
   * comfortable stay.
   *
   * @param Housekeeping $housekeeping
   */
  public function setHousekeeping(Housekeeping $housekeeping)
  {
    $this->housekeeping = $housekeeping;
  }
  /**
   * @return Housekeeping
   */
  public function getHousekeeping()
  {
    return $this->housekeeping;
  }
  /**
   * Required. Metadata for the lodging.
   *
   * @param LodgingMetadata $metadata
   */
  public function setMetadata(LodgingMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return LodgingMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Required. Google identifier for this location in the form:
   * `locations/{location_id}/lodging`
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
  /**
   * Parking options at the property.
   *
   * @param Parking $parking
   */
  public function setParking(Parking $parking)
  {
    $this->parking = $parking;
  }
  /**
   * @return Parking
   */
  public function getParking()
  {
    return $this->parking;
  }
  /**
   * Policies regarding guest-owned animals.
   *
   * @param Pets $pets
   */
  public function setPets(Pets $pets)
  {
    $this->pets = $pets;
  }
  /**
   * @return Pets
   */
  public function getPets()
  {
    return $this->pets;
  }
  /**
   * Property rules that impact guests.
   *
   * @param Policies $policies
   */
  public function setPolicies(Policies $policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return Policies
   */
  public function getPolicies()
  {
    return $this->policies;
  }
  /**
   * Swimming pool or recreational water facilities available at the hotel.
   *
   * @param Pools $pools
   */
  public function setPools(Pools $pools)
  {
    $this->pools = $pools;
  }
  /**
   * @return Pools
   */
  public function getPools()
  {
    return $this->pools;
  }
  /**
   * General factual information about the property's physical structure and
   * important dates.
   *
   * @param Property $property
   */
  public function setProperty(Property $property)
  {
    $this->property = $property;
  }
  /**
   * @return Property
   */
  public function getProperty()
  {
    return $this->property;
  }
  /**
   * Conveniences or help provided by the property to facilitate an easier, more
   * comfortable stay.
   *
   * @param Services $services
   */
  public function setServices(Services $services)
  {
    $this->services = $services;
  }
  /**
   * @return Services
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * Output only. Some units on the property have as much as these attributes.
   *
   * @param GuestUnitFeatures $someUnits
   */
  public function setSomeUnits(GuestUnitFeatures $someUnits)
  {
    $this->someUnits = $someUnits;
  }
  /**
   * @return GuestUnitFeatures
   */
  public function getSomeUnits()
  {
    return $this->someUnits;
  }
  /**
   * Sustainability practices implemented at the hotel.
   *
   * @param Sustainability $sustainability
   */
  public function setSustainability(Sustainability $sustainability)
  {
    $this->sustainability = $sustainability;
  }
  /**
   * @return Sustainability
   */
  public function getSustainability()
  {
    return $this->sustainability;
  }
  /**
   * Vehicles or vehicular services facilitated or owned by the property.
   *
   * @param Transportation $transportation
   */
  public function setTransportation(Transportation $transportation)
  {
    $this->transportation = $transportation;
  }
  /**
   * @return Transportation
   */
  public function getTransportation()
  {
    return $this->transportation;
  }
  /**
   * Guest facilities at the property to promote or maintain health, beauty, and
   * fitness.
   *
   * @param Wellness $wellness
   */
  public function setWellness(Wellness $wellness)
  {
    $this->wellness = $wellness;
  }
  /**
   * @return Wellness
   */
  public function getWellness()
  {
    return $this->wellness;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Lodging::class, 'Google_Service_MyBusinessLodging_Lodging');
