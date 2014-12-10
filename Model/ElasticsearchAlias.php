<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.12.2014
 * Time: 17:12:36
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('ElasticsearchModel', 'ElasticsearchSource.Model');

/**
 * Aliases
 * 
 * @package ElasticsearchSource
 * @subpackage Model
 */
class ElasticsearchAlias extends ElasticsearchModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'ElasticsearchAlias';

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useTable = 'aliases';

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $primaryKey = 'name';

}
