<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 */

/**
 * All Elasticsearch Source Tests
 * 
 * @package ElasticsearchSourceTest
 * @subpackage Test
 */
class AllElasticsearchSourceTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Suite define the tests for this suite
	 *
	 * @return void
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Elasticsearch Source Tests');

		$path = App::pluginPath('ElasticsearchSource') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);
		return $suite;
	}

}
