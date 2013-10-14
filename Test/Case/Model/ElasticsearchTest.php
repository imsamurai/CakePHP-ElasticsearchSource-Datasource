<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
require_once dirname(__FILE__) . DS . 'models.php';

class ElasticsearchTest extends CakeTestCase {

	/**
	 * Elasticsearch Model
	 *
	 * @var Elasticsearch
	 */
	public $Elasticsearch = null;

	public function setUp() {
		parent::setUp();
		$this->_setConfig();
	}

	protected function _setConfig() {
		Configure::delete('ElasticsearchSource');
		Configure::load('ElasticsearchSource.ElasticsearchSource');
	}

	protected function _loadModel($config_name = 'elasticsearchSource', $config = array()) {
		$db_configs = ConnectionManager::enumConnectionObjects();

		if (!empty($db_configs['elasticsearchTest'])) {
			$TestDS = ConnectionManager::getDataSource('elasticsearchTest');
			$config += $TestDS->config;
		} else {
			$config += array(
				'datasource' => 'ElasticsearchSource.Http/ElasticsearchSource',
				'host' => 'example.com',
				'path' => 'yourpath',
				'port' => 80,
				'timeout' => 5
			);
		}

		ConnectionManager::drop($config_name);
		ConnectionManager::create($config_name, $config);
		$this->Elasticsearch = new Elasticsearch(false, null, $config_name);
	}

	public function test_simple_query() {
		$this->_loadModel();
		$this->Elasticsearch->setSource('_search');
		$params = array(
			'conditions' => array(
				'query' => array(
					"term" => array("title" => "apple")
				),
				'index' => 'news'
			),
			'fields' => array('title', 'rank'),
			'order' => array('rank' => 'desc'),
			'offset' => 2
		);

		$result = $this->Elasticsearch->find('first', $params);
		$this->assertNotEqual($result, false);
		$this->assertCount(1, $result);
		debug($result);
	}

	public function test_index_create() {
		$CF = HttpSourceConfigFactory::instance();
		$Config = $CF->load('ElasticsearchSource');
		$Config->endpoint(2)
				->addCondition($CF->condition()->name('title'))
				->addCondition($CF->condition()->name('description'));

		$this->_loadModel();

		$this->Elasticsearch->setSource('index_create');

		$params = array(
			"title" => "Test 5",
			"description" => 'test descr',
			"index" => "news",
			"type" => "article",
			"id" => "3425234532543532452352345"
		);

		$result = $this->Elasticsearch->save($params);
		debug($result);
		$this->assertNotEqual($result, false);
		$this->assertCount(1, $result);
		debug($result);
	}

}
