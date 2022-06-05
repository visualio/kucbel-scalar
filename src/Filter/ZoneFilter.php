<?php

namespace Kucbel\Scalar\Filter;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Nette\SmartObject;

class ZoneFilter implements FilterInterface
{
	use SmartObject;

	/**
	 * @var DateTimeZone
	 */
	protected $zone;

	/**
	 * ZoneFilter constructor.
	 *
	 * @param DateTimeZone | null $zone
	 */
	function __construct( DateTimeZone $zone = null )
	{
		$this->zone = $zone ?? new DateTimeZone( date_default_timezone_get() );
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function clear( mixed $value ) : mixed
	{
		if( $value instanceof DateTime or $value instanceof DateTimeImmutable ) {
			$value = $value->setTimezone( $this->zone );
		}

		return $value;
	}
}
