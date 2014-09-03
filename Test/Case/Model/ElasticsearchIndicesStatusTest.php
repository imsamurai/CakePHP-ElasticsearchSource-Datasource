<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 18.10.2013
 * Time: 16:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('ElasticsearchTest', 'ElasticsearchSource.Test/');
App::uses('ElasticsearchIndicesStatus', 'ElasticsearchSource.Model');

/**
 * Tests indices status api
 *
 * @package ElasticsearchSourceTest
 * @subpackage Model
 */
class ElasticsearchIndicesStatusTest extends ElasticsearchTest {

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
		$this->Elasticsearch = new ElasticsearchIndicesStatus(false, null, 'elasticsearchTest');
		$this->Elasticsearch->setSource('indices_status', 'test_index', 'test_type');
	}

	/**
	 * Test status
	 */
	public function testStatus() {
		$result = $this->Elasticsearch->find('all', array('fields' => array('name')));
		debug($result);
		$this->assertNotEqual($result, false);
		$this->assertTrue(in_array('test_index', Hash::extract($result, "{n}.{$this->Elasticsearch->alias}.name"), true));
	}

}
