<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 *
 */
require_once App::pluginPath('ElasticsearchSource') . 'Test' . DS . 'Data' . DS . 'models.php';

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

	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		$this->_setConfig();
		$this->_loadModel();
		parent::__construct($name, $data, $dataName);
	}

	public function setUp() {
		parent::setUp();
		$this->_setConfig();
		$this->_loadModel();
	}

	protected function _setConfig() {
		Configure::delete('ElasticsearchSource');
		Configure::load('ElasticsearchSource.ElasticsearchSource');
		include App::pluginPath('ElasticsearchSource') . 'Test' . DS . 'Data' . DS . 'config.php';
	}

	protected function _loadModel($config_name = 'testElasticsearchSource', $config = array()) {
		$db_configs = ConnectionManager::enumConnectionObjects();

		if (!empty($db_configs['elasticsearchTest'])) {
			$TestDS = ConnectionManager::getDataSource('elasticsearchTest');
			$config += $TestDS->config;
		} else {
			$config += array(
				'datasource' => 'ElasticsearchSource.Http/ElasticsearchSource',
				'host' => '127.0.0.1',
				'port' => 9200,
				'timeout' => 5
			);
		}

		$config+=array('prefix' => '');

		ConnectionManager::create($config_name, $config);
		$this->Elasticsearch = new Elasticsearch(false, null, $config_name);
	}

}
