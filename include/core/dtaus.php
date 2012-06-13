<?php

class DTAUS {
	private $transactionType, $sender, $reference, $data,
			$totalSum, $accountSum, $blzSum,
			$transactions = array();
			
	public function __construct($transactionType, $sender, $reference) {
		$this->transactionType = $transactionType;
		$this->sender = $sender;
		$this->reference = $reference;
	}
	
	private function generateData() {
		$this->data = "";
		
		$this->generatePartA();
		$this->generatePartC();
		$this->generatePartE();
	}
	
	public function getData() {
		$this->generateData();
		
		return $this->data;
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
			
			$referencesNumber = count($transaction->references)-1;
			// only allow one addition for names
			$additionalSections = $referencesNumber + $senderName['add'] + $rcpName['add'];
			
		
			$this->addPartIntro("C", 187 + $additionalSections * 29);
			
			// recipient info
			$this->fillWithZeros(8, $this->sender['blz']);
			$this->blzSum += $transaction->recipient['blz'];
			$this->fillWithZeros(8, $transaction->recipient['blz']);
			$this->accountSum += $transaction->recipient['account'];
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
			$this->totalSum += $total;
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
			
			// names
			$this->addAddition(1, $rcpName['strings'], $rcpName['add']);
			$this->addAddition(3, $senderName['strings'], $senderName['add']);
			
			// references
			$this->addAddition(2, $transaction->references, $referencesNumber);
			
			$this->fillWithSpaces(11);
			// fix
			$this->fillWithSpaces(29);
		}
	}
	
	private function generatePartE() {
		$this->addPartIntro("E", 128);
		$this->fillWithSpaces(5);
		
		// number of C parts (transactions)
		$this->fillWithZeros(7, count($this->transactions));
		
		// sums
		$this->fillWithZeros(13);
		$this->fillWithZeros(17, $this->accountSum);
		$this->fillWithZeros(17, $this->blzSum);
		$this->fillWithZeros(13, $this->totalSum);
		
		$this->fillWithSpaces(51);
	}
	
	private function addAddition($type, $objects, $number) {
		for ($i = 1; $i <= $number; $i++) {
			$this->addData("0".$type);
			$this->fillWithSpaces(27, $objects[$i]);
		}
	}
	
	private function splitName($name, $chars, $maxAdd) {
		$var = array("strings" => str_split($name, $chars));
		$var['add'] = min(count($var['strings'])-1, $maxAdd);
		
		return $var;
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
		$this->addData($data);
	}
	
	private function fillWithSpaces($length, $data = "") {
		$this->addData($data);
		$this->fillDataWithCharacter(" ", $length, $data);
	}
	
	private function addData($data) {
		$this->data .= strtoupper($data);
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