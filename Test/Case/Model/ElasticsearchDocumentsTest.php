<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 *
 */
App::uses('ElasticsearchTest', 'ElasticsearchSource.Test/');
App::uses('ElasticsearchDocument', 'ElasticsearchSource.Model');

/**
 * Tests documents api - search/get/delete/update/create
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
class ElasticsearchDocumentsTest extends ElasticsearchTest {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.ElasticsearchSource.ElasticsearchArticle',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @param array $config_name
	 * @param array $config
	 */
	protected function _loadModel($config_name = 'testElasticsearchSource', $config = array()) {
		parent::_loadModel($config_name, $config);
		$this->Elasticsearch = new ElasticsearchDocument(false, null, $config_name);
	}

	public function test_search_document() {
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
		$this->assertEqual($result[$this->Elasticsearch->alias]['id'], 1);
		debug($result);
	}

	public function test_get_document() {
		$params = array(
			'conditions' => array(
				'id' => 2,
				'index' => 'test_index',
				'type' => 'test_type'
			),
			'fields' => array('title', 'rank', 'id')
		);

		$result = $this->Elasticsearch->find('first', $params);
		$this->assertNotEqual($result, false);
		$this->assertCount(1, $result);
		$this->assertEqual($result[$this->Elasticsearch->alias]['id'], 2);
		debug($result);
	}

	public function test_update_document() {
		$params = array(
			"title" => "Testaaa",
			"description" => 'test descr',
			"index" => "test_index",
			"type" => "test_type",
			"id" => mt_rand(),
			"refresh" => 1
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

	public function test_create_document() {
		$params = array(
			"title" => "Testaaattt",
			"description" => 'test descr 123',
			"index" => "test_index",
			"type" => "test_type",
			"refresh" => 1
		);
		$this->Elasticsearch->create();
		$result = $this->Elasticsearch->save($params);
		debug($result);
		$this->assertNotEqual($result, false);

		$resultCheck = $this->Elasticsearch->find('first', array(
			'conditions' => array(
				'query' => array(
					"ids" => array(
						"type" => $params['type'],
						"values" => array($result[$this->Elasticsearch->alias]['id'])
					)
				),
				'index' => $params['index']
			)
		));
		debug($resultCheck);
		$this->assertNotEqual($resultCheck, false);
		$this->assertCount(1, $resultCheck);
	}

	public function test_delete_document() {
		$params = array(
			"index" => "test_index",
			"type" => "test_type",
			"id" => 3
		);

		$result = $this->Elasticsearch->deleteAll($params);
		debug($result);
		$this->assertNotEqual($result, false);

		$resultCheck = $this->Elasticsearch->find('first', array(
			'conditions' => array(
				'query' => array(
					"ids" => array(
						"type" => $params['type'],
						"values" => array($params['id'])
					)
				),
				'index' => $params['index']
			)
		));
		debug($resultCheck);
		$this->assertNotEqual($resultCheck, true);
	}

}
