<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 *
 */

App::uses('ConnectionManager', 'Model');

/**
 * Tests
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
abstract class ElasticsearchTest extends CakeTestCase {

	/**
	 * Elasticsearch Model
	 *
	 * @var Elasticsearch
	 */
	public $Elasticsearch = null;
	
	/**
	 * {@inheritdoc}
	 *
	 * @var bool
	 */
	public $autoFixtures = false;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		static::setConfig();
	}
	
	/**
	 * Sets config for source
	 */
	public static function setConfig() {
		Configure::delete('ElasticsearchSource');
		Configure::load('ElasticsearchSource.ElasticsearchSource');
		include App::pluginPath('ElasticsearchSource') . 'Test' . DS . 'Data' . DS . 'config.php';
	}
}


