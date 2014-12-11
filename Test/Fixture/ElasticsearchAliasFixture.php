<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 */
App::uses("HttpSourceTestFixture", "HttpSource.TestSuite/Fixture");

/**
 * Alias fixture
 *
 * @package ElasticsearchSourceTest
 * @subpackage Test.Fixture
 */
class ElasticsearchAliasFixture extends HttpSourceTestFixture {

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
	public $model = "ElasticsearchSource.ElasticsearchAlias";

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = array(
		array("name" => 'test_alias1', "index" => "test_index", "filter" => array('query' => array("term" => array("title" => "guratabaata"))), "routing" => 1),
		array("name" => 'test_alias2', "index" => "test_index", "filter" => array('query' => array("term" => array("title" => "guratabaata"))), "routing" => 2),
		array("name" => 'test_alias3', "index" => "test_index", "filter" => array('query' => array("term" => array("title" => "guratabaata"))), "routing" => 3)
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
		$this->_Model->setSource('aliases');
	}

}
