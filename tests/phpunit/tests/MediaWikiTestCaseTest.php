<?php

/**
 * @covers MediaWikiTestCase
 * @author Addshore
 */
class MediaWikiTestCaseTest extends MediaWikiTestCase {

	const GLOBAL_KEY_NONEXISTING = 'MediaWikiTestCaseTestGLOBAL-NONExisting';

	private static $startGlobals = array(
		'MediaWikiTestCaseTestGLOBAL-ExistingString' => 'foo',
		'MediaWikiTestCaseTestGLOBAL-ExistingStringEmpty' => '',
		'MediaWikiTestCaseTestGLOBAL-ExistingArray' => array( 1, 'foo' => 'bar' ),
		'MediaWikiTestCaseTestGLOBAL-ExistingArrayEmpty' => array(),
	);

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		foreach ( self::$startGlobals as $key => $value ) {
			$GLOBALS[$key] = $value;
		}
	}

	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();
		foreach ( self::$startGlobals as $key => $value ) {
			unset( $GLOBALS[$key] );
		}
	}

	public function provideExistingKeysAndNewValues() {
		$providedArray = array();
		foreach ( array_keys( self::$startGlobals ) as $key ) {
			$providedArray[] = array( $key, 'newValue' );
			$providedArray[] = array( $key, array( 'newValue' ) );
		}
		return $providedArray;
	}

	/**
	 * @dataProvider provideExistingKeysAndNewValues
	 *
	 * @covers MediaWikiTestCase::setMwGlobals
	 * @covers MediaWikiTestCase::tearDown
	 */
	public function testSetGlobalsAreRestoredOnTearDown( $globalKey, $newValue ) {
		$this->setMwGlobals( $globalKey, $newValue );
		$this->assertEquals(
			$newValue,
			$GLOBALS[$globalKey],
			'Global failed to correctly set'
		);

		$this->tearDown();

		$this->assertEquals(
			self::$startGlobals[$globalKey],
			$GLOBALS[$globalKey],
			'Global failed to be restored on tearDown'
		);
	}

	/**
	 * @dataProvider provideExistingKeysAndNewValues
	 *
	 * @covers MediaWikiTestCase::stashMwGlobals
	 * @covers MediaWikiTestCase::tearDown
	 */
	public function testStashedGlobalsAreRestoredOnTearDown( $globalKey, $newValue ) {
		$this->stashMwGlobals( $globalKey );
		$GLOBALS[$globalKey] = $newValue;
		$this->assertEquals(
			$newValue,
			$GLOBALS[$globalKey],
			'Global failed to correctly set'
		);

		$this->tearDown();

		$this->assertEquals(
			self::$startGlobals[$globalKey],
			$GLOBALS[$globalKey],
			'Global failed to be restored on tearDown'
		);
	}

	/**
	 * @covers MediaWikiTestCase::stashMwGlobals
	 */
	public function testExceptionThrownWhenStashingNonExistentGlobals() {
		$this->setExpectedException(
			'Exception',
			'Global with key ' . self::GLOBAL_KEY_NONEXISTING . ' doesn\'t exist and cant be stashed'
		);

		$this->stashMwGlobals( self::GLOBAL_KEY_NONEXISTING );
	}

}
