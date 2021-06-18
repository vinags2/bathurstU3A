<?php
namespace App\Traits;

trait Sanitizable
{

	/**
	 * Sanitize 'name' fields
	 * That is, trim, and capitalize all words
	 */
	static private function nameSanitizable($data) {
		return static::trimSanitizable(static::capitalizeAllWordsSanitizable($data));
	}

	/**
	 * Sanitize 'address' fields
	 */
	static private function addressSanitizable(array $data) {
		$addressLine1 = $data['line_1'] ?? null;
		$addressLine1 = static::nameSanitizable($addressLine1);
		$addressLine1 = static::removeUnitSanitizable($addressLine1);
		$addressLine1 = static::formatPoboxSanitizable($addressLine1);
		$addressLine1 = static::removeSpacesAroundSlashesSanitizable($addressLine1);
		$addressLine1 = static::abbrevateStreetNamesSanitizable($addressLine1);

		$addressLine2 = $data['line_2'] ?? null;
		$addressLine2 = static::nameSanitizable($addressLine2);
		$addressLine2 = static::removeUnitSanitizable($addressLine2);
		$addressLine2 = static::formatPoboxSanitizable($addressLine2);
		$addressLine2 = static::removeSpacesAroundSlashesSanitizable($addressLine2);
		$addressLine2 = static::abbrevateStreetNamesSanitizable($addressLine2);

		static::checkForAddressLine1beingJustANumberSanitizable($addressLine1, $addressLine2);
		$data['line_1'] = $addressLine1;
		$data['line_2'] = $addressLine2;
		
		return $data;
	}

	/**
	 * Remove spaces from the beginning and ends of the field.
	 * An Array is also accepted, in which case, each element of the array is trimmed.
	 * Important: run this function first before any of the others.
	 */
	static private function trimSanitizable($data)
	{
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = trim($value);
			}
		} else {
			$data = trim($data);
		}
		return $data;
	}

	/**
	 * Capitalize all words
	 */
	static private function capitalizeAllWordsSanitizable($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = ucwords(strtolower($value));
			}
		} else {
			$data = ucwords(strtolower($data));
		}
		return $data;
	}

	/**
	 * Remove the word 'unit', 'villa', etc from the address
	 * Ensure that trimSanitizable has been run first.
	 */
    static private function removeUnitSanitizable($address) {
        $pos = stripos($address,'unit');
        if ($pos === false) {
            $pos = stripos($address,'villa');
            if ($pos !== false) {
                $length = 5;
            }
        } else $length = 4;
        if (($pos !== false) and ($pos == 0)) {
            $address = trim(substr($address,$length));
		}
		return $address;
	}

	/**
	 * If the first address line is just a number. merge the two address lines, separating them by a slash.
	 */
    static private function  checkForAddressLine1beingJustANumberSanitizable(&$addressLine1, &$addressLine2) {
        if (preg_match ("/^([0-9]+)$/", $addressLine1)) {
            $addressLine1 = $addressLine1."/".$addressLine2;
            $addressLine2 = "";
        }
    }

    /**
	 *  remove spaces around slashes, eg 9 / 5 holroyd st to 9/5 holroyd st. and change 9,5 holroyd st to 9/5 holroyd st.
	 */
    static private function removeSpacesAroundSlashesSanitizable($address) {
        $comma = strpos($address,",");
        if (($comma !== false) && ($comma >= 0) && ($comma <= 7)) {
            $address = substr_replace($address,'/',$comma,1);
        }
        $spaceSlash = strpos($address," /");
        if (($spaceSlash !== false) && ($spaceSlash >= 0) && ($spaceSlash <= 7)) {
            $address = substr_replace($address,'/',$spaceSlash,2);
        }
        $slashSpace = strpos($address,"/ ");
        if (($slashSpace !== false) && ($slashSpace >= 0) && ($slashSpace <= 7)) {
            $address = substr_replace($address,'/',$slashSpace,2);
		}
		return $address;
    }

	/**
	 * Make sure that PO Box addresses are formatted as 'PO Box nnn' where nnn is the PO Box number.
	 */
	static private function formatPoboxSanitizable($address) {
        $box = stripos($address,"box");
        $p = stripos($address,"p");
        if (($box !== false) && ($p == 0)) {
            $address = 'PO Box '.substr($address,$box+4);
		}
		return $address;
	}

	static private function abbrevateStreetNamesSanitizable($address) {
		$abbreviations = [
			'St'   => 'Street',
			'Rd'   => 'Road',
			'Ave'  => 'Avenue',
			'Ct'   => 'Court',
			'Hwy'  => 'Highway',
			'Blvd' => 'Boulevard',
			'Cir'  => 'Circle',
			'Cor'  => 'Corner',
			'Crk'  => 'Creek',
			'Cres' => 'Crescent',
			'Cres' => 'Cresc',
			'Dr'   => 'Drive',
			'Jct'  => 'Junction',
			'Ln'   => 'Lane',
			'Mt'   => 'Mount',
			'Pl'   => 'Place',
			'Rdg'  => 'Ridge',
			'Sta'  => 'Station'
		];
		foreach ($abbreviations as $key => $value) {
			$address = static::abbreviateSanitizable($value, $key, $address);
		}
		return $address;
	}

	/**
	 * replace text in a string, especially 'street' to 'st' for example
	 * the text is at the end of the string.
	 */
	static private function abbreviateSanitizable($from, $to, $address) {
		$fromLength = strlen($from);
		if (($fromLength <= strlen($address)) and (strpos($address, $from, -$fromLength) !== false)) {
			return substr_replace($address, $to , -$fromLength);
		} else {
			return $address;
		}
	}
}