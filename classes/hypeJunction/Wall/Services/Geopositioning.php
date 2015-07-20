<?php

namespace hypeJunction\Wall\Services;

class Geopositioning {

	const CLASSNAME = __CLASS__;
	const COOKIE_NAME = 'Elgg_Wall_Geop';
	
	/**
	 * Get coordinates and location name of the current session
	 * @return array
	 */
	public function get() {
		if (isset($_COOKIE[self::COOKIE_NAME])) {
			return unserialize(base64_decode($_COOKIE[self::COOKIE_NAME]));
		}
		return array(
			'location' => '',
			'latitude' => 0,
			'longitude' => 0
		);
	}

	/**
	 * Set session geopositioning
	 *
	 * @param string $location  Location
	 * @param float  $latitude  Latitude
	 * @param float  $longitude Longitude
	 * @return stdClass
	 */
	public function set($location = '', $latitude = 0, $longitude = 0) {

		$location = sanitize_string($location);
		$lat = (float) $latitude;
		$long = (float) $longitude;

		if (!$lat || !$long) {
			$latlong = $this->geocode($location);
			if ($latlong) {
				$lat = elgg_extract('lat', $latlong);
				$long = elgg_extract('long', $latlong);
			}
		}

		$geopositioning = array(
			'location' => $location,
			'latitude' => $lat,
			'longitude' => $long
		);
		$cookie_value = base64_encode(serialize($geopositioning));
		if (\hypeJunction\Integration::isElggVersionAbove('1.9.0')) {
			$cookie = new \ElggCookie(self::COOKIE_NAME);
			$cookie->value = $cookie_value;
			elgg_set_cookie($cookie);
		} else {
			setcookie(self::COOKIE_NAME, $cookie_value, strtotime("+30days"), "/", "");
		}

		return (object) $geopositioning;
	}

	/**
	 * Geocodes a location
	 *
	 * @param string $location Address to geocode
	 * @return array
	 */
	public function geocode($location = '') {
		return elgg_trigger_plugin_hook('geocode', 'location', array('location' => $location));
	}

}
