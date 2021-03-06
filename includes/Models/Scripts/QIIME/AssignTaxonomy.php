<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\DefaultScript;
use Models\Scripts\Parameters\VersionParameter;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\TextArgumentParameter;
use Models\Scripts\Parameters\TrueFalseParameter;
use Models\Scripts\Parameters\TrueFalseInvertedParameter;
use Models\Scripts\Parameters\NewFileParameter;
use Models\Scripts\Parameters\OldFileParameter;
use Models\Scripts\Parameters\ChoiceParameter;
use Models\Scripts\Parameters\Label;

class AssignTaxonomy extends DefaultScript {
	public function getScriptName() {
		return "assign_taxonomy.py";
	}
	public function getScriptTitle() {
		return "Assign taxonomies";
	}
	public function getHtmlId() {
		return "assign_taxonomy";
	}

	public function getInitialParameters() {
		$parameters = parent::getInitialParameters();

		$inputFastaFp = new OldFileParameter("--input_fasta_fp", $this->project);
		$inputFastaFp->requireIf();

		$assignmentMethod = new ChoiceParameter("--assignment_method", "uclust", 
			array("rdp", "blast", "rtax", "mothur", "tax2tree", "uclust"));
		$read1SeqsFp = new OldFileParameter("--read_1_seqs_fp", $this->project);
		$read2SeqsFp = new OldFileParameter("--read_2_seqs_fp", $this->project);
		$singleOk = new TrueFalseParameter("--single_ok");
		$noSingleOkGeneric = new TrueFalseParameter("--no_single_ok_generic");
		$readIdRegex = new TextArgumentParameter("--read_id_regex", "\\S+\\s+(\\S+)", TextArgumentParameter::PATTERN_ANYTHING_GOES);
		$ampliconIdRegex = new TextArgumentParameter("--amplicon_id_regex", "(\\S+)\\s+(\\S+?)\\/", TextArgumentParameter::PATTERN_ANYTHING_GOES);
		$headerIdRegex = new TextArgumentParameter("--header_id_regex", "\S+\s+(\S+?)\/", TextArgumentParameter::PATTERN_ANYTHING_GOES); 
		$confidence = new TextArgumentParameter("--confidence", "0.8", TextArgumentParameter::PATTERN_PROPORTION);
		$rdpMaxMemory = new TextArgumentParameter("--rdp_max_memory", "4000", TextArgumentParameter::PATTERN_DIGIT);
		$uclustMinConsensusFraction = new TextArgumentParameter("--uclust_min_consensus_fraction", "0.51", TextArgumentParameter::PATTERN_PROPORTION);
		$uclustSimilarity = new TextArgumentParameter("--uclust_similarity", "0.9", TextArgumentParameter::PATTERN_PROPORTION);
		$uclustMaxAccepts = new TextArgumentParameter("--uclust_max_accepts", "3", TextArgumentParameter::PATTERN_DIGIT); 

		$read1SeqsFp->excludeButAllowIf($assignmentMethod, "rtax");
		$read2SeqsFp->excludeButAllowIf($assignmentMethod, "rtax");
		$singleOk->excludeButAllowIf($assignmentMethod, "rtax");
		$noSingleOkGeneric->excludeButAllowIf($assignmentMethod, "rtax");
		$readIdRegex->excludeButAllowIf($assignmentMethod, "rtax");
		$ampliconIdRegex->excludeButAllowIf($assignmentMethod, "rtax");
		$headerIdRegex->excludeButAllowIf($assignmentMethod, "rtax");
		$confidence->excludeButAllowIf($assignmentMethod, "mothur");
		$confidence->excludeButAllowIf($assignmentMethod, "rdp");
		$rdpMaxMemory->excludeButAllowIf($assignmentMethod, "rdp");
		$uclustMinConsensusFraction->excludeButAllowIf($assignmentMethod, "uclust");
		$uclustSimilarity->excludeButAllowIf($assignmentMethod, "uclust");
		$uclustMaxAccepts->excludeButAllowIf($assignmentMethod, "uclust");

		$idToTaxonomyFp = new OldFileParameter("--id_to_taxonomy_fp", $this->project,
			'/macqiime/greengenes/gg_13_8_otus/taxonomy/97_otu_taxonomy.txt'); 
		$treeFp = new OldFileParameter("--tree_fp", $this->project);
		$referenceSeqsFp = new OldFileParameter("--reference_seqs_fp", $this->project,
			'/macqiime/greengenes/gg_13_8_otus/rep_set/97_otus.fasta'); 
		$blastDb = new OldFileParameter("--blast_db", $this->project);
		$eitherBlastDatabase = $referenceSeqsFp->linkTo($blastDb);

		$idToTaxonomyFp->requireIf($assignmentMethod, "blast");
		$eitherBlastDatabase->requireIf($assignmentMethod, "blast");
		$treeFp->requireIf($assignmentMethod, "tax2tree");
		$treeFp->excludeButAllowIf($assignmentMethod, "tax2tree");

		array_push($parameters,
			new Label("Required Parameters"),
			$inputFastaFp,

			new Label("Optional Parameters"),
			$assignmentMethod,
			$read1SeqsFp,
			$read2SeqsFp,
			$singleOk,
			$noSingleOkGeneric,
			$readIdRegex,
			$ampliconIdRegex,
			$headerIdRegex,
			$confidence,
			$rdpMaxMemory,
			$uclustMinConsensusFraction,
			$uclustSimilarity,
			$uclustMaxAccepts,
			$idToTaxonomyFp,
			$eitherBlastDatabase,
			$treeFp,
			new TextArgumentParameter("--e_value", "0.001", TextArgumentParameter::PATTERN_NUMBER),
			new OldFileParameter("--training_data_properties_fp", $this->project),

			new Label("Output Options"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", "_assigned_taxonomy", $isDir = true)
			);
		return $parameters;
	}
}
