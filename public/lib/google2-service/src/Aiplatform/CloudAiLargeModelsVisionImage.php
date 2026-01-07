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

namespace Google\Service\Aiplatform;

class CloudAiLargeModelsVisionImage extends \Google\Model
{
  /**
   * Image encoding, encoded as "image/png" or "image/jpg".
   *
   * @var string
   */
  public $encoding;
  /**
   * Generation seed for the sampled image. This parameter is exposed to the
   * user only if one of the following is true: 1. The user specified per-
   * example seeds in the request. 2. The user doesn't specify the generation
   * seed in the request.
   *
   * @var int
   */
  public $generationSeed;
  /**
   * Raw bytes.
   *
   * @var string
   */
  public $image;
  protected $imageRaiScoresType = CloudAiLargeModelsVisionImageRAIScores::class;
  protected $imageRaiScoresDataType = '';
  protected $imageSizeType = CloudAiLargeModelsVisionImageImageSize::class;
  protected $imageSizeDataType = '';
  protected $raiInfoType = CloudAiLargeModelsVisionRaiInfo::class;
  protected $raiInfoDataType = '';
  protected $semanticFilterResponseType = CloudAiLargeModelsVisionSemanticFilterResponse::class;
  protected $semanticFilterResponseDataType = '';
  /**
   * Text/Expanded text input for imagen.
   *
   * @var string
   */
  public $text;
  /**
   * Path to another storage (typically Google Cloud Storage).
   *
   * @var string
   */
  public $uri;

  /**
   * Image encoding, encoded as "image/png" or "image/jpg".
   *
   * @param string $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Generation seed for the sampled image. This parameter is exposed to the
   * user only if one of the following is true: 1. The user specified per-
   * example seeds in the request. 2. The user doesn't specify the generation
   * seed in the request.
   *
   * @param int $generationSeed
   */
  public function setGenerationSeed($generationSeed)
  {
    $this->generationSeed = $generationSeed;
  }
  /**
   * @return int
   */
  public function getGenerationSeed()
  {
    return $this->generationSeed;
  }
  /**
   * Raw bytes.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * RAI scores for generated image.
   *
   * @param CloudAiLargeModelsVisionImageRAIScores $imageRaiScores
   */
  public function setImageRaiScores(CloudAiLargeModelsVisionImageRAIScores $imageRaiScores)
  {
    $this->imageRaiScores = $imageRaiScores;
  }
  /**
   * @return CloudAiLargeModelsVisionImageRAIScores
   */
  public function getImageRaiScores()
  {
    return $this->imageRaiScores;
  }
  /**
   * Image size. The size of the image. Can be self reported, or computed from
   * the image bytes.
   *
   * @param CloudAiLargeModelsVisionImageImageSize $imageSize
   */
  public function setImageSize(CloudAiLargeModelsVisionImageImageSize $imageSize)
  {
    $this->imageSize = $imageSize;
  }
  /**
   * @return CloudAiLargeModelsVisionImageImageSize
   */
  public function getImageSize()
  {
    return $this->imageSize;
  }
  /**
   * RAI info for image.
   *
   * @param CloudAiLargeModelsVisionRaiInfo $raiInfo
   */
  public function setRaiInfo(CloudAiLargeModelsVisionRaiInfo $raiInfo)
  {
    $this->raiInfo = $raiInfo;
  }
  /**
   * @return CloudAiLargeModelsVisionRaiInfo
   */
  public function getRaiInfo()
  {
    return $this->raiInfo;
  }
  /**
   * Semantic filter info for image.
   *
   * @param CloudAiLargeModelsVisionSemanticFilterResponse $semanticFilterResponse
   */
  public function setSemanticFilterResponse(CloudAiLargeModelsVisionSemanticFilterResponse $semanticFilterResponse)
  {
    $this->semanticFilterResponse = $semanticFilterResponse;
  }
  /**
   * @return CloudAiLargeModelsVisionSemanticFilterResponse
   */
  public function getSemanticFilterResponse()
  {
    return $this->semanticFilterResponse;
  }
  /**
   * Text/Expanded text input for imagen.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Path to another storage (typically Google Cloud Storage).
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiLargeModelsVisionImage::class, 'Google_Service_Aiplatform_CloudAiLargeModelsVisionImage');
