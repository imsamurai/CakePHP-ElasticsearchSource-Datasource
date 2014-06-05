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

	public function testSearchDocument() {
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

	public function testGetDocument() {
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

	public function testGetMultipleDocuments() {
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

	public function testUpdateDocument() {
		$params = array(
			"title" => "Test update",
			"description" => 'test update document ' . __FUNCTION__ . '|' . __LINE__,
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
		$this->testDeleteDocument($params['id']);
	}

	public function testCreateDocument() {
		$params = array(
			"title" => "Test create",
			"description" => 'test create ' . __FUNCTION__ . '|' . __LINE__,
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
		$this->testDeleteDocument($result[$this->Elasticsearch->alias]['id']);
	}

	public function testDeleteDocument($id = null) {
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

	public function testCountDocument() {
		debug($this->Elasticsearch->find('all'));
		$total = $this->Elasticsearch->getDatasource()->lastCandidates();

		$this->assertNotEqual($total, 0);
	}

	public function testHighlightSearchDocument() {
		$params = array(
			'conditions' => array(
				'query' => array(
					"term" => array("title" => "guratabaata")
				),
				'size' => 1,
				'highlight' => array('fields' => array('title' => new Object()))
			),
			'order' => array('rank' => 'desc')
		);

		$result = $this->Elasticsearch->find('all', $params);
		debug($result);
		$res = Hash::extract($result, '{n}.' . $this->Elasticsearch->alias . '.highlight.title');

		$this->assertNotEmpty($res);
	}

	/**
	 * Test create/drop schema
	 */
	public function testCreateDropSchema() {
		$Schema = new CakeSchema(array(
			'name' => 'TestSuite',
			'index' => array(
				'tableParameters' => array(
					'index' => 'new_test_index'
				)
			),
			'mapping' => array(
				'tableParameters' => array(
					'index' => 'new_test_index',
					'type' => 'new_test_type'
				)
			),
		));
		$DB = $this->Elasticsearch->getDataSource();
		$DB->execute($DB->dropSchema($Schema));
		$result = $DB->execute($DB->createSchema($Schema, 'index'));
		$this->assertTrue((bool)$result[0]);
		$result = $DB->execute($DB->createSchema($Schema, 'index'));
		$this->assertFalse((bool)$result[0]);
		$result = $DB->execute($DB->createSchema($Schema, 'mapping'));
		$this->assertTrue((bool)$result[0]);

		$result = $DB->execute($DB->dropSchema($Schema, 'mapping'));
		$this->assertTrue((bool)$result[0]);
		$result = $DB->execute($DB->dropSchema($Schema, 'mapping'));
		$this->assertFalse((bool)$result[0]);

		$result = $DB->execute($DB->dropSchema($Schema, 'index'));
		$this->assertTrue((bool)$result[0]);
		$result = $DB->execute($DB->dropSchema($Schema, 'index'));
		$this->assertFalse((bool)$result[0]);
	}

	/**
	 * Test type mapping
	 */
	public function testMapping() {
		$Schema = new CakeSchema(array(
			'name' => 'TestSuite',
			'mapping' => array(
				'tableParameters' => array(
					'index' => 'test_index',
					'type' => 'new_test_type',
					'mapping' => array(
						"_timestamp" => array(
							"enabled" => true,
							"store" => true
						),
						"properties" => array(
							"title" => array(
								"type" => "string",
								"index" => "analyzed"
							),
							"description" => array(
								"type" => "string",
								"index" => "analyzed"
							),
							"pubtime" => array(
								"type" => "date",
								"format" => "basic_date_time_no_millis"
							),
						)
					)
				)
			)
		));
		$DB = $this->Elasticsearch->getDataSource();
		$DB->execute($DB->createSchema($Schema));
		$this->Elasticsearch->setSource('mapping', 'test_index', 'new_test_type');
		$mappings = $this->Elasticsearch->find('list', array('fields' => array('type', 'mapping')));

		$expectedMappings = array(
			'_timestamp' => array(
				'enabled' => true,
				'store' => true
			),
			'properties' => array(
				'description' => array(
					'type' => 'string'
				),
				'pubtime' => array(
					'type' => 'date',
					'format' => 'basic_date_time_no_millis'
				),
				'title' => array(
					'type' => 'string'
				)
			)
		);
		$this->assertSame($expectedMappings, $mappings);
		$DB->execute($DB->dropSchema($Schema));
	}

	public function testExistsDocument() {
		$params = array(
			"title" => "Test create",
			"description" => 'test create ' . __FUNCTION__ . '|' . __LINE__,
			"refresh" => 1
		);
		$this->Elasticsearch->create();
		$result = $this->Elasticsearch->save($params);
		debug($result);
		$this->assertNotEqual($result, false);

		$this->assertTrue($this->Elasticsearch->exists($result[$this->Elasticsearch->alias]['id']));
		$this->Elasticsearch->delete($result[$this->Elasticsearch->alias]['id']);

		$this->assertFalse($this->Elasticsearch->exists($result[$this->Elasticsearch->alias]['id']));
	}

	/**
	 * Test query explaination
	 * 
	 * @param string $rawQuery
	 * @param array $explainationExists
	 * 
	 * @dataProvider explainQueryProvider
	 */
	public function testExplainQuery($rawQuery, $explainationExists) {
		$explainations = $this->Elasticsearch->explainQuery($this->Elasticsearch->useDbConfig, $rawQuery);
//		debug($explainations);
		$this->assertSame($explainationExists, (bool)$explainations);
	}

	/**
	 * Data provider for testExplainQuery
	 * 
	 * @return array
	 */
	public function explainQueryProvider() {
		return array(
			//set #0
			array(
				//rawQuery
				'GET /_status HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP',
				//explainationExists
				false
			),
			//set #1
			array(
				//rawQuery
				'GET /test_index/test_type/_search?size=10 HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP Content-Type: application/x-www-form-urlencoded Content-Length: 171 {"query":{"bool":{"must":[{"match":{"_all":{"query":"guratabaata 1"}}}]}}}',
				//explainationExists
				true
			),
			//set #2
			array(
				//rawQuery
				'GET ///_search?size=10 HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP Content-Type: application/x-www-form-urlencoded Content-Length: 171 {"query":{"bool":{"must":[{"match":{"_all":{"query":"guratabaata 1"}}}]}}}',
				//explainationExists
				true
			),
			//set #3
			array(
				//rawQuery
				'GET //_search?size=10 HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP Content-Type: application/x-www-form-urlencoded Content-Length: 171 {"query":{"bool":{"must":[{"match":{"_all":{"query":"guratabaata 1"}}}]}}}',
				//explainationExists
				true
			)
		);
	}

}
