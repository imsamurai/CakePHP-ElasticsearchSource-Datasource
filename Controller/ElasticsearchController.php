<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 05.06.2014
 * Time: 11:25:37
 * Format: http://book.cakephp.org/2.0/en/controllers.html
 */
App::uses('HttpSourceController', 'HttpSource.Controller');

/**
 * ElasticsearchController
 * 
 * @property ElasticsearchDocument $ElasticsearchDocument ElasticsearchDocument Model
 * 
 * @package ElasticsearchSource
 * @subpackage Controller
 */
class ElasticsearchController extends HttpSourceController {

	/**
	 * {@inheritdoc}
	 *
	 * @var array 
	 */
	public $uses = array('ElasticsearchSource.ElasticsearchDocument');

	/**
	 * {@inheritdoc}
	 *
	 * @var array 
	 */
	public $helpers = array('ElasticsearchSource.Elasticsearch');

	/**
	 * {@inheritdoc}
	 *
	 * @throws BadRequestException
	 */
	public function explain() {
		$this->_checkRequest();
		$result = $this->ElasticsearchDocument->explainQuery($this->request->data['log']['ds'], $this->request->data['log']['sql']);
		$this->set(compact('result'));
	}

}
