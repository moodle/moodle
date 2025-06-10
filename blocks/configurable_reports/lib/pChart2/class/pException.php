<?php

/*
pException - pChart core class

Version     : 2.4.0-dev
Made by     : Created by Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the MIT license

*/

namespace pChart;

class pException extends \Exception
{
	public static function InvalidDimentions($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidCoordinates($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidImageType($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidImageFilter($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidInput($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidResourcePath($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function PieNoAbscissaException()
	{
		return new static('pPie: No Abscissa');
	}

	public static function PieNoDataSerieException()
	{
		return new static('pPie: No DataSerie');
	}

	public static function StockMissingSerieException()
	{
		return new static('pStock: No DataSerie');
	}

	public static function SpringIvalidConnectionsException()
	{
		return new static('pSpring: Connections needs to be an array');
	}

	public static function SpringInvalidInputException($text)
	{
		return new static(sprintf('pSprint: %s', $text));
	}

	public static function ZoneChartInvalidInputException($text)
	{
		return new static(sprintf('pCharts: %s', $text));
	}

	public static function ScatterInvalidInputException($text)
	{
		return new static(sprintf('pScatter: %s', $text));
	}

	public static function SurfaceInvalidInputException($text)
	{
		return new static(sprintf('pSurface: %s', $text));
	}

	public static function BubbleInvalidInputException($text)
	{
		return new static(sprintf('pBubble: %s', $text));
	}

	public static function PDF417EncoderError($text)
	{
		return new static(sprintf('PDF417: %s', $text));
	}

	public static function AztecEncoderError($text)
	{
		return new static(sprintf('Aztec: %s', $text));
	}

	public static function QRCodeEncoderError($text)
	{
		return new static(sprintf('QRCode: %s', $text));
	}
}
