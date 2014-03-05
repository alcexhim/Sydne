<?php
	// CueCatDecoder4, Mike Becker's CueCat Decoder, translated from C# to PHP
	// Based off CueCatDecoder3, the InCueBus CueCat Decoder originally written by Matt Musante. This version adds translation support for CC! (CueCat standard) barcodes.
	// 
	// Copyright (C) 2012  Mike Becker
	// 
	// This program is free software; you can redistribute it and/or
	// modify it under the terms of the GNU General Public License
	// as published by the Free Software Foundation; either version 2
	// of the License, or (at your option) any later version.
	// 
	// This program is distributed in the hope that it will be useful,
	// but WITHOUT ANY WARRANTY; without even the implied warranty of
	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	// GNU General Public License for more details.
	// 
	// You should have received a copy of the GNU General Public License
	// along with this program; if not, write to the Free Software
	// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
	
	class BarcodeInformation
	{
		public $RawData;
		public $Value;
		public $Type;
		public $SerialNumber;
	}
	class CueCatDecoder4
	{
		public $EnableCueCatDecoding = true;
		public $EnableFormatting = true;
		public $Separator = "-";
		
		public function TryParse($input)
		{
			try
			{
				$w = $this->Parse($input);
				return $w;
			}
			catch (Exception $ex)
			{
				return false;
			}
		}
		public function Parse($input)
		{
			$w = explode(".", $input);
			if (count($w) != 5) throw new Exception("Encoded data must begin and end with a period, and must contain 3 segments, each separated by a period.");
			
			$bi = new BarcodeInformation();
			
			$bi->SerialNumber = $this->Decode($w[1]);
			$bi->Type = $this->Decode($w[2]);
			$bi->RawData = $input;
			$bi->Value = $this->Decode($w[3]);
			
			if ($this->EnableCueCatDecoding && $bi->Type == "CC!")
			{
				$formatted = "C 01 ";
				for ($i = 0; $i < strlen($bi->Value); $i++)
				{
					$formatted .= str_pad((ord($bi->Value[$i]) - 32), 2, "0", STR_PAD_LEFT);
					if ($i < (strlen($bi->Value) - 1)) $formatted .= " ";
				}
				$bi->Value = $formatted;
			}
			if ($this->EnableFormatting && $bi->Type != "CC!")
			{
				$bi->Value = $this->Format($bi->Value, $bi->Type);
			}
			return $bi;
		}
		
		public function Decode($input)
		{
			$TABLE = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-";
			$tc1 = null;
			$tc2 = null;
			$tc3 = null;
			$tc4 = null;
			$dec = "";
			$Byte1 = 0;
			$Byte2 = 0;
			$Byte3 = 0;
			$Byte4 = 0;
			$FirstByte = 0;
			$SecondByte = 0;
			$ThirdByte = 0;
			
			// in case you're wondering, tc stands for temp. char.

			$inputLength = strlen($input);
			for ($a = 0; $a < $inputLength; $a += 4)
			{
				// puts each 6-bit character from the 4-character segment into
				// its own variable
				if ($a < $inputLength)
				{
					$tc1 = substr($input, $a, 1);
				}
				if ((a + 1) < $inputLength)
				{
					$tc2 = substr($input, $a + 1, 1);
				}
				if ((a + 2) < $inputLength)
				{
					$tc3 = substr($input, $a + 2, 1);
				}
				if ((a + 3) < $inputLength)
				{
					$tc4 = substr($input, $a + 3, 1);
				}
				
				// performs the lookup for each 6-bit character
				// and converts it to a byte
				
				if ($tc1 != null)
				{
					$ic1 = strpos($TABLE, $tc1);
					
					if ($ic1 !== FALSE)
					{
						// Additional check added for testing purposes.
						// CueCats should NEVER return a scan where this condition
						// is not satisfied, but people who copy-paste codes manually
						// might. ;)
						
						$Byte1 = $ic1;
					}
					else
					{
						$dec .= $input[$a];
						$a -= 3;
						continue;
					}
				}
				
				if ($tc2 != null)
				{
					$Byte2 = strpos($TABLE, $tc2);
				}
				if ($tc3 != null)
				{
					$Byte3 = strpos($TABLE, $tc3);
				}
				if ($tc4 != null)
				{
					$Byte4 = strpos($TABLE, $tc4);
				}
				
				// converts the 4, 6-bit bytes into 3, 8-bit bytes
				$FirstByte = floor(($Byte1 * 4) + (($Byte2 & 48) / 16));
				$SecondByte = floor((($Byte2 & 15) * 16) + (($Byte3 & 60) / 4));
				$ThirdByte = floor((($Byte3 & 3) * 64) + $Byte4);
				
				// xor's each 8-bit byte by 67d (cuecat specific)
				$FirstByte = ($FirstByte ^ 67);
				$SecondByte = ($SecondByte ^ 67);
				$ThirdByte = ($ThirdByte ^ 67);
				
				// adds new bytes to the decoded string
				$dec .= (chr($FirstByte) . chr($SecondByte) . chr($ThirdByte));
			}
			return $dec;
		}
	
		public function Format($input, $type)
		{
			$formatted = "";
			switch (strtolower($type))
			{
				case "upa":
				{
					if (strlen($input) == 12)
					{
						$formatted = $input[0] . $this->Separator . substr($input, 1, 5) . $this->Separator . substr($input, 6, 5) . $this->Separator . $input[strlen($input) - 1];
					}
					else
					{
						$formatted = $input;
					}
					break;
				}
				default:
				{
					$formatted = $input;
					break;
				}
			}
			return $formatted;
		}
	}
?>