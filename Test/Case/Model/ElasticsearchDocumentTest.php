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
 * @package ElasticsearchSourceTest
 * @subpackage Model
 */
class ElasticsearchDocumentTest extends ElasticsearchTest {

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

	/**
	 * Test search document
	 */
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

	/**
	 * Test scroll search document
	 */
	public function testScrollSearchDocument() {
		$params = array(
			'conditions' => array(
				'scroll' => '1m'
			),
			'fields' => array('title', 'rank', 'id'),
			'order' => array('rank' => 'desc'),
			'limit' => 100
		);
		$results = array();
		while ($result = $this->Elasticsearch->find('all', $params)) {
			$results = array_merge($results, $result);
		}
		debug($results);
		$this->assertCount(3, $results);
	}

	/**
	 * Test scroll scan search document
	 */
	public function testScrollScanSearchDocument() {
		$params = array(
			'conditions' => array(
				'query' => array(
					"term" => array("title" => "guratabaata")
				),
				'search_type' => 'scan',
				'scroll' => '1m'
			),
			'fields' => array('title', 'rank', 'id'),
			'order' => array('rank' => 'desc'),
			'limit' => 100
		);
		$results = array();
		while ($result = $this->Elasticsearch->find('all', $params)) {
			$results = array_merge($results, $result);
		}
		debug($results);
		$this->assertCount(3, $results);
	}

	/**
	 * Test get document
	 */
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

	/**
	 * Test get multiple documents
	 */
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

	/**
	 * Test update document
	 */
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
	}

	/**
	 * Test create document
	 */
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
	}

	/**
	 * Test delete document
	 */
	public function testDeleteDocument() {
		$id = 3;
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

	/**
	 * Test documents count
	 */
	public function testCountDocument() {
		Cache::config('Elasticsearch', array(
			'engine' => 'File',
			'prefix' => 'Elasticsearch_',
			'path' => CACHE,
			'mask' => 0777,
			'serialize' => true,
			'duration' => '+10 minutes'
		));
		Cache::clear(false, 'Elasticsearch');
		$this->Elasticsearch->cacheQueries = false;
		Configure::write('Cache.disable', true);
		debug($this->Elasticsearch->find('all'));
		$total = $this->Elasticsearch->getDatasource()->lastCandidates();
		debug($this->Elasticsearch->find('all', array('limit' => 1)));
		$total1 = $this->Elasticsearch->getDatasource()->lastCandidates();
		debug($this->Elasticsearch->find('all'));
		$total2 = $this->Elasticsearch->getDatasource()->lastCandidates();

		$this->assertNotEqual($total, 0);
		$this->assertNotEqual($total1, 0);
		$this->assertNotEqual($total2, 0);
		$this->assertEqual($total, $total2);
		$this->assertEqual($total1, $total2);
		$this->Elasticsearch->cacheQueries = true;
		Configure::write('Cache.disable', false);
		debug($this->Elasticsearch->find('all'));
		$total = $this->Elasticsearch->getDatasource()->lastCandidates();
		debug($this->Elasticsearch->find('all', array('limit' => 1)));
		$total1 = $this->Elasticsearch->getDatasource()->lastCandidates();
		debug($this->Elasticsearch->find('all'));
		$total2 = $this->Elasticsearch->getDatasource()->lastCandidates();

		$this->assertNotEqual($total, 0);
		$this->assertNotEqual($total1, 0);
		$this->assertNotEqual($total2, 0);
		$this->assertEqual($total, $total2);
		$this->assertEqual($total1, $total2);
	}

	/**
	 * Test highlight document
	 */
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
					'type' => 'new_test_type',
					'mapping' => array(
						'new_test_type' => array(
							'_timestamp' => array(
								'enabled' => true,
								'store' => true
							)
						)
					)
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
			'new_test_type' => array(
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
			)
		);
		$this->assertSame($expectedMappings, $mappings);
		$DB->execute($DB->dropSchema($Schema));
	}

	/**
	 * Test document exists
	 */
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

		$this->assertTrue($this->Elasticsearch->exists($result[$this->Elasticsearch->alias]['id'], array(), true));
		$this->Elasticsearch->delete($result[$this->Elasticsearch->alias]['id']);

		$this->assertFalse($this->Elasticsearch->exists($result[$this->Elasticsearch->alias]['id'], array(), true));
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
				'GET ///_search?size=10 HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP Content-Type: application/x-www-form-urlencoded Content-Length: 171 {"query":{"bool":{"must":[{"match":{"_all":{"query":"guratabaata 1"}}}]}}}',
				//explainationExists
				true
			),
			//set #4
			array(
				//rawQuery
				'GET /_search?size=10 HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP Content-Type: application/x-www-form-urlencoded Content-Length: 171 {"query":{"bool":{"must":[{"match":{"_all":{"query":"guratabaata 1"}}}]}}}',
				//explainationExists
				true
			),
			//set #5
			array(
				//rawQuery
				'GET /_ulala?size=10 HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP Content-Type: application/x-www-form-urlencoded Content-Length: 0',
				//explainationExists
				false
			),
			//set #6
			array(
				//rawQuery
				'GET /_ulala HTTP/1.1 Host: localhost:9200 Connection: close User-Agent: CakePHP Content-Type: application/x-www-form-urlencoded Content-Length: 0',
				//explainationExists
				false
			),
		);
	}

	/**
	 * Test transactions
	 * 
	 * @param array $params
	 * @param array $transactions
	 * @param bool $autoTransactions
	 * 
	 * @dataProvider bulkProvider
	 */
	public function testBulk($params, $transactions, $autoTransactions) {
		$Model = $this->Elasticsearch;
		$Model->setTransactionParams($params['table'], $params['params'], $params['transactionsField'], $params['method']);
		if (!$autoTransactions) {
			$Model->getDataSource()->begin();
		}
		foreach ($transactions as $transaction) {
			list($method, $options) = $transaction;
			$this->assertTrue((bool)call_user_func_array(array($Model, $method), $options));
		}
		if (!$autoTransactions) {
			$this->assertTrue($Model->getDataSource()->commit());
		}
		$this->assertEmpty($Model->getDataSource()->getTransactionParams());
	}

	/**
	 * Data source for testBulk
	 * 
	 * @return array
	 */
	public function bulkProvider() {
		return array(
			//set #0
			array(
				//params
				array(
					'table' => 'bulk',
					'params' => array(),
					'transactionsField' => 'transactions',
					'method' => HttpSource::METHOD_CREATE
				),
				//transactions
				array(
					array('saveAll', array(
							array(
								array('title' => 'bulk 1'),
								array('title' => 'bulk 2'),
								array('title' => 'bulk 3'),
							)
						))
				),
				//autoTransactions
				true
			),
			//set #1
			array(
				//params
				array(
					'table' => 'bulk',
					'params' => array(),
					'transactionsField' => 'transactions',
					'method' => HttpSource::METHOD_CREATE
				),
				//transactions
				array(
					array('save', array(
							array('title' => 'bulk 11'),
						)),
					array('save', array(
							array('title' => 'bulk 22'),
						)),
					array('save', array(
							array('title' => 'bulk 33'),
						)),
				),
				//autoTransactions
				false
			),
		);
	}

}
