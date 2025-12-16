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

namespace Google\Service\TravelImpactModel;

class FlightWithEmissions extends \Google\Model
{
  /**
   * The contrails impact is unspecified.
   */
  public const CONTRAILS_IMPACT_BUCKET_CONTRAILS_IMPACT_UNSPECIFIED = 'CONTRAILS_IMPACT_UNSPECIFIED';
  /**
   * The contrails impact is negligible compared to the total CO2e emissions.
   */
  public const CONTRAILS_IMPACT_BUCKET_CONTRAILS_IMPACT_NEGLIGIBLE = 'CONTRAILS_IMPACT_NEGLIGIBLE';
  /**
   * The contrails impact is comparable to the total CO2e emissions.
   */
  public const CONTRAILS_IMPACT_BUCKET_CONTRAILS_IMPACT_MODERATE = 'CONTRAILS_IMPACT_MODERATE';
  /**
   * The contrails impact is higher than the total CO2e emissions impact.
   */
  public const CONTRAILS_IMPACT_BUCKET_CONTRAILS_IMPACT_SEVERE = 'CONTRAILS_IMPACT_SEVERE';
  /**
   * The source of the emissions data is unspecified.
   */
  public const SOURCE_SOURCE_UNSPECIFIED = 'SOURCE_UNSPECIFIED';
  /**
   * The emissions data is from the Travel Impact Model.
   */
  public const SOURCE_TIM = 'TIM';
  /**
   * The emissions data is from the EASA environmental labels.
   */
  public const SOURCE_EASA = 'EASA';
  /**
   * Optional. The significance of contrails warming impact compared to the
   * total CO2e emissions impact.
   *
   * @var string
   */
  public $contrailsImpactBucket;
  protected $easaLabelMetadataType = EasaLabelMetadata::class;
  protected $easaLabelMetadataDataType = '';
  protected $emissionsGramsPerPaxType = EmissionsGramsPerPax::class;
  protected $emissionsGramsPerPaxDataType = '';
  protected $flightType = Flight::class;
  protected $flightDataType = '';
  /**
   * Optional. The source of the emissions data.
   *
   * @var string
   */
  public $source;

  /**
   * Optional. The significance of contrails warming impact compared to the
   * total CO2e emissions impact.
   *
   * Accepted values: CONTRAILS_IMPACT_UNSPECIFIED, CONTRAILS_IMPACT_NEGLIGIBLE,
   * CONTRAILS_IMPACT_MODERATE, CONTRAILS_IMPACT_SEVERE
   *
   * @param self::CONTRAILS_IMPACT_BUCKET_* $contrailsImpactBucket
   */
  public function setContrailsImpactBucket($contrailsImpactBucket)
  {
    $this->contrailsImpactBucket = $contrailsImpactBucket;
  }
  /**
   * @return self::CONTRAILS_IMPACT_BUCKET_*
   */
  public function getContrailsImpactBucket()
  {
    return $this->contrailsImpactBucket;
  }
  /**
   * Optional. Metadata about the EASA Flight Emissions Label. Only set when the
   * emissions data source is EASA.
   *
   * @param EasaLabelMetadata $easaLabelMetadata
   */
  public function setEasaLabelMetadata(EasaLabelMetadata $easaLabelMetadata)
  {
    $this->easaLabelMetadata = $easaLabelMetadata;
  }
  /**
   * @return EasaLabelMetadata
   */
  public function getEasaLabelMetadata()
  {
    return $this->easaLabelMetadata;
  }
  /**
   * Optional. Per-passenger emission estimate numbers. Will not be present if
   * emissions could not be computed. For the list of reasons why emissions
   * could not be computed, see ComputeFlightEmissions.
   *
   * @param EmissionsGramsPerPax $emissionsGramsPerPax
   */
  public function setEmissionsGramsPerPax(EmissionsGramsPerPax $emissionsGramsPerPax)
  {
    $this->emissionsGramsPerPax = $emissionsGramsPerPax;
  }
  /**
   * @return EmissionsGramsPerPax
   */
  public function getEmissionsGramsPerPax()
  {
    return $this->emissionsGramsPerPax;
  }
  /**
   * Required. Matches the flight identifiers in the request. Note: all IATA
   * codes are capitalized.
   *
   * @param Flight $flight
   */
  public function setFlight(Flight $flight)
  {
    $this->flight = $flight;
  }
  /**
   * @return Flight
   */
  public function getFlight()
  {
    return $this->flight;
  }
  /**
   * Optional. The source of the emissions data.
   *
   * Accepted values: SOURCE_UNSPECIFIED, TIM, EASA
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FlightWithEmissions::class, 'Google_Service_TravelImpactModel_FlightWithEmissions');
