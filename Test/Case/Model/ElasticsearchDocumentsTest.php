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
	 */
	public function setUp() {
		parent::setUp();
		$this->loadFixtures('ElasticsearchArticle');
		$this->Elasticsearch = new ElasticsearchDocument(false, null, 'elasticsearchTest');
		$this->Elasticsearch->setSource('document', 'test_index', 'test_type');
	}

	public function test_search_document() {
		$params = array(
			'conditions' => array(
				'query' => array(
					"term" => array("title" => "guratabaata")
				)
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
				'id' => 2
			),
			'fields' => array('title', 'rank', 'id')
		);

		$result = $this->Elasticsearch->find('first', $params);
		$this->assertNotEqual($result, false);
		$this->assertCount(1, $result);
		$this->assertEqual($result[$this->Elasticsearch->alias]['id'], 2);
		debug($result);
	}
	
	public function test_get_multiple_documents() {
		$params = array(
			'conditions' => array(
				'id' => array(1, 2)
			)
		);

		$results = $this->Elasticsearch->find('all', $params);
		debug($results);
		$this->assertNotEqual($results, false);
		$this->assertCount(2, $results);
		foreach ($results as $result) {
			$this->assertTrue(in_array($result[$this->Elasticsearch->alias]['id'], $params['conditions']['id']));
		}
	}

	public function test_update_document() {
		$params = array(
			"title" => "Test update",
			"description" => 'test update document '. __FUNCTION__ .'|'. __LINE__,
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
				)
			)
		));
		debug($resultCheck);
		$this->assertNotEqual($resultCheck, false);
		$this->assertCount(1, $resultCheck);
		//not forget to delete document
		$this->test_delete_document($params['id']);
	}

	public function test_create_document() {
		$params = array(
			"title" => "Test create",
			"description" => 'test create '. __FUNCTION__ .'|'. __LINE__,
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
						"values" => array($result[$this->Elasticsearch->alias]['id'])
					)
				)
			)
		));
		debug($resultCheck);
		$this->assertNotEqual($resultCheck, false);
		$this->assertCount(1, $resultCheck);
		//not forget to delete document
		$this->test_delete_document($result[$this->Elasticsearch->alias]['id']);
	}

	public function test_delete_document($id = null) {
		$id = is_null($id) ? 3 : $id;

		$result = $this->Elasticsearch->delete($id);
		debug($result);
		$this->assertNotEqual($result, false);

		$resultCheck = $this->Elasticsearch->find('first', array(
			'conditions' => array(
				'query' => array(
					"ids" => array(
						"values" => array($id)
					)
				),
			)
		));
		debug($resultCheck);
		$this->assertNotEqual($resultCheck, true);
	}

}
