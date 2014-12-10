<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 *
 */
App::uses('ElasticsearchTest', 'ElasticsearchSource.Test/');
App::uses('ElasticsearchAlias', 'ElasticsearchSource.Model');

/**
 * Tests aliases
 *
 * @package ElasticsearchSourceTest
 * @subpackage Model
 */
class ElasticsearchAliasTest extends ElasticsearchTest {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.ElasticsearchSource.ElasticsearchArticle',
		'plugin.ElasticsearchSource.ElasticsearchAlias',
	);

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->loadFixtures('ElasticsearchArticle');
		$this->loadFixtures('ElasticsearchAlias');
		$this->Elasticsearch = new ElasticsearchAlias(false, null, 'elasticsearchTest');
		$this->Elasticsearch->setSource('aliases');
	}

	/**
	 * Test create alias
	 */
	public function testCreate() {
		$index = 'test_index';
		$alias = 'test_alias';
		$filter = array(
			'query' => array(
				"term" => array("title" => "guratabaata")
			)
		);
		$routing = 1;
		$result = $this->Elasticsearch->save(array(
			'index' => $index,
			'name' => $alias,
			'filter' => $filter,
			'routing' => $routing
		));
		$this->assertTrue((bool)$result);
	}

	/**
	 * Test create alias
	 */
	public function testRead() {
		$result = $this->Elasticsearch->find('all');
		debug($result);
		$this->assertCount(3, $result);
	}

	/**
	 * Test delete alias
	 */
	public function testDelete() {
		$result = $this->Elasticsearch->delete('test_alias1');
		debug($result);
		$this->assertTrue($result);
	}

}
