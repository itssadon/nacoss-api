<?php
namespace NACOSS\Helpers;

use Carbon\Carbon;

class DateTimeHelper extends \DateTime {

	public function isAfter($date) {
		$someDate = new \DateTime($date);

		return $this > $someDate;
	}

	public function isBefore($date) {
		$someDate = new \DateTime($date);

		return $this < $someDate;
	}

	public function is($date) {
		$someDate = new \DateTime($date);

		return $this == $someDate;
	}

	public function diffForHumans(DateTimeHelper $anotherDate) {
		$currentDate = Carbon::instance($this);
		return $currentDate->diffForHumans(Carbon::instance($anotherDate));
	}

	public function getParsedDate() {
		$carbonDate = Carbon::instance($this);
		$dateForHumans = $carbonDate->diffForHumans();

		return str_replace("from now", "to go", $dateForHumans);
	}

	public function getParsedTime() {
		return $this->format('h:i a');
	}

	public function getDate($date) {
		$someDate = new \DateTime($date);
		return $someDate->format('d\t\h M, Y');
	}
}