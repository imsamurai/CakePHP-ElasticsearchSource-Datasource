<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2013
 * Time: 18:19:36
 * Format: http://book.cakephp.org/2.0/en/models.html
 *
 * @package ElasticsearchSource
 * @subpackage Document
 */
App::uses('ElasticsearchModel', 'ElasticsearchSource.Model');

/**
 * Indices Status
 *
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-get.html
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search.html
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-index_.html
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-delete.html
 * @package ElasticsearchSource
 * @subpackage Indices
 */
class ElasticsearchDocument extends ElasticsearchModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'ElasticsearchDocument';

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useTable = 'document';
	
	/**
	 * {@inheritdoc}
	 * 
	 * @param array $queryData
	 * @return array
	 */
	public function beforeFind($queryData) {
		$queryData = parent::beforeFind($queryData);
		$this->_mapMultipleIds($queryData);

		return $queryData;
	}
	
	/**
	 * {@inheritdoc}
	 * 
	 * @param mixed $id
	 * @param array $conditions
	 * @return bool
	 */
	public function exists($id = null, array $conditions = array()) {
		$ex = parent::exists($id, array(
					'index' => $this->useIndex,
					'type' => $this->useType,
		));
		return $ex;
	}

	/**
	 * Put ids in right place in case of multiple ids
	 * 
	 * @param array $queryData
	 */
	protected function _mapMultipleIds(array &$queryData) {
		$idsKeys = array(
			$this->primaryKey,
			"{$this->alias}.{$this->primaryKey}"
		);

		foreach ($idsKeys as $idsKey) {
			if (!isset($queryData['conditions'][$idsKey])) {
				continue;
			}
			
			$queryData['conditions'] = Hash::insert($queryData['conditions'], 'query.ids.values', (array)$queryData['conditions'][$idsKey]);
			$queryData['limit'] = count($queryData['conditions'][$idsKey]);
			unset($queryData['conditions'][$idsKey]);
		}
	}

}
