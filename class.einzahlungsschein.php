<?php

/* ------------------------------------------------------------------------ 
 * class.einzahlungsschein.php
 * A class to create Swiss payment slips with ESR number in pdf format.
 * Original Code
 *
 * See https://github.com/sprain/class.Einzahlungsschein.php
 * ------------------------------------------------------------------------ 
*/

class Einzahlungsschein {

	//values on payment slip
	public $ezs_bankName = '';
	public $ezs_bankCity = '';
	public $ezs_bankingAccount = '';
	
	public $ezs_recipientName    = '';
	public $ezs_recipientAddress = '';
	public $ezs_recipientCity    = '';
	public $ezs_bankingCustomerIdentification = '';
	
	public $ezs_payerLine1		  = '';
	public $ezs_payerLine2       = '';
	public $ezs_payerLine3       = '';
	public $ezs_payerLine4       = '';
	
	public $ezs_referenceNumber = '';
	public $ezs_amount = 0;

    public function __construct(){}

    /**
     * Set name, address and banking account of bank
     * @param string $bankName
     * @param string $bankCity
     * @param string $bankingAccount
     * @return bool
     */
    public function setBankData($bankName, $bankCity, $bankingAccount){
        $this->ezs_bankName = utf8_decode($bankName);
        $this->ezs_bankCity = utf8_decode($bankCity);
        $this->ezs_bankingAccount = $bankingAccount;
        return true;
    }

    /**
    * Set name and address of recipient of money (= you, I guess)
    * @param string $recipientName
    * @param string $recipientAddress
    * @param string $recipientCity
    * @param int    $bankingCustomerIdentification
    * @return bool
    */
    public function setRecipientData($recipientName, $recipientAddress, $recipientCity, $bankingCustomerIdentification){
        $this->ezs_recipientName    = $recipientName;
        $this->ezs_recipientAddress = $recipientAddress;
        $this->ezs_recipientCity    = $recipientCity;
        $this->ezs_bankingCustomerIdentification = $bankingCustomerIdentification;
        return true;
    }


    /**
    * Set name and address of payer (very flexible four lines of text)
    * @param string $payerLine1
    * @param string $payerLine2
    * @param string $payerLine3
    * @param string $payerLine4
    * @return bool
    */
    public function setPayerData($payerLine1, $payerLine2, $payerLine3='', $payerLine4=''){
        $this->ezs_payerLine1 = $payerLine1;
        $this->ezs_payerLine2 = $payerLine2;
        $this->ezs_payerLine3 = $payerLine3;
        $this->ezs_payerLine4 = $payerLine4;
        return true;
    }


    /**
     * Set payment data
     * @param float $amount
     * @param int   $referenceNumber (
     * @return bool
     */
    public function setPaymentData($amount, $referenceNumber=null){
        $this->ezs_amount 		   = sprintf("%01.2f",$amount);
        $this->ezs_referenceNumber = $referenceNumber;
        return true;
    }


    /**
     * Creates bottom line string
     * @return string
     */
    public function createBottomLineString() {

        //start it, baby!
        $bottomLineString = "";

        //EZS with amount or not?
        if($this->ezs_amount == 0){
            $bottomLineString .= "042>";
        }else{
            $amountParts = explode(".", $this->ezs_amount);
            $bottomLineString .= "01";
            $bottomLineString .= str_pad($amountParts[0], 8 ,'0', STR_PAD_LEFT);
            $bottomLineString .= str_pad($amountParts[1], 2 ,'0', STR_PAD_RIGHT);
            $bottomLineString .= $this->modulo10($bottomLineString);
            $bottomLineString .= ">";
        }//if

        //add reference number
        $bottomLineString .= $this->createCompleteReferenceNumber();
        $bottomLineString .= "+ ";

        //add banking account
        $bankingAccountParts = explode("-", $this->ezs_bankingAccount);
        $bottomLineString .= str_pad($bankingAccountParts[0], 2 ,'0', STR_PAD_LEFT);
        $bottomLineString .= str_pad($bankingAccountParts[1], 6 ,'0', STR_PAD_LEFT);
        $bottomLineString .= str_pad($bankingAccountParts[2], 1 ,'0', STR_PAD_LEFT);
        $bottomLineString .= ">";

        //done!
        return $bottomLineString;

    }//function

    /**
    * Creates Modulo10 recursive check digit
    *
    * as found on http://www.developers-guide.net/forums/5431,modulo10-rekursiv
    * (thanks, dude!)
    *
    * @param string $number
    * @return int
    */
    private function modulo10($number) {
        $table = array(0,9,4,6,8,2,7,1,3,5);
        $next = 0;
        for ($i=0; $i<strlen($number); $i++) {
            $next = $table[($next + substr($number, $i, 1)) % 10];
        }//for
        return (10 - $next) % 10;
    }



    /**
    * Creates complete reference number
    * @return string
    */
    public function createCompleteReferenceNumber() {

        //get reference number and fill with zeros
        $completeReferenceNumber = str_pad($this->ezs_referenceNumber, (26 - strlen($this->ezs_bankingCustomerIdentification)) ,'0', STR_PAD_LEFT);

        //add customer identification code
        $completeReferenceNumber = $this->ezs_bankingCustomerIdentification.$completeReferenceNumber;

        //add check digit
        $completeReferenceNumber .= $this->modulo10($completeReferenceNumber);

        //return
        return $completeReferenceNumber;
    }

}