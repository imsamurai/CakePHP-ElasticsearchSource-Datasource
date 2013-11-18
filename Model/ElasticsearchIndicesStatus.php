<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 18.10.2013
 * Time: 15:54:36
 * Format: http://book.cakephp.org/2.0/en/models.html
 *
 * @package ElasticsearchSource
 * @subpackage Indices
 */
App::uses('ElasticsearchModel', 'ElasticsearchSource.Model');

/**
 * Indices Status
 *
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/indices-status.html
 * @package ElasticsearchSource
 * @subpackage Indices
 */
class ElasticsearchIndicesStatus extends ElasticsearchModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'ElasticsearchIndicesStatus';

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useTable = 'indices_status';

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $primaryKey = 'name';

}
