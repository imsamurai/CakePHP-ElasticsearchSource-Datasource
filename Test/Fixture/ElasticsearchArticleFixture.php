<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
App::uses("HttpSourceTestFixture", "HttpSource.TestSuite/Fixture");

/**
 * Main fixture
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
class ElasticsearchArticleFixture extends HttpSourceTestFixture {

	/**
	 * Fixture Datasource
	 *
	 * @var string
	 */
	public $useDbConfig = "elasticsearchTest";

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $model = "ElasticsearchSource.ElasticsearchDocument";

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = array(
		array("id" => 1, "title" => "guratabaata 1", "description" => "test article 1", "rank" => 1, "refresh" => 1),
		array("id" => 2, "title" => "guratabaata 2", "description" => "test article 2", "rank" => 2, "refresh" => 1),
		array("id" => 3, "title" => "guratabaata 3", "description" => "test article 3", "rank" => 3, "refresh" => 1)
	);

	/**
	 * {@inheritdoc}
	 *
	 * @return void
	 * @throws MissingModelException Whe importing from a model that does not exist.
	 */
	public function init() {
		ElasticsearchTest::setConfig();
		parent::init();
		$this->_Model->setSource('document', 'test_index', 'test_type');
	}
	
}
