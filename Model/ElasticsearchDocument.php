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
App::uses('HttpSourceModel', 'HttpSource.Model');

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
class ElasticsearchDocument extends HttpSourceModel {

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
	 * @var string
	 */
	public $useDbConfig = 'elasticsearch';

}
