<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\ParserHooks;

use Maps\ParserHooks\MapsDocFunction;

/**
 * @covers MapsDocFunction
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsDocTest extends ParserHookTest {

	/**
	 * @see ParserHookTest::parametersProvider
	 */
	public function parametersProvider() {
		$paramLists = [];

		$paramLists[] = [];

		return $this->arrayWrap( $paramLists );
	}

	/**
	 * @see ParserHookTest::processingProvider
	 */
	public function processingProvider() {
		$argLists = [];

		$values = [ 'service' => 'googlemaps3' ];

		$expected = [ 'service' => 'googlemaps3' ];

		$argLists[] = [ $values, $expected ];

		$values = [ 'service' => 'GOOGLEmaps3' ];

		$expected = [ 'service' => 'googlemaps3' ];

		$argLists[] = [ $values, $expected ];

		return $argLists;
	}

	/**
	 * @see ParserHookTest::getInstance
	 */
	protected function getInstance() {
		return new \Maps\ParserHooks\MapsDocFunction();
	}

}
