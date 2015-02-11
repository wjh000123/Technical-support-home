<?php
require_once(APPPATH.'nusoapclient/salesforce.php');

class Salesforce_model extends CI_Model {
	private $mySforceConnection;

	public function __construct()
	{
		$this->mySforceConnection = new salesforce(APPPATH."nusoapclient/enterprise.wsdl.xml");
		//$this->mySforceConnection->createConnection(APPPATH."soapclient/enterprise.wsdl.xml");
		$this->mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);
	}

	public function getSFCase($utcStartDate, $utcEndDate, $ownerGroup, $needDetail)
	{
		$ownerPart = '';
		$extraQuery = '';
		foreach($ownerGroup as $owner)
		{
			if($ownerPart=='')
			{
				$ownerPart.='(';
				$ownerPart.="OwnerId = '".$owner['SalesforceId']."'";
			}
			else
				$ownerPart.=" OR OwnerId = '".$owner['SalesforceId']."'";
		}
		$ownerPart.=')';
		if($needDetail)
			$extraQuery = ', Subject, Description';
		$sql = "SELECT Id, CaseNumber, Status, Owner.Name, CreatedDate, CreatedBy.Name, ClosedDate".$extraQuery." from Case where $ownerPart and CreatedDate >= $utcStartDate and CreatedDate < $utcEndDate and RecordTypeId = '012400000009bCbAAI' order by CaseNumber";
		$result = $this->mySforceConnection->query($sql);


		return $result;
	}
}