<?php
namespace NACOSS\Helpers;

class StringTrimmer {
	public static function trimSpaces(array $stringsArray) {
		$trimmed = [];
		foreach ($stringsArray as $key => $string) {
			$trimmed[$key] = trim($string);
		}

		return $trimmed;
	}
}
