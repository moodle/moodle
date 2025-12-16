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

class RotatingBarcode extends \Google\Model
{
  public const RENDER_ENCODING_RENDER_ENCODING_UNSPECIFIED = 'RENDER_ENCODING_UNSPECIFIED';
  /**
   * UTF_8 encoding for barcodes. This is only supported for barcode type
   * qrCode.
   */
  public const RENDER_ENCODING_UTF_8 = 'UTF_8';
  public const TYPE_BARCODE_TYPE_UNSPECIFIED = 'BARCODE_TYPE_UNSPECIFIED';
  /**
   * Not supported for Rotating Barcodes.
   */
  public const TYPE_AZTEC = 'AZTEC';
  /**
   * Legacy alias for `AZTEC`. Deprecated. Not supported for Rotating Barcodes.
   *
   * @deprecated
   */
  public const TYPE_aztec = 'aztec';
  /**
   * Not supported for Rotating Barcodes.
   */
  public const TYPE_CODE_39 = 'CODE_39';
  /**
   * Legacy alias for `CODE_39`. Deprecated. Not supported for Rotating
   * Barcodes.
   *
   * @deprecated
   */
  public const TYPE_code39 = 'code39';
  /**
   * Not supported for Rotating Barcodes.
   */
  public const TYPE_CODE_128 = 'CODE_128';
  /**
   * Legacy alias for `CODE_128`. Deprecated. Not supported for Rotating
   * Barcodes.
   *
   * @deprecated
   */
  public const TYPE_code128 = 'code128';
  /**
   * Not supported for Rotating Barcodes.
   */
  public const TYPE_CODABAR = 'CODABAR';
  /**
   * Legacy alias for `CODABAR`. Deprecated. Not supported for Rotating
   * Barcodes.
   *
   * @deprecated
   */
  public const TYPE_codabar = 'codabar';
  /**
   * A 2D matrix barcode consisting of black and white. Cells or modules are
   * arranged in either a square or rectangle. Not supported for Rotating
   * Barcodes.
   */
  public const TYPE_DATA_MATRIX = 'DATA_MATRIX';
  /**
   * Legacy alias for `DATA_MATRIX`. Deprecated. Not supported for Rotating
   * Barcodes.
   *
   * @deprecated
   */
  public const TYPE_dataMatrix = 'dataMatrix';
  /**
   * Not supported for Rotating Barcodes.
   */
  public const TYPE_EAN_8 = 'EAN_8';
  /**
   * Legacy alias for `EAN_8`. Deprecated. Not supported for Rotating Barcodes.
   *
   * @deprecated
   */
  public const TYPE_ean8 = 'ean8';
  /**
   * Not supported for Rotating Barcodes.
   */
  public const TYPE_EAN_13 = 'EAN_13';
  /**
   * Legacy alias for `EAN_13`. Deprecated. Not supported for Rotating Barcodes.
   *
   * @deprecated
   */
  public const TYPE_ean13 = 'ean13';
  /**
   * Legacy alias for `EAN_13`. Deprecated. Not supported for Rotating Barcodes.
   *
   * @deprecated
   */
  public const TYPE_EAN13 = 'EAN13';
  /**
   * 14 digit ITF code Not supported for Rotating Barcodes.
   */
  public const TYPE_ITF_14 = 'ITF_14';
  /**
   * Legacy alias for `ITF_14`. Deprecated. Not supported for Rotating Barcodes.
   *
   * @deprecated
   */
  public const TYPE_itf14 = 'itf14';
  /**
   * Supported for Rotating Barcodes.
   */
  public const TYPE_PDF_417 = 'PDF_417';
  /**
   * Legacy alias for `PDF_417`. Deprecated.
   *
   * @deprecated
   */
  public const TYPE_pdf417 = 'pdf417';
  /**
   * Legacy alias for `PDF_417`. Deprecated.
   *
   * @deprecated
   */
  public const TYPE_PDF417 = 'PDF417';
  /**
   * Supported for Rotating Barcodes.
   */
  public const TYPE_QR_CODE = 'QR_CODE';
  /**
   * Legacy alias for `QR_CODE`. Deprecated.
   *
   * @deprecated
   */
  public const TYPE_qrCode = 'qrCode';
  /**
   * Legacy alias for `QR_CODE`. Deprecated.
   *
   * @deprecated
   */
  public const TYPE_qrcode = 'qrcode';
  /**
   * 11 or 12 digit codes Not supported for Rotating Barcodes.
   */
  public const TYPE_UPC_A = 'UPC_A';
  /**
   * Legacy alias for `UPC_A`. Deprecated. Not supported for Rotating Barcodes.
   *
   * @deprecated
   */
  public const TYPE_upcA = 'upcA';
  /**
   * Renders the field as a text field. The `alternateText` field may not be
   * used with a barcode of type `textOnly`. Not supported for Rotating
   * Barcodes.
   */
  public const TYPE_TEXT_ONLY = 'TEXT_ONLY';
  /**
   * Legacy alias for `TEXT_ONLY`. Deprecated. Not supported for Rotating
   * Barcodes.
   *
   * @deprecated
   */
  public const TYPE_textOnly = 'textOnly';
  /**
   * An optional text that will override the default text that shows under the
   * barcode. This field is intended for a human readable equivalent of the
   * barcode value, used when the barcode cannot be scanned.
   *
   * @var string
   */
  public $alternateText;
  protected $initialRotatingBarcodeValuesType = RotatingBarcodeValues::class;
  protected $initialRotatingBarcodeValuesDataType = '';
  /**
   * The render encoding for the barcode. When specified, barcode is rendered in
   * the given encoding. Otherwise best known encoding is chosen by Google.
   *
   * @var string
   */
  public $renderEncoding;
  protected $showCodeTextType = LocalizedString::class;
  protected $showCodeTextDataType = '';
  protected $totpDetailsType = RotatingBarcodeTotpDetails::class;
  protected $totpDetailsDataType = '';
  /**
   * The type of this barcode.
   *
   * @var string
   */
  public $type;
  /**
   * String encoded barcode value. This string supports the following
   * substitutions: * {totp_value_n}: Replaced with the TOTP value (see
   * TotpDetails.parameters). * {totp_timestamp_millis}: Replaced with the
   * timestamp (millis since epoch) at which the barcode was generated. *
   * {totp_timestamp_seconds}: Replaced with the timestamp (seconds since epoch)
   * at which the barcode was generated.
   *
   * @var string
   */
  public $valuePattern;

  /**
   * An optional text that will override the default text that shows under the
   * barcode. This field is intended for a human readable equivalent of the
   * barcode value, used when the barcode cannot be scanned.
   *
   * @param string $alternateText
   */
  public function setAlternateText($alternateText)
  {
    $this->alternateText = $alternateText;
  }
  /**
   * @return string
   */
  public function getAlternateText()
  {
    return $this->alternateText;
  }
  /**
   * Input only. NOTE: This feature is only available for the transit vertical.
   * Optional set of initial rotating barcode values. This allows a small subset
   * of barcodes to be included with the object. Further rotating barcode values
   * must be uploaded with the UploadRotatingBarcodeValues endpoint.
   *
   * @param RotatingBarcodeValues $initialRotatingBarcodeValues
   */
  public function setInitialRotatingBarcodeValues(RotatingBarcodeValues $initialRotatingBarcodeValues)
  {
    $this->initialRotatingBarcodeValues = $initialRotatingBarcodeValues;
  }
  /**
   * @return RotatingBarcodeValues
   */
  public function getInitialRotatingBarcodeValues()
  {
    return $this->initialRotatingBarcodeValues;
  }
  /**
   * The render encoding for the barcode. When specified, barcode is rendered in
   * the given encoding. Otherwise best known encoding is chosen by Google.
   *
   * Accepted values: RENDER_ENCODING_UNSPECIFIED, UTF_8
   *
   * @param self::RENDER_ENCODING_* $renderEncoding
   */
  public function setRenderEncoding($renderEncoding)
  {
    $this->renderEncoding = $renderEncoding;
  }
  /**
   * @return self::RENDER_ENCODING_*
   */
  public function getRenderEncoding()
  {
    return $this->renderEncoding;
  }
  /**
   * Optional text that will be shown when the barcode is hidden behind a click
   * action. This happens in cases where a pass has Smart Tap enabled. If not
   * specified, a default is chosen by Google.
   *
   * @param LocalizedString $showCodeText
   */
  public function setShowCodeText(LocalizedString $showCodeText)
  {
    $this->showCodeText = $showCodeText;
  }
  /**
   * @return LocalizedString
   */
  public function getShowCodeText()
  {
    return $this->showCodeText;
  }
  /**
   * Details used to evaluate the {totp_value_n} substitutions.
   *
   * @param RotatingBarcodeTotpDetails $totpDetails
   */
  public function setTotpDetails(RotatingBarcodeTotpDetails $totpDetails)
  {
    $this->totpDetails = $totpDetails;
  }
  /**
   * @return RotatingBarcodeTotpDetails
   */
  public function getTotpDetails()
  {
    return $this->totpDetails;
  }
  /**
   * The type of this barcode.
   *
   * Accepted values: BARCODE_TYPE_UNSPECIFIED, AZTEC, aztec, CODE_39, code39,
   * CODE_128, code128, CODABAR, codabar, DATA_MATRIX, dataMatrix, EAN_8, ean8,
   * EAN_13, ean13, EAN13, ITF_14, itf14, PDF_417, pdf417, PDF417, QR_CODE,
   * qrCode, qrcode, UPC_A, upcA, TEXT_ONLY, textOnly
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * String encoded barcode value. This string supports the following
   * substitutions: * {totp_value_n}: Replaced with the TOTP value (see
   * TotpDetails.parameters). * {totp_timestamp_millis}: Replaced with the
   * timestamp (millis since epoch) at which the barcode was generated. *
   * {totp_timestamp_seconds}: Replaced with the timestamp (seconds since epoch)
   * at which the barcode was generated.
   *
   * @param string $valuePattern
   */
  public function setValuePattern($valuePattern)
  {
    $this->valuePattern = $valuePattern;
  }
  /**
   * @return string
   */
  public function getValuePattern()
  {
    return $this->valuePattern;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RotatingBarcode::class, 'Google_Service_Walletobjects_RotatingBarcode');
