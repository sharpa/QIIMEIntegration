<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Utils;

class Helper {
	private static $helper = NULL;
	public static function setDefaultHelper(Helper $helper = NULL) {
		Helper::$helper = $helper;
	}
	public static function getHelper() {
		if (!Helper::$helper) {
			Helper::$helper = new Helper();
		}
		return Helper::$helper;
	}

	/**
	 * Takes an array of element arrays
	 * Groups the element arrays into categories, based on a shared category field
	 * Returns and array of categories, each category contains several element arrays
	 */
	public function categorizeArray(array $rawArray, $categoryField, $fieldToKeep = "") {
		$formattedArray = array();
		foreach ($rawArray as $element) {
			$category = $element[$categoryField];

			if ($fieldToKeep) {
				$element = $element[$fieldToKeep];
			}

			if (!isset($formattedArray[$category])) {
				$formattedArray[$category] = array();
			}
			$formattedArray[$category][] = $element;
		}
		return $formattedArray;
	}

	public function htmlentities($string) {
		if ($string === 0) {
			return 0;
		}
		else if (!$string) {
			return "";
		}

		if (defined('ENT_SUBSTITUE')) {
			$stringEsc = htmlentities($string, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE);
		}
		else {
			$stringEsc = htmlentities($string, ENT_QUOTES | ENT_IGNORE);
		}
		if (!$stringEsc) {
			throw new \Exception("Problem with html special char escaping; dropped whole string");
		}
		return $stringEsc;
	}
}
