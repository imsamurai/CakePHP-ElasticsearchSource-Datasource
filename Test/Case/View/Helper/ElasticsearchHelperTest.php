<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 03.09.2014
 * Time: 17:22:38
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('View', 'View');
App::uses('ElasticsearchHelper', 'ElasticsearchSource.View/Helper');

/**
 * ElasticsearchHelperTest
 * 
 * @property ElasticsearchHelper $Helper Elasticsearch Helper
 * 
 * @package ElasticsearchSourceTest
 * @subpackage View.Helper
 */
class ElasticsearchHelperTest extends CakeTestCase {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->Helper = new ElasticsearchHelper(new View);
	}

	/**
	 * Test explain()
	 * 
	 * @param array $explainations
	 * @param string $result
	 * 
	 * @dataProvider explainProvider
	 */
	public function testExplain($explainations, $result) {
		$this->assertSame($result, $this->Helper->explain($explainations));
	}

	/**
	 * Data provider for testExplain
	 * 
	 * @return array
	 */
	public function explainProvider() {
		return array(
			//set #0
			array(
				//explainations
				array(),
				//result
				'There is no explaination.'
			),
			//set #1
			array(
				//explainations
				array(
					(int)0 => array(
						'id' => '16414363201132797699-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)1 => array(
						'id' => '14960097206220799364-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)2 => array(
						'id' => '15148147060363339322-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)3 => array(
						'id' => '13751095702815676354-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)4 => array(
						'id' => '16416299197515024972-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)5 => array(
						'id' => '10333881030353604074-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)6 => array(
						'id' => '9241146738202816496-14-07',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)7 => array(
						'id' => '11566597173585665706-14-07',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)8 => array(
						'id' => '12769635326146617259-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)9 => array(
						'id' => '10837928390142812679-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(cache(_type:document)), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					)
				),
				//result
				'<tr><th>id</th> <th>explanation</th></tr><tr><td>16414363201132797699-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>14960097206220799364-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>15148147060363339322-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>13751095702815676354-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>16416299197515024972-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>10333881030353604074-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>9241146738202816496-14-07</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>11566597173585665706-14-07</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>12769635326146617259-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>10837928390142812679-14-03</td> <td><ul><li>1 = ConstantScore(cache(_type:document)), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>'
			),
			//set #2
			array(
				//explainations
				array(
					(int)0 => array(
						'id' => '16414363201132797699-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)1 => array(
						'id' => '10333881030353604074-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)2 => array(
						'id' => '13768516356988032155-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)3 => array(
						'id' => '9808364690265838034-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)4 => array(
						'id' => '10259577079084565417-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)5 => array(
						'id' => '17355610899758906552-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)6 => array(
						'id' => '9857817655664713291-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)7 => array(
						'id' => '10569334701470008491-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)8 => array(
						'id' => '14051313325606859217-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					),
					(int)9 => array(
						'id' => '12595943569711630576-14-03',
						'explanation' => array(
							'value' => (float)1,
							'description' => 'ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:',
							'details' => array(
								(int)0 => array(
									'value' => (float)1,
									'description' => 'boost'
								),
								(int)1 => array(
									'value' => (float)1,
									'description' => 'queryNorm'
								)
							)
						)
					)
				),
				//result
				'<tr><th>id</th> <th>explanation</th></tr><tr><td>16414363201132797699-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>10333881030353604074-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>13768516356988032155-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>9808364690265838034-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>10259577079084565417-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>17355610899758906552-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>9857817655664713291-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>10569334701470008491-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>14051313325606859217-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>
<tr><td>12595943569711630576-14-03</td> <td><ul><li>1 = ConstantScore(BooleanFilter(++cache(labels:Industry))), product of:<ul><li>1 = boost</li><li>1 = queryNorm</li></ul></li></ul></td></tr>'
			),
		);
	}

}
