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

}
