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
class ElasticsearchTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.ElasticsearchSource.ElasticsearchArticle',
	);

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

	public function tearDown() {
		parent::tearDown();
		debug(ConnectionManager::getDataSource('testElasticsearchSource')->getLog(false, false));
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

	public function test_search_document() {
		$this->Elasticsearch->setSource('search');
		$r = $this->Elasticsearch->find('all', array('conditions' => array('index' => 'test_index', 'type' => 'test_type')));
		debug($r);
		$params = array(
			'conditions' => array(
				'query' => array(
					"term" => array("title" => "guratabaata")
				),
				'index' => 'test_index'
			),
			'fields' => array('title', 'rank', 'id'),
			'order' => array('rank' => 'desc'),
			'offset' => 2
		);

		$result = $this->Elasticsearch->find('first', $params);
		$this->assertNotEqual($result, false);
		$this->assertCount(1, $result);
		debug($result);
	}

	public function test_add_document() {
		$this->Elasticsearch->setSource('search');

		$params = array(
			"title" => "Testaaa",
			"description" => 'test descr',
			"index" => "test_index",
			"type" => "test_type",
			"id" => "3425234532543532452352345"
		);

		$result = $this->Elasticsearch->save($params);
		debug($result);
		$this->assertNotEqual($result, false);

		$resultCheck = $this->Elasticsearch->find('first', array(
			'conditions' => array(
				'query' => array(
					"ids" => array(
						"type" => "test_type",
						"values" => array($params['id'])
					)
				),
				'index' => 'test_index'
			)
		));
		debug($resultCheck);
		$this->assertNotEqual($resultCheck, false);
		$this->assertCount(1, $resultCheck);
	}

}
