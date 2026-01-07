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

namespace Google\Service\Solar;

class DataLayers extends \Google\Collection
{
  /**
   * No quality is known.
   */
  public const IMAGERY_QUALITY_IMAGERY_QUALITY_UNSPECIFIED = 'IMAGERY_QUALITY_UNSPECIFIED';
  /**
   * Solar data is derived from aerial imagery captured at low-altitude and
   * processed at 0.1 m/pixel.
   */
  public const IMAGERY_QUALITY_HIGH = 'HIGH';
  /**
   * Solar data is derived from enhanced aerial imagery captured at high-
   * altitude and processed at 0.25 m/pixel.
   */
  public const IMAGERY_QUALITY_MEDIUM = 'MEDIUM';
  /**
   * Solar data is derived from enhanced satellite imagery processed at 0.25
   * m/pixel.
   */
  public const IMAGERY_QUALITY_LOW = 'LOW';
  /**
   * Solar data is derived from enhanced satellite imagery processed at 0.25
   * m/pixel.
   */
  public const IMAGERY_QUALITY_BASE = 'BASE';
  protected $collection_key = 'hourlyShadeUrls';
  /**
   * The URL for the annual flux map (annual sunlight on roofs) of the region.
   * Values are kWh/kW/year. This is *unmasked flux*: flux is computed for every
   * location, not just building rooftops. Invalid locations are stored as
   * -9999: locations outside our coverage area will be invalid, and a few
   * locations inside the coverage area, where we were unable to calculate flux,
   * will also be invalid.
   *
   * @var string
   */
  public $annualFluxUrl;
  /**
   * The URL for an image of the DSM (Digital Surface Model) of the region.
   * Values are in meters above EGM96 geoid (i.e., sea level). Invalid locations
   * (where we don't have data) are stored as -9999.
   *
   * @var string
   */
  public $dsmUrl;
  /**
   * Twelve URLs for hourly shade, corresponding to January...December, in
   * order. Each GeoTIFF will contain 24 bands, corresponding to the 24 hours of
   * the day. Each pixel is a 32 bit integer, corresponding to the (up to) 31
   * days of that month; a 1 bit means that the corresponding location is able
   * to see the sun at that day, of that hour, of that month. Invalid locations
   * are stored as -9999 (since this is negative, it has bit 31 set, and no
   * valid value could have bit 31 set as that would correspond to the 32nd day
   * of the month). An example may be useful. If you want to know whether a
   * point (at pixel location (x, y)) saw sun at 4pm on the 22nd of June you
   * would: 1. fetch the sixth URL in this list (corresponding to June). 1. look
   * up the 17th channel (corresponding to 4pm). 1. read the 32-bit value at (x,
   * y). 1. read bit 21 of the value (corresponding to the 22nd of the month).
   * 1. if that bit is a 1, then that spot saw the sun at 4pm 22 June. More
   * formally: Given `month` (1-12), `day` (1...month max; February has 28 days)
   * and `hour` (0-23), the shade/sun for that month/day/hour at a position `(x,
   * y)` is the bit ``` (hourly_shade[month - 1])(x, y)[hour] & (1 << (day - 1))
   * ``` where `(x, y)` is spatial indexing, `[month - 1]` refers to fetching
   * the `month - 1`st URL (indexing from zero), `[hour]` is indexing into the
   * channels, and a final non-zero result means "sunny". There are no leap
   * days, and DST doesn't exist (all days are 24 hours long; noon is always
   * "standard time" noon).
   *
   * @var string[]
   */
  public $hourlyShadeUrls;
  protected $imageryDateType = Date::class;
  protected $imageryDateDataType = '';
  protected $imageryProcessedDateType = Date::class;
  protected $imageryProcessedDateDataType = '';
  /**
   * The quality of the result's imagery.
   *
   * @var string
   */
  public $imageryQuality;
  /**
   * The URL for the building mask image: one bit per pixel saying whether that
   * pixel is considered to be part of a rooftop or not.
   *
   * @var string
   */
  public $maskUrl;
  /**
   * The URL for the monthly flux map (sunlight on roofs, broken down by month)
   * of the region. Values are kWh/kW/year. The GeoTIFF pointed to by this URL
   * will contain twelve bands, corresponding to January...December, in order.
   *
   * @var string
   */
  public $monthlyFluxUrl;
  /**
   * The URL for an image of RGB data (aerial photo) of the region.
   *
   * @var string
   */
  public $rgbUrl;

  /**
   * The URL for the annual flux map (annual sunlight on roofs) of the region.
   * Values are kWh/kW/year. This is *unmasked flux*: flux is computed for every
   * location, not just building rooftops. Invalid locations are stored as
   * -9999: locations outside our coverage area will be invalid, and a few
   * locations inside the coverage area, where we were unable to calculate flux,
   * will also be invalid.
   *
   * @param string $annualFluxUrl
   */
  public function setAnnualFluxUrl($annualFluxUrl)
  {
    $this->annualFluxUrl = $annualFluxUrl;
  }
  /**
   * @return string
   */
  public function getAnnualFluxUrl()
  {
    return $this->annualFluxUrl;
  }
  /**
   * The URL for an image of the DSM (Digital Surface Model) of the region.
   * Values are in meters above EGM96 geoid (i.e., sea level). Invalid locations
   * (where we don't have data) are stored as -9999.
   *
   * @param string $dsmUrl
   */
  public function setDsmUrl($dsmUrl)
  {
    $this->dsmUrl = $dsmUrl;
  }
  /**
   * @return string
   */
  public function getDsmUrl()
  {
    return $this->dsmUrl;
  }
  /**
   * Twelve URLs for hourly shade, corresponding to January...December, in
   * order. Each GeoTIFF will contain 24 bands, corresponding to the 24 hours of
   * the day. Each pixel is a 32 bit integer, corresponding to the (up to) 31
   * days of that month; a 1 bit means that the corresponding location is able
   * to see the sun at that day, of that hour, of that month. Invalid locations
   * are stored as -9999 (since this is negative, it has bit 31 set, and no
   * valid value could have bit 31 set as that would correspond to the 32nd day
   * of the month). An example may be useful. If you want to know whether a
   * point (at pixel location (x, y)) saw sun at 4pm on the 22nd of June you
   * would: 1. fetch the sixth URL in this list (corresponding to June). 1. look
   * up the 17th channel (corresponding to 4pm). 1. read the 32-bit value at (x,
   * y). 1. read bit 21 of the value (corresponding to the 22nd of the month).
   * 1. if that bit is a 1, then that spot saw the sun at 4pm 22 June. More
   * formally: Given `month` (1-12), `day` (1...month max; February has 28 days)
   * and `hour` (0-23), the shade/sun for that month/day/hour at a position `(x,
   * y)` is the bit ``` (hourly_shade[month - 1])(x, y)[hour] & (1 << (day - 1))
   * ``` where `(x, y)` is spatial indexing, `[month - 1]` refers to fetching
   * the `month - 1`st URL (indexing from zero), `[hour]` is indexing into the
   * channels, and a final non-zero result means "sunny". There are no leap
   * days, and DST doesn't exist (all days are 24 hours long; noon is always
   * "standard time" noon).
   *
   * @param string[] $hourlyShadeUrls
   */
  public function setHourlyShadeUrls($hourlyShadeUrls)
  {
    $this->hourlyShadeUrls = $hourlyShadeUrls;
  }
  /**
   * @return string[]
   */
  public function getHourlyShadeUrls()
  {
    return $this->hourlyShadeUrls;
  }
  /**
   * When the source imagery (from which all the other data are derived) in this
   * region was taken. It is necessarily somewhat approximate, as the images may
   * have been taken over more than one day.
   *
   * @param Date $imageryDate
   */
  public function setImageryDate(Date $imageryDate)
  {
    $this->imageryDate = $imageryDate;
  }
  /**
   * @return Date
   */
  public function getImageryDate()
  {
    return $this->imageryDate;
  }
  /**
   * When processing was completed on this imagery.
   *
   * @param Date $imageryProcessedDate
   */
  public function setImageryProcessedDate(Date $imageryProcessedDate)
  {
    $this->imageryProcessedDate = $imageryProcessedDate;
  }
  /**
   * @return Date
   */
  public function getImageryProcessedDate()
  {
    return $this->imageryProcessedDate;
  }
  /**
   * The quality of the result's imagery.
   *
   * Accepted values: IMAGERY_QUALITY_UNSPECIFIED, HIGH, MEDIUM, LOW, BASE
   *
   * @param self::IMAGERY_QUALITY_* $imageryQuality
   */
  public function setImageryQuality($imageryQuality)
  {
    $this->imageryQuality = $imageryQuality;
  }
  /**
   * @return self::IMAGERY_QUALITY_*
   */
  public function getImageryQuality()
  {
    return $this->imageryQuality;
  }
  /**
   * The URL for the building mask image: one bit per pixel saying whether that
   * pixel is considered to be part of a rooftop or not.
   *
   * @param string $maskUrl
   */
  public function setMaskUrl($maskUrl)
  {
    $this->maskUrl = $maskUrl;
  }
  /**
   * @return string
   */
  public function getMaskUrl()
  {
    return $this->maskUrl;
  }
  /**
   * The URL for the monthly flux map (sunlight on roofs, broken down by month)
   * of the region. Values are kWh/kW/year. The GeoTIFF pointed to by this URL
   * will contain twelve bands, corresponding to January...December, in order.
   *
   * @param string $monthlyFluxUrl
   */
  public function setMonthlyFluxUrl($monthlyFluxUrl)
  {
    $this->monthlyFluxUrl = $monthlyFluxUrl;
  }
  /**
   * @return string
   */
  public function getMonthlyFluxUrl()
  {
    return $this->monthlyFluxUrl;
  }
  /**
   * The URL for an image of RGB data (aerial photo) of the region.
   *
   * @param string $rgbUrl
   */
  public function setRgbUrl($rgbUrl)
  {
    $this->rgbUrl = $rgbUrl;
  }
  /**
   * @return string
   */
  public function getRgbUrl()
  {
    return $this->rgbUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataLayers::class, 'Google_Service_Solar_DataLayers');
