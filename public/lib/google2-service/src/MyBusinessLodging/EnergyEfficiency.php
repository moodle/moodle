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

class EnergyEfficiency extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CARBON_FREE_ENERGY_SOURCES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CARBON_FREE_ENERGY_SOURCES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CARBON_FREE_ENERGY_SOURCES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CARBON_FREE_ENERGY_SOURCES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ENERGY_CONSERVATION_PROGRAM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ENERGY_CONSERVATION_PROGRAM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ENERGY_CONSERVATION_PROGRAM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ENERGY_CONSERVATION_PROGRAM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ENERGY_EFFICIENT_HEATING_AND_COOLING_SYSTEMS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ENERGY_EFFICIENT_HEATING_AND_COOLING_SYSTEMS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ENERGY_EFFICIENT_HEATING_AND_COOLING_SYSTEMS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ENERGY_EFFICIENT_HEATING_AND_COOLING_SYSTEMS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ENERGY_EFFICIENT_LIGHTING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ENERGY_EFFICIENT_LIGHTING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ENERGY_EFFICIENT_LIGHTING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ENERGY_EFFICIENT_LIGHTING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ENERGY_SAVING_THERMOSTATS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ENERGY_SAVING_THERMOSTATS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ENERGY_SAVING_THERMOSTATS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ENERGY_SAVING_THERMOSTATS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const GREEN_BUILDING_DESIGN_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const GREEN_BUILDING_DESIGN_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const GREEN_BUILDING_DESIGN_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const GREEN_BUILDING_DESIGN_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_ENERGY_USE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_ENERGY_USE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_ENERGY_USE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_ENERGY_USE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Carbon free energy sources. Property sources carbon-free electricity via at
   * least one of the following methods: on-site clean energy generation, power
   * purchase agreement(s) with clean energy generators, green power provided by
   * electricity supplier, or purchases of Energy Attribute Certificates (such
   * as Renewable Energy Certificates or Guarantees of Origin).
   *
   * @var bool
   */
  public $carbonFreeEnergySources;
  /**
   * Carbon free energy sources exception.
   *
   * @var string
   */
  public $carbonFreeEnergySourcesException;
  /**
   * Energy conservation program. The property tracks corporate-level Scope 1
   * and 2 GHG emissions, and Scope 3 emissions if available. The property has a
   * commitment to implement initiatives that reduce GHG emissions year over
   * year. The property has shown an absolute reduction in emissions for at
   * least 2 years. Emissions are either verfied by a third-party and/or
   * published in external communications.
   *
   * @var bool
   */
  public $energyConservationProgram;
  /**
   * Energy conservation program exception.
   *
   * @var string
   */
  public $energyConservationProgramException;
  /**
   * Energy efficient heating and cooling systems. The property doesn't use
   * chlorofluorocarbon (CFC)-based refrigerants in heating, ventilating, and
   * air-conditioning systems unless a third-party audit shows it's not
   * economically feasible. The CFC-based refrigerants which are used should
   * have a Global Warming Potential (GWP) ≤ 10. The property uses occupancy
   * sensors on HVAC systems in back-of-house spaces, meeting rooms, and other
   * low-traffic areas.
   *
   * @var bool
   */
  public $energyEfficientHeatingAndCoolingSystems;
  /**
   * Energy efficient heating and cooling systems exception.
   *
   * @var string
   */
  public $energyEfficientHeatingAndCoolingSystemsException;
  /**
   * Energy efficient lighting. At least 75% of the property's lighting is
   * energy efficient, using lighting that is more than 45 lumens per watt –
   * typically LED or CFL lightbulbs.
   *
   * @var bool
   */
  public $energyEfficientLighting;
  /**
   * Energy efficient lighting exception.
   *
   * @var string
   */
  public $energyEfficientLightingException;
  /**
   * Energy saving thermostats. The property installed energy-saving thermostats
   * throughout the building to conserve energy when rooms or areas are not in
   * use. Energy-saving thermostats are devices that control heating/cooling in
   * the building by learning temperature preferences and automatically
   * adjusting to energy-saving temperatures as the default. The thermostats are
   * automatically set to a temperature between 68-78 degrees F (20-26 °C),
   * depending on seasonality. In the winter, set the thermostat to 68°F (20°C)
   * when the room is occupied, lowering room temperature when unoccupied. In
   * the summer, set the thermostat to 78°F (26°C) when the room is occupied.
   *
   * @var bool
   */
  public $energySavingThermostats;
  /**
   * Energy saving thermostats exception.
   *
   * @var string
   */
  public $energySavingThermostatsException;
  /**
   * Output only. Green building design. True if the property has been awarded a
   * relevant certification.
   *
   * @var bool
   */
  public $greenBuildingDesign;
  /**
   * Output only. Green building design exception.
   *
   * @var string
   */
  public $greenBuildingDesignException;
  /**
   * Independent organization audits energy use. The property conducts an energy
   * audit at least every 5 years, the results of which are either verified by a
   * third-party and/or published in external communications. An energy audit is
   * a detailed assessment of the facility which provides recommendations to
   * existing operations and procedures to improve energy efficiency, available
   * incentives or rebates,and opportunities for improvements through
   * renovations or upgrades. Examples of organizations that conduct credible
   * third party audits include: Engie Impact, DNV GL (EU), Dexma, and local
   * utility providers (they often provide energy and water audits).
   *
   * @var bool
   */
  public $independentOrganizationAuditsEnergyUse;
  /**
   * Independent organization audits energy use exception.
   *
   * @var string
   */
  public $independentOrganizationAuditsEnergyUseException;

  /**
   * Carbon free energy sources. Property sources carbon-free electricity via at
   * least one of the following methods: on-site clean energy generation, power
   * purchase agreement(s) with clean energy generators, green power provided by
   * electricity supplier, or purchases of Energy Attribute Certificates (such
   * as Renewable Energy Certificates or Guarantees of Origin).
   *
   * @param bool $carbonFreeEnergySources
   */
  public function setCarbonFreeEnergySources($carbonFreeEnergySources)
  {
    $this->carbonFreeEnergySources = $carbonFreeEnergySources;
  }
  /**
   * @return bool
   */
  public function getCarbonFreeEnergySources()
  {
    return $this->carbonFreeEnergySources;
  }
  /**
   * Carbon free energy sources exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CARBON_FREE_ENERGY_SOURCES_EXCEPTION_* $carbonFreeEnergySourcesException
   */
  public function setCarbonFreeEnergySourcesException($carbonFreeEnergySourcesException)
  {
    $this->carbonFreeEnergySourcesException = $carbonFreeEnergySourcesException;
  }
  /**
   * @return self::CARBON_FREE_ENERGY_SOURCES_EXCEPTION_*
   */
  public function getCarbonFreeEnergySourcesException()
  {
    return $this->carbonFreeEnergySourcesException;
  }
  /**
   * Energy conservation program. The property tracks corporate-level Scope 1
   * and 2 GHG emissions, and Scope 3 emissions if available. The property has a
   * commitment to implement initiatives that reduce GHG emissions year over
   * year. The property has shown an absolute reduction in emissions for at
   * least 2 years. Emissions are either verfied by a third-party and/or
   * published in external communications.
   *
   * @param bool $energyConservationProgram
   */
  public function setEnergyConservationProgram($energyConservationProgram)
  {
    $this->energyConservationProgram = $energyConservationProgram;
  }
  /**
   * @return bool
   */
  public function getEnergyConservationProgram()
  {
    return $this->energyConservationProgram;
  }
  /**
   * Energy conservation program exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ENERGY_CONSERVATION_PROGRAM_EXCEPTION_* $energyConservationProgramException
   */
  public function setEnergyConservationProgramException($energyConservationProgramException)
  {
    $this->energyConservationProgramException = $energyConservationProgramException;
  }
  /**
   * @return self::ENERGY_CONSERVATION_PROGRAM_EXCEPTION_*
   */
  public function getEnergyConservationProgramException()
  {
    return $this->energyConservationProgramException;
  }
  /**
   * Energy efficient heating and cooling systems. The property doesn't use
   * chlorofluorocarbon (CFC)-based refrigerants in heating, ventilating, and
   * air-conditioning systems unless a third-party audit shows it's not
   * economically feasible. The CFC-based refrigerants which are used should
   * have a Global Warming Potential (GWP) ≤ 10. The property uses occupancy
   * sensors on HVAC systems in back-of-house spaces, meeting rooms, and other
   * low-traffic areas.
   *
   * @param bool $energyEfficientHeatingAndCoolingSystems
   */
  public function setEnergyEfficientHeatingAndCoolingSystems($energyEfficientHeatingAndCoolingSystems)
  {
    $this->energyEfficientHeatingAndCoolingSystems = $energyEfficientHeatingAndCoolingSystems;
  }
  /**
   * @return bool
   */
  public function getEnergyEfficientHeatingAndCoolingSystems()
  {
    return $this->energyEfficientHeatingAndCoolingSystems;
  }
  /**
   * Energy efficient heating and cooling systems exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ENERGY_EFFICIENT_HEATING_AND_COOLING_SYSTEMS_EXCEPTION_* $energyEfficientHeatingAndCoolingSystemsException
   */
  public function setEnergyEfficientHeatingAndCoolingSystemsException($energyEfficientHeatingAndCoolingSystemsException)
  {
    $this->energyEfficientHeatingAndCoolingSystemsException = $energyEfficientHeatingAndCoolingSystemsException;
  }
  /**
   * @return self::ENERGY_EFFICIENT_HEATING_AND_COOLING_SYSTEMS_EXCEPTION_*
   */
  public function getEnergyEfficientHeatingAndCoolingSystemsException()
  {
    return $this->energyEfficientHeatingAndCoolingSystemsException;
  }
  /**
   * Energy efficient lighting. At least 75% of the property's lighting is
   * energy efficient, using lighting that is more than 45 lumens per watt –
   * typically LED or CFL lightbulbs.
   *
   * @param bool $energyEfficientLighting
   */
  public function setEnergyEfficientLighting($energyEfficientLighting)
  {
    $this->energyEfficientLighting = $energyEfficientLighting;
  }
  /**
   * @return bool
   */
  public function getEnergyEfficientLighting()
  {
    return $this->energyEfficientLighting;
  }
  /**
   * Energy efficient lighting exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ENERGY_EFFICIENT_LIGHTING_EXCEPTION_* $energyEfficientLightingException
   */
  public function setEnergyEfficientLightingException($energyEfficientLightingException)
  {
    $this->energyEfficientLightingException = $energyEfficientLightingException;
  }
  /**
   * @return self::ENERGY_EFFICIENT_LIGHTING_EXCEPTION_*
   */
  public function getEnergyEfficientLightingException()
  {
    return $this->energyEfficientLightingException;
  }
  /**
   * Energy saving thermostats. The property installed energy-saving thermostats
   * throughout the building to conserve energy when rooms or areas are not in
   * use. Energy-saving thermostats are devices that control heating/cooling in
   * the building by learning temperature preferences and automatically
   * adjusting to energy-saving temperatures as the default. The thermostats are
   * automatically set to a temperature between 68-78 degrees F (20-26 °C),
   * depending on seasonality. In the winter, set the thermostat to 68°F (20°C)
   * when the room is occupied, lowering room temperature when unoccupied. In
   * the summer, set the thermostat to 78°F (26°C) when the room is occupied.
   *
   * @param bool $energySavingThermostats
   */
  public function setEnergySavingThermostats($energySavingThermostats)
  {
    $this->energySavingThermostats = $energySavingThermostats;
  }
  /**
   * @return bool
   */
  public function getEnergySavingThermostats()
  {
    return $this->energySavingThermostats;
  }
  /**
   * Energy saving thermostats exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ENERGY_SAVING_THERMOSTATS_EXCEPTION_* $energySavingThermostatsException
   */
  public function setEnergySavingThermostatsException($energySavingThermostatsException)
  {
    $this->energySavingThermostatsException = $energySavingThermostatsException;
  }
  /**
   * @return self::ENERGY_SAVING_THERMOSTATS_EXCEPTION_*
   */
  public function getEnergySavingThermostatsException()
  {
    return $this->energySavingThermostatsException;
  }
  /**
   * Output only. Green building design. True if the property has been awarded a
   * relevant certification.
   *
   * @param bool $greenBuildingDesign
   */
  public function setGreenBuildingDesign($greenBuildingDesign)
  {
    $this->greenBuildingDesign = $greenBuildingDesign;
  }
  /**
   * @return bool
   */
  public function getGreenBuildingDesign()
  {
    return $this->greenBuildingDesign;
  }
  /**
   * Output only. Green building design exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::GREEN_BUILDING_DESIGN_EXCEPTION_* $greenBuildingDesignException
   */
  public function setGreenBuildingDesignException($greenBuildingDesignException)
  {
    $this->greenBuildingDesignException = $greenBuildingDesignException;
  }
  /**
   * @return self::GREEN_BUILDING_DESIGN_EXCEPTION_*
   */
  public function getGreenBuildingDesignException()
  {
    return $this->greenBuildingDesignException;
  }
  /**
   * Independent organization audits energy use. The property conducts an energy
   * audit at least every 5 years, the results of which are either verified by a
   * third-party and/or published in external communications. An energy audit is
   * a detailed assessment of the facility which provides recommendations to
   * existing operations and procedures to improve energy efficiency, available
   * incentives or rebates,and opportunities for improvements through
   * renovations or upgrades. Examples of organizations that conduct credible
   * third party audits include: Engie Impact, DNV GL (EU), Dexma, and local
   * utility providers (they often provide energy and water audits).
   *
   * @param bool $independentOrganizationAuditsEnergyUse
   */
  public function setIndependentOrganizationAuditsEnergyUse($independentOrganizationAuditsEnergyUse)
  {
    $this->independentOrganizationAuditsEnergyUse = $independentOrganizationAuditsEnergyUse;
  }
  /**
   * @return bool
   */
  public function getIndependentOrganizationAuditsEnergyUse()
  {
    return $this->independentOrganizationAuditsEnergyUse;
  }
  /**
   * Independent organization audits energy use exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INDEPENDENT_ORGANIZATION_AUDITS_ENERGY_USE_EXCEPTION_* $independentOrganizationAuditsEnergyUseException
   */
  public function setIndependentOrganizationAuditsEnergyUseException($independentOrganizationAuditsEnergyUseException)
  {
    $this->independentOrganizationAuditsEnergyUseException = $independentOrganizationAuditsEnergyUseException;
  }
  /**
   * @return self::INDEPENDENT_ORGANIZATION_AUDITS_ENERGY_USE_EXCEPTION_*
   */
  public function getIndependentOrganizationAuditsEnergyUseException()
  {
    return $this->independentOrganizationAuditsEnergyUseException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnergyEfficiency::class, 'Google_Service_MyBusinessLodging_EnergyEfficiency');
