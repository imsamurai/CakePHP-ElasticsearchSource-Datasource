<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Nov 18, 2013
 * Time: 12:58:16 PM
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('HttpSourceModel', 'HttpSource.Model');

/**
 * ElasticsearchModel Model
 * 
 * @package ElasticsearchSource
 * @subpackage Indices
 */
class ElasticsearchModel extends HttpSourceModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'ElasticsearchModel';

	/**
	 * Elasticsearch index
	 *
	 * @var string
	 */
	public $useIndex;

	/**
	 * Elasticsearch index type
	 *
	 * @var string 
	 */
	public $useType;

	/**
	 * {@inheritdoc}
	 * 
	 * @param string $tableName
	 * @param string $indexName
	 * @param string $typeName
	 * @throws MissingTableException when database table $tableName is not found on data source
	 */
	public function setSource($tableName, $indexName = null, $typeName = null) {
		$this->useIndex = $indexName;
		$this->useType = $typeName;
		parent::setSource($tableName);
	}

}
