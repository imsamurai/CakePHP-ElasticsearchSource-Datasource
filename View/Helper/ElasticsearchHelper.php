<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 05.06.2014
 * Time: 14:42:59
 * Format: http://book.cakephp.org/2.0/en/views/helpers.html
 */
App::uses('HtmlHelper', 'View/Helper');

/**
 * ElasticsearchHelper
 * 
 * @package ElasticsearchSource
 * @subpackage View.Helper
 */
class ElasticsearchHelper extends HtmlHelper {

	/**
	 * Make html from array of explainations
	 * 
	 * @param array $explainations
	 * @return string
	 */
	public function explain($explainations) {
		if (count($explainations) === 0) {
			return "There is no explaination.";
		}
		array_walk($explainations, function(&$item) {
			$item['explanation'] = '<ul>' . $this->_explainationToString($item['explanation']) . '</ul>';
		});
		$headers = array_keys($explainations[0]);

		$out = $this->tableHeaders($headers);
		$out .= $this->tableCells($explainations);
		return $out;
	}

	/**
	 * Make html from array of explainations
	 * 
	 * @param array $explaination
	 * @return string
	 */
	protected function _explainationToString($explaination) {
		$out = '<li>' . $explaination['value'] . ' = ' . $explaination['description'];
		$subExplains = array_map(function($explaination) {
			return $this->_explainationToString($explaination);
		}, empty($explaination['details']) ? array() : $explaination['details']);
		$out .= $subExplains ? '<ul>' . implode('', $subExplains) . '</ul>' : '';
		return $out . '</li>';
	}

}
