<?php
/**
 * Test class for the InListRule
 */
namespace App\Test\TestCase\Model\Rule;

use App\Model\Rule\InListRule;
use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\ORM\Entity as User;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * \App\Test\TestCase\Model\Rule\TestInConfigureListRule
 *
 * Class to override protected properties for InConfigureListRule
 */
class TestInListRule extends InListRule {
	public $field;
}

/**
 * \App\Test\TestCase\Model\Rule\InListRuleTest
 */
class InListRuleTest extends TestCase {
	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		//'app.users',
	];

	/**
	 * Store the name of the field to operate on in tests.
	 *
	 * @var string
	 */
	public $field = 'role';

	/**
	 * Data to load into Configure for testing.
	 *
	 * @var string
	 */
	public $configData = [
			'Users' => [
				'role' => [
					'student' => 'Student',
					'parent' => 'Guardian',
					'admin' => 'Administrator',
				],
			],
	];

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->Rule = new TestInListRule($this->field);
		$this->User = new User([], ['source' => 'Users']);
		Configure::write('Lists', $this->configData);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		Configure::delete('Lists');
		unset($this->Rule);
		unset($this->User);

		parent::tearDown();
	}

	/**
	 * test the __construct method
	 *
	 * @return void
	 */
	public function testConstruct() {
		$this->assertEquals(
			$this->field,
			$this->Rule->field,
			'The field value of the property should be equal to the passed in construct argument.'
		);
	}

	/**
	 * test the __invoke method on no repository set.
	 *
	 * @return void
	 */
	public function testInvokeOnNoRepositoryPassed() {
		$this->assertFalse(
			$this->Rule->__invoke(new User(), []),
			'When no Entity::source() is available, return false'
		);
	}

	/**
	 * test the __invoke method
	 *
	 * @param string $field The field name to test.
	 * @param array $config The config params for InConfigureListRule.
	 * @param bool $dirty Whether the field should be marked as dirty or not.
	 * @param string $value The value of the field to check against.
	 * @param bool $expected The expected output from __invoke.
	 * @param string $msg The PHPUnit error message.
	 * @return void
	 * @dataProvider providerInvoke
	 */
	public function testInvoke($field, $config, $dirty, $value, $expected, $msg = '') {
		$options = [
			'repository' => $this->getMockBuilder('Cake\ORM\Table')
				->setMethods([])
				->setConstructorArgs([
					['alias' => 'Users'], // 1st constructor arg
				])
				->getMock(),
		];

		$this->Rule = new InListRule($field, $config);
		$this->User->{$this->field} = $value;
		$this->User->dirty($this->field, $dirty);

		$this->assertEquals(
			$expected,
			$this->Rule->__invoke($this->User, $options),
			$msg
		);
	}

	/**
	 * DataProvider for testInvoke.
	 *
	 * @return array Data inputs for testInvoke.
	 */
	public function providerInvoke() {
		return [
			'Role that is available in Config' => [
				$this->field,
				[],
				false,
				'admin',
				true,
				'On a valid role, should return true.',
			],
			'Empty String role value' => [
				$this->field,
				[],
				false,
				'',
				false,
				'On an empty role value, should return false.',
			],
			'Role that is not available in Config' => [
				$this->field,
				[],
				false,
				'not-real-value',
				false,
				'One a role that is not available in Config, should return false.',
			],
			'Field that is not available in Config' => [
				'not-real-field',
				[],
				false,
				'not-real-value',
				false,
				'One a field that is not available in Config, should return false.',
			],
			'Custom Path that is not available in Config' => [
				$this->field,
				['configPath' => 'Custom.path.something'],
				false,
				'admin',
				false,
				'On an invalid path, should return false.',
			],
			'Allow null and pass null' => [
				$this->field,
				['allowNulls' => true],
				false,
				null,
				true,
				'On allowing nulls and setting null, should return true.',
			],
			'Disallow null and pass null' => [
				$this->field,
				[],
				false,
				null,
				false,
				'On disallowing nulls and setting null, should return false.',
			],
			'Check only dirty enabled, a valid & clean field/value should return true.' => [
				$this->field,
				['checkOnlyIfDirty' => true],
				false,
				'admin',
				true,
				'When checkOnlyIfDirty is enabled, a valid field+value that is marked dirty should return true.',
			],
			'On the Rule set to checkOnlyIfDirty and the field set to be dirty, should return true on a real value.' => [
				$this->field,
				['checkOnlyIfDirty' => true],
				true,
				'admin',
				true,
				'When checkOnlyIfDirty is enabled, a valid field+value that is marked dirty should return true.',
			],
			'On the Rule set to checkOnlyIfDirty and the field set to be dirty, should return false on a not real value.' => [
				$this->field,
				['checkOnlyIfDirty' => true],
				true,
				'not-valid-value',
				false,
				'When checkOnlyIfDirty is enabled, an invalid field+value that is marked dirty should return false.',
			],
		];
	}
}
