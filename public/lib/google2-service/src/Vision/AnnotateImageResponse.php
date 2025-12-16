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

class AnnotateImageResponse extends \Google\Collection
{
  protected $collection_key = 'textAnnotations';
  protected $contextType = ImageAnnotationContext::class;
  protected $contextDataType = '';
  protected $cropHintsAnnotationType = CropHintsAnnotation::class;
  protected $cropHintsAnnotationDataType = '';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  protected $faceAnnotationsType = FaceAnnotation::class;
  protected $faceAnnotationsDataType = 'array';
  protected $fullTextAnnotationType = TextAnnotation::class;
  protected $fullTextAnnotationDataType = '';
  protected $imagePropertiesAnnotationType = ImageProperties::class;
  protected $imagePropertiesAnnotationDataType = '';
  protected $labelAnnotationsType = EntityAnnotation::class;
  protected $labelAnnotationsDataType = 'array';
  protected $landmarkAnnotationsType = EntityAnnotation::class;
  protected $landmarkAnnotationsDataType = 'array';
  protected $localizedObjectAnnotationsType = LocalizedObjectAnnotation::class;
  protected $localizedObjectAnnotationsDataType = 'array';
  protected $logoAnnotationsType = EntityAnnotation::class;
  protected $logoAnnotationsDataType = 'array';
  protected $productSearchResultsType = ProductSearchResults::class;
  protected $productSearchResultsDataType = '';
  protected $safeSearchAnnotationType = SafeSearchAnnotation::class;
  protected $safeSearchAnnotationDataType = '';
  protected $textAnnotationsType = EntityAnnotation::class;
  protected $textAnnotationsDataType = 'array';
  protected $webDetectionType = WebDetection::class;
  protected $webDetectionDataType = '';

  /**
   * If present, contextual information is needed to understand where this image
   * comes from.
   *
   * @param ImageAnnotationContext $context
   */
  public function setContext(ImageAnnotationContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return ImageAnnotationContext
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * If present, crop hints have completed successfully.
   *
   * @param CropHintsAnnotation $cropHintsAnnotation
   */
  public function setCropHintsAnnotation(CropHintsAnnotation $cropHintsAnnotation)
  {
    $this->cropHintsAnnotation = $cropHintsAnnotation;
  }
  /**
   * @return CropHintsAnnotation
   */
  public function getCropHintsAnnotation()
  {
    return $this->cropHintsAnnotation;
  }
  /**
   * If set, represents the error message for the operation. Note that filled-in
   * image annotations are guaranteed to be correct, even when `error` is set.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * If present, face detection has completed successfully.
   *
   * @param FaceAnnotation[] $faceAnnotations
   */
  public function setFaceAnnotations($faceAnnotations)
  {
    $this->faceAnnotations = $faceAnnotations;
  }
  /**
   * @return FaceAnnotation[]
   */
  public function getFaceAnnotations()
  {
    return $this->faceAnnotations;
  }
  /**
   * If present, text (OCR) detection or document (OCR) text detection has
   * completed successfully. This annotation provides the structural hierarchy
   * for the OCR detected text.
   *
   * @param TextAnnotation $fullTextAnnotation
   */
  public function setFullTextAnnotation(TextAnnotation $fullTextAnnotation)
  {
    $this->fullTextAnnotation = $fullTextAnnotation;
  }
  /**
   * @return TextAnnotation
   */
  public function getFullTextAnnotation()
  {
    return $this->fullTextAnnotation;
  }
  /**
   * If present, image properties were extracted successfully.
   *
   * @param ImageProperties $imagePropertiesAnnotation
   */
  public function setImagePropertiesAnnotation(ImageProperties $imagePropertiesAnnotation)
  {
    $this->imagePropertiesAnnotation = $imagePropertiesAnnotation;
  }
  /**
   * @return ImageProperties
   */
  public function getImagePropertiesAnnotation()
  {
    return $this->imagePropertiesAnnotation;
  }
  /**
   * If present, label detection has completed successfully.
   *
   * @param EntityAnnotation[] $labelAnnotations
   */
  public function setLabelAnnotations($labelAnnotations)
  {
    $this->labelAnnotations = $labelAnnotations;
  }
  /**
   * @return EntityAnnotation[]
   */
  public function getLabelAnnotations()
  {
    return $this->labelAnnotations;
  }
  /**
   * If present, landmark detection has completed successfully.
   *
   * @param EntityAnnotation[] $landmarkAnnotations
   */
  public function setLandmarkAnnotations($landmarkAnnotations)
  {
    $this->landmarkAnnotations = $landmarkAnnotations;
  }
  /**
   * @return EntityAnnotation[]
   */
  public function getLandmarkAnnotations()
  {
    return $this->landmarkAnnotations;
  }
  /**
   * If present, localized object detection has completed successfully. This
   * will be sorted descending by confidence score.
   *
   * @param LocalizedObjectAnnotation[] $localizedObjectAnnotations
   */
  public function setLocalizedObjectAnnotations($localizedObjectAnnotations)
  {
    $this->localizedObjectAnnotations = $localizedObjectAnnotations;
  }
  /**
   * @return LocalizedObjectAnnotation[]
   */
  public function getLocalizedObjectAnnotations()
  {
    return $this->localizedObjectAnnotations;
  }
  /**
   * If present, logo detection has completed successfully.
   *
   * @param EntityAnnotation[] $logoAnnotations
   */
  public function setLogoAnnotations($logoAnnotations)
  {
    $this->logoAnnotations = $logoAnnotations;
  }
  /**
   * @return EntityAnnotation[]
   */
  public function getLogoAnnotations()
  {
    return $this->logoAnnotations;
  }
  /**
   * If present, product search has completed successfully.
   *
   * @param ProductSearchResults $productSearchResults
   */
  public function setProductSearchResults(ProductSearchResults $productSearchResults)
  {
    $this->productSearchResults = $productSearchResults;
  }
  /**
   * @return ProductSearchResults
   */
  public function getProductSearchResults()
  {
    return $this->productSearchResults;
  }
  /**
   * If present, safe-search annotation has completed successfully.
   *
   * @param SafeSearchAnnotation $safeSearchAnnotation
   */
  public function setSafeSearchAnnotation(SafeSearchAnnotation $safeSearchAnnotation)
  {
    $this->safeSearchAnnotation = $safeSearchAnnotation;
  }
  /**
   * @return SafeSearchAnnotation
   */
  public function getSafeSearchAnnotation()
  {
    return $this->safeSearchAnnotation;
  }
  /**
   * If present, text (OCR) detection has completed successfully.
   *
   * @param EntityAnnotation[] $textAnnotations
   */
  public function setTextAnnotations($textAnnotations)
  {
    $this->textAnnotations = $textAnnotations;
  }
  /**
   * @return EntityAnnotation[]
   */
  public function getTextAnnotations()
  {
    return $this->textAnnotations;
  }
  /**
   * If present, web detection has completed successfully.
   *
   * @param WebDetection $webDetection
   */
  public function setWebDetection(WebDetection $webDetection)
  {
    $this->webDetection = $webDetection;
  }
  /**
   * @return WebDetection
   */
  public function getWebDetection()
  {
    return $this->webDetection;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnnotateImageResponse::class, 'Google_Service_Vision_AnnotateImageResponse');
