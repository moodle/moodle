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

namespace Google\Service\MapsPlaces\Resource;

use Google\Service\MapsPlaces\GoogleMapsPlacesV1PhotoMedia;

/**
 * The "photos" collection of methods.
 * Typical usage is:
 *  <code>
 *   $placesService = new Google\Service\MapsPlaces(...);
 *   $photos = $placesService->places_photos;
 *  </code>
 */
class PlacesPhotos extends \Google\Service\Resource
{
  /**
   * Get a photo media with a photo reference string. (photos.getMedia)
   *
   * @param string $name Required. The resource name of a photo media in the
   * format: `places/{place_id}/photos/{photo_reference}/media`. The resource name
   * of a photo as returned in a Place object's `photos.name` field comes with the
   * format `places/{place_id}/photos/{photo_reference}`. You need to append
   * `/media` at the end of the photo resource to get the photo media resource
   * name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int maxHeightPx Optional. Specifies the maximum desired height, in
   * pixels, of the image. If the image is smaller than the values specified, the
   * original image will be returned. If the image is larger in either dimension,
   * it will be scaled to match the smaller of the two dimensions, restricted to
   * its original aspect ratio. Both the max_height_px and max_width_px properties
   * accept an integer between 1 and 4800, inclusively. If the value is not within
   * the allowed range, an INVALID_ARGUMENT error will be returned. At least one
   * of max_height_px or max_width_px needs to be specified. If neither
   * max_height_px nor max_width_px is specified, an INVALID_ARGUMENT error will
   * be returned.
   * @opt_param int maxWidthPx Optional. Specifies the maximum desired width, in
   * pixels, of the image. If the image is smaller than the values specified, the
   * original image will be returned. If the image is larger in either dimension,
   * it will be scaled to match the smaller of the two dimensions, restricted to
   * its original aspect ratio. Both the max_height_px and max_width_px properties
   * accept an integer between 1 and 4800, inclusively. If the value is not within
   * the allowed range, an INVALID_ARGUMENT error will be returned. At least one
   * of max_height_px or max_width_px needs to be specified. If neither
   * max_height_px nor max_width_px is specified, an INVALID_ARGUMENT error will
   * be returned.
   * @opt_param bool skipHttpRedirect Optional. If set, skip the default HTTP
   * redirect behavior and render a text format (for example, in JSON format for
   * HTTP use case) response. If not set, an HTTP redirect will be issued to
   * redirect the call to the image media. This option is ignored for non-HTTP
   * requests.
   * @return GoogleMapsPlacesV1PhotoMedia
   * @throws \Google\Service\Exception
   */
  public function getMedia($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getMedia', [$params], GoogleMapsPlacesV1PhotoMedia::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlacesPhotos::class, 'Google_Service_MapsPlaces_Resource_PlacesPhotos');
