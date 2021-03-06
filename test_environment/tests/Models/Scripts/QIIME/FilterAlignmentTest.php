<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class FilterAlignmentTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("FilterAlignmentTest");
	}

	private $defaultValue = 1;

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--input_fasta_file" => "",
		"--remove_outliers" => "",
		"--threshold" => "",
		"--lane_mask_fp" => "",
		"--entropy_threshold" => "",
		"--suppress_lane_mask_filter" => "",
		"--allowed_gap_frac" => "",
		"--verbose" => "",
		"--output_dir" => "",
	);
	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}
	public function setUp() {
		$this->object = new \Models\Scripts\QIIME\FilterAlignment($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\FilterAlignment::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "filter_alignment.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\FilterAlignment::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Filter sequence alignment";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	public function testScriptExists() {
		$expecteds = array(
			"script_location" => "/macqiime/QIIME/bin/{$this->object->getScriptName()}",
			"which_return" => "0",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(NULL)
			->getMock();
		$sourceFile = $mockProject->getEnvironmentSource();
		$systemCommand = "source {$sourceFile}; which {$this->object->getScriptName()}; echo $?";

		exec($systemCommand, $output);

		$actuals['script_location'] = $output[0];
		$actuals['which_return'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\QIIME\FilterAlignment::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "filter_alignment";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testInputFastaFile_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;

		$this->object->acceptInput($input);

	}
	public function testInputFastaFile_notPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --input_fasta_file is required</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		unset($input['--input_fasta_file']);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testThreshold_removeOutliersPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		$input['--remove_outliers'] = true;
		$input['--threshold'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testThreshold_removeOutliersNotPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --threshold can only be used when:<br/>&nbsp;- --remove_outliers is set</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--remove_outliers']);
		$input['--threshold'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testSuppressLaneMaskFilter_entropyThresholdPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --suppress_lane_mask_filter cannot be used when:<br/>&nbsp;- --entropy_threshold is set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--lane_mask_fp']);
		$input['--entropy_threshold'] = $this->defaultValue;
		$input['--suppress_lane_mask_filter'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testSuppressLaneMaskFilter_entropyThresholdNotPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--lane_mask_fp']);
		unset($input['--entropy_threshold']);
		$input['--suppress_lane_mask_filter'] = true;

		$this->object->acceptInput($input);

	}

	public function testLaneMaskFp_entropyThresholdPresent_suppressLaneMaskNotPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --lane_mask_fp cannot be used when:<br/>&nbsp;- --entropy_threshold is set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		$input['--entropy_threshold'] = $this->defaultValue;
		unset($input['--suppress_lane_mask_filter']);
		$input['--lane_mask_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testLaneMaskFp_entropyThresholdNotPresent_suppressLaneMaskPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --lane_mask_fp cannot be used when:<br/>&nbsp;- --suppress_lane_mask_filter is set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--entropy_threshold']);
		$input['--suppress_lane_mask_filter'] = true;
		$input['--lane_mask_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testLaneMaskFp_entropyThresholdNotPresent_suppressLaneMaskNotPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--entropy_threshold']);
		unset($input['--suppress_lane_mask_filter']);
		$input['--lane_mask_fp'] = true;

		$this->object->acceptInput($input);

	}
}
