<?php

declare( strict_types = 1 );

namespace Maps\LegacyModel;

use DataValues\Geo\Values\LatLongValue;
use InvalidArgumentException;

/**
 * Class representing a collection of LatLongValue objects forming a line.
 *
 * @since 3.0
 *
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Line extends \Maps\LegacyModel\BaseStrokableElement {

	/**
	 * @since 3.0
	 *
	 * @var LatLongValue[]
	 */
	protected $coordinates;

	/**
	 * @since 3.0
	 *
	 * @param LatLongValue[] $coordinates
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( array $coordinates = [] ) {
		foreach ( $coordinates as $coordinate ) {
			if ( !( $coordinate instanceof LatLongValue ) ) {
				throw new InvalidArgumentException( 'Can only construct Line with LatLongValue objects' );
			}
		}

		$this->coordinates = $coordinates;
	}

	/**
	 * @since 3.0
	 *
	 * @return LatLongValue[]
	 */
	public function getLineCoordinates() {
		return $this->coordinates;
	}

	public function getJSONObject( string $defText = '', string $defTitle = '' ): array {
		$parentArray = parent::getJSONObject( $defText, $defTitle );
		$posArray = [];

		foreach ( $this->coordinates as $mapLocation ) {
			$posArray[] = [
				'lat' => $mapLocation->getLatitude(),
				'lon' => $mapLocation->getLongitude()
			];
		}

		$posArray = [ 'pos' => $posArray ];

		return array_merge( $parentArray, $posArray );
	}

}
