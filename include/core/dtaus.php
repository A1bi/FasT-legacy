<?php

class DTAUS {
	private $transactionType, $sender, $reference, $data = "",
			$sums = array(),
			$transactions = array();
			
	public function __construct($transactionType, $sender, $reference) {
		$this->transactionType = $transactionType;
		$this->sender = $sender;
		$this->reference = $reference;
	}
	
	public function generateData() {
		// reset data and sums
		$this->data = "";
		$sums = array("blz" => 0, "account" => 0, "total" => 0);
		
		$this->generatePartA();
		$this->generatePartC();
		$this->generatePartE();
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getSums() {
		return $this->sums;
	}
	
	private function generatePartA() {
		$this->addPartIntro("A", 128);
		
		// type of transaction
		$this->addData($this->transactionType);
		
		// sender info
		$this->fillWithZeros(8, $this->sender['blz']);
		$this->fillWithZeros(8);
		$this->fillWithSpaces(27, $this->sender['shortname']);
		
		$this->addData(date("dmy"));
		$this->fillWithSpaces(4);
		
		// additional info
		$this->fillWithZeros(10, $this->sender['account']);
		$this->fillWithZeros(10, $this->reference);
		$this->fillWithSpaces(15);
		
		// due date
		$this->fillWithSpaces(8);
		$this->fillWithSpaces(24);
		
		// currency
		$this->addData(1);
	}
	
	private function generatePartC() {
		foreach ($this->transactions as $transaction) {
			// look if we have to split names because they are too long
			$senderName = $this->splitName($this->sender['name'], 27, 1);
			$rcpName = $this->splitName($transaction->recipient['name'], 27, 1);
			
			
			// gather additions
			$additions = array();
			
			// recipient name
			for ($i = 1; $i <= $rcpName['add']; $i++) {
				$additions[] = array("type" => 1, "string" => $rcpName['strings'][$i]);
			}
			
			// references
			// skip the first reference because it is added outside the additions
			$tmpReferences = $transaction->references;
			unset($tmpReferences[0]);
			
			foreach ($tmpReferences as $reference) {
				$additions[] = array("type" => 2, "string" => $reference);
			}
			
			// sender name
			for ($i = 1; $i <= $senderName['add']; $i++) {
				$additions[] = array("type" => 3, "string" => $senderName['strings'][$i]);
			}
			
			$additionalSections = count($additions);
			
		
			$this->addPartIntro("C", 187 + $additionalSections * 29);
			
			// recipient info
			$this->fillWithZeros(8, $this->sender['blz']);
			$this->addToSum($transaction->recipient['blz'], "blz");
			$this->fillWithZeros(8, $transaction->recipient['blz']);
			$this->addToSum($transaction->recipient['account'], "account");
			$this->fillWithZeros(10, $transaction->recipient['account']);
			$this->fillWithZeros(13);
			
			// transaction type
			$this->addData($transaction->getTransactionKey());
			$this->fillWithSpaces(1);
			$this->fillWithZeros(11);
			
			// sender info
			$this->fillWithZeros(8, $this->sender['blz']);
			$this->fillWithZeros(10, $this->sender['account']);
			
			// total
			$total = $transaction->total * 100;
			$this->addToSum($total, "total");
			$this->fillWithZeros(11, $total);
			$this->fillWithSpaces(3);
			
			// recipient info
			$this->fillWithSpaces(27, $rcpName['strings'][0]);
			$this->fillWithSpaces(8);
			
			// sender info
			$this->fillWithSpaces(27, $senderName['strings'][0]);
			
			// reference
			$this->fillWithSpaces(27, $transaction->references[0]);
			$this->addData(1);
			$this->fillWithSpaces(2);
			
			
			// additional references
			// number of additions
			$this->fillWithZeros(2, $additionalSections);
			
			// additions
			$pos = 0;
			// start with set 2 of 6
			for ($i = 2; $i <= 6; $i++) {
				// how many spaces to add after set
				$spaces = 12;
				if ($i == 2) {
					// max 2 additions in set 2
					$max = 2;
					$spaces = 11;
				} else if ($i < 6) {
					// max 4 additions in sets 3-5
					$max = 4;
				} else {
					// max 1 addition in set 6
					$max = 1;
				}
				
				for ($n = 0; $n < $max; $n++) {
					// no more additions ?
					if ($pos >= $additionalSections) {
						// fill up the remaining additions in this set with spaces
						$this->fillWithSpaces(29);
						continue;
					}
					
					// add the actual addition
					$this->addAddition($additions[$pos]);
					$pos++;
				}
				
				// spaces which conclude the set
				$this->fillWithSpaces($spaces);
				
				// no more additions - no more sets
				if ($pos >= $additionalSections) break;
			}
		}
	}
	
	private function generatePartE() {
		$this->addPartIntro("E", 128);
		$this->fillWithSpaces(5);
		
		// number of C parts (transactions)
		$this->fillWithZeros(7, count($this->transactions));
		
		// sums
		$this->fillWithZeros(13);
		$this->fillWithZeros(17, $this->sums['account']);
		$this->fillWithZeros(17, $this->sums['blz']);
		$this->fillWithZeros(13, $this->sums['total']);
		
		$this->fillWithSpaces(51);
	}
	
	private function addAddition($addition) {
		$this->addData("0".$addition['type']);
		$this->fillWithSpaces(27, $addition['string']);
	}
	
	private function splitName($name, $chars, $maxAdd) {
		$var = array("strings" => str_split($name, $chars));
		$var['add'] = min(count($var['strings'])-1, $maxAdd);
		
		return $var;
	}
	
	private function prepareText($text) {
		$text = strtoupper($text);
		
		// replace umlaute
		$chars = array("Ä", "Ö", "Ü", "ß");
		$replacements = array("AE", "OE", "UE", "SS");
		$text = str_replace($chars, $replacements, $text);
		$chars = array("ä", "ö", "ü");
		$text = str_replace($chars, $replacements, $text);
		
		// remove other unsopprted characters
		return preg_replace("#[^A-Z0-9.,&-/+*$% ]#", "", $text);
	}
	
	private function addPartIntro($part, $length) {
		// part length
		$this->fillWithZeros(4, $length);
		
		// part identifier
		$this->addData($part);
	}
	
	private function fillWithCharacter($char, $length) {
		for ($i = 0; $i < $length; $i++) {
			$this->addData($char);
		}
	}
	
	private function fillDataWithCharacter($char, $length, $data) {
		$this->fillWithCharacter($char, $length - strlen($data));
	}
	
	private function fillWithZeros($length, $data = "") {
		$this->fillDataWithCharacter("0", $length, $data);
		$this->addDataWithMaxLength($length, $data);
	}
	
	private function fillWithSpaces($length, $data = "") {
		if (!empty($data)) {
			$data = $this->prepareText($data);
		}
		$this->addDataWithMaxLength($length, $data);
		$this->fillDataWithCharacter(" ", $length, $data);
	}
	
	private function addData($data) {
		$this->data .= $data;
	}
	
	private function addDataWithMaxLength($length, $data) {
		$this->addData(substr($data, 0, $length));
	}
	
	private function addToSum($number, $type) {
		$this->sums[$type] += $number;
	}

	public function addTransaction($transaction) {
		$this->transactions[] = $transaction;
	}
}

class Transaction {
	public $recipient, $type, $total, $references = array();
	
	static $types = array("charge" => "05000");
	
	public function __construct($type, $total, $recipient, $references) {
		$this->type = $type;
		$this->total = $total;
		$this->recipient = $recipient;
		$this->references = $references;
	}
	
	public function getTransactionKey() {
		return self::$types[$this->type];
	}
}

?>