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

namespace Google\Service\Vision;

class ImportProductSetsGcsSource extends \Google\Model
{
  /**
   * The Google Cloud Storage URI of the input csv file. The URI must start with
   * `gs://`. The format of the input csv file should be one image per line. In
   * each line, there are 8 columns. 1. image-uri 2. image-id 3. product-set-id
   * 4. product-id 5. product-category 6. product-display-name 7. labels 8.
   * bounding-poly The `image-uri`, `product-set-id`, `product-id`, and
   * `product-category` columns are required. All other columns are optional. If
   * the `ProductSet` or `Product` specified by the `product-set-id` and
   * `product-id` values does not exist, then the system will create a new
   * `ProductSet` or `Product` for the image. In this case, the `product-
   * display-name` column refers to display_name, the `product-category` column
   * refers to product_category, and the `labels` column refers to
   * product_labels. The `image-id` column is optional but must be unique if
   * provided. If it is empty, the system will automatically assign a unique id
   * to the image. The `product-display-name` column is optional. If it is
   * empty, the system sets the display_name field for the product to a space ("
   * "). You can update the `display_name` later by using the API. If a
   * `Product` with the specified `product-id` already exists, then the system
   * ignores the `product-display-name`, `product-category`, and `labels`
   * columns. The `labels` column (optional) is a line containing a list of
   * comma-separated key-value pairs, in the following format:
   * "key_1=value_1,key_2=value_2,...,key_n=value_n" The `bounding-poly` column
   * (optional) identifies one region of interest from the image in the same
   * manner as `CreateReferenceImage`. If you do not specify the `bounding-poly`
   * column, then the system will try to detect regions of interest
   * automatically. At most one `bounding-poly` column is allowed per line. If
   * the image contains multiple regions of interest, add a line to the CSV file
   * that includes the same product information, and the `bounding-poly` values
   * for each region of interest. The `bounding-poly` column must contain an
   * even number of comma-separated numbers, in the format
   * "p1_x,p1_y,p2_x,p2_y,...,pn_x,pn_y". Use non-negative integers for absolute
   * bounding polygons, and float values in [0, 1] for normalized bounding
   * polygons. The system will resize the image if the image resolution is too
   * large to process (larger than 20MP).
   *
   * @var string
   */
  public $csvFileUri;

  /**
   * The Google Cloud Storage URI of the input csv file. The URI must start with
   * `gs://`. The format of the input csv file should be one image per line. In
   * each line, there are 8 columns. 1. image-uri 2. image-id 3. product-set-id
   * 4. product-id 5. product-category 6. product-display-name 7. labels 8.
   * bounding-poly The `image-uri`, `product-set-id`, `product-id`, and
   * `product-category` columns are required. All other columns are optional. If
   * the `ProductSet` or `Product` specified by the `product-set-id` and
   * `product-id` values does not exist, then the system will create a new
   * `ProductSet` or `Product` for the image. In this case, the `product-
   * display-name` column refers to display_name, the `product-category` column
   * refers to product_category, and the `labels` column refers to
   * product_labels. The `image-id` column is optional but must be unique if
   * provided. If it is empty, the system will automatically assign a unique id
   * to the image. The `product-display-name` column is optional. If it is
   * empty, the system sets the display_name field for the product to a space ("
   * "). You can update the `display_name` later by using the API. If a
   * `Product` with the specified `product-id` already exists, then the system
   * ignores the `product-display-name`, `product-category`, and `labels`
   * columns. The `labels` column (optional) is a line containing a list of
   * comma-separated key-value pairs, in the following format:
   * "key_1=value_1,key_2=value_2,...,key_n=value_n" The `bounding-poly` column
   * (optional) identifies one region of interest from the image in the same
   * manner as `CreateReferenceImage`. If you do not specify the `bounding-poly`
   * column, then the system will try to detect regions of interest
   * automatically. At most one `bounding-poly` column is allowed per line. If
   * the image contains multiple regions of interest, add a line to the CSV file
   * that includes the same product information, and the `bounding-poly` values
   * for each region of interest. The `bounding-poly` column must contain an
   * even number of comma-separated numbers, in the format
   * "p1_x,p1_y,p2_x,p2_y,...,pn_x,pn_y". Use non-negative integers for absolute
   * bounding polygons, and float values in [0, 1] for normalized bounding
   * polygons. The system will resize the image if the image resolution is too
   * large to process (larger than 20MP).
   *
   * @param string $csvFileUri
   */
  public function setCsvFileUri($csvFileUri)
  {
    $this->csvFileUri = $csvFileUri;
  }
  /**
   * @return string
   */
  public function getCsvFileUri()
  {
    return $this->csvFileUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportProductSetsGcsSource::class, 'Google_Service_Vision_ImportProductSetsGcsSource');
