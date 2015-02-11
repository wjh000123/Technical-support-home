<?php

class Configure extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('configure_model');
	}

	public function getSubFunctionUnitTableList()
	{
		$reArray = array();
		$results = $this->configure_model->getAllSubFunctionUnit();
		$extraAttr = '';
		foreach ($results as $r) {
			$actionColumn = '';
			$extraAttr = 'data-systemUnit="'.$r['SystemUnitId'].'|'.$r['SystemUnitName'].'" data-functionUnit="'.$r['FunctionUnitId'].'|'.$r['FunctionUnit'].'" data-subFunctionUnit="'.$r['FunctionUnitDetailId'].'|'.$r['FunctionUnitDetailName'].'"';
			$actionColumn.= "<a class='btn btn-mini del' title='Delete' $extraAttr><i class='icon-trash'></i></a>";
			array_push($reArray, array($r['SystemUnitName'], $r['FunctionUnit'], $r['FunctionUnitDetailName'], $actionColumn));
		}

		echo json_encode(array('aaData'=>$reArray));
	}

	public function getSubCaseTypeTableList()
	{
		$reArray = array();
		$results = $this->configure_model->getAllSubCaseType();
		$extraAttr = '';
		foreach ($results as $r) {
			$actionColumn = '';
			$extraAttr = 'data-casetype="'.$r['CaseTypeId'].'|'.$r['CaseTypeName'].'" data-subcasetype="'.$r['CaseTypeDetailId'].'|'.$r['CaseTypeDetailName'].'"';
			$actionColumn.= "<a class='btn btn-mini del' title='Delete' $extraAttr><i class='icon-trash'></i></a>";
			array_push($reArray, array($r['CaseTypeName'], $r['CaseTypeDetailName'], $actionColumn));
		}

		echo json_encode(array('aaData'=>$reArray));
	}

	public function getCaseFilter()
	{
		$reArray = array();
		$tempArray = array();
		$results = $this->configure_model->getAllCaseType();
		foreach($results as $result)
		{
			if($result['CaseTypeId']!=null&&$result['CaseTypeName']!=null)
				$tempArray[] = array('k'=>$result['CaseTypeId'], 'v'=>$result['CaseTypeName']);
		}
		$reArray['CaseType'] = $tempArray;

		$tempArray = array();
		$results = $this->configure_model->getAllSubCaseType();
		foreach($results as $result)
		{
			if($result['CaseTypeDetailId']!=null&&$result['CaseTypeDetailName']!=null)
				$tempArray[] = array('k'=>$result['CaseTypeDetailId'], 'v'=>$result['CaseTypeDetailName'], 'pk'=>$result['CaseTypeId']);
			//array_push($tempArray, array($result['FunctionUnit'], $result['SystemUnitName']));
		}
		$reArray['SubCaseType'] = $tempArray;

		$tempArray = array();
		$results = $this->configure_model->getAllFunctionUnit();
		foreach($results as $result)
		{
			if($result['FunctionUnitId']!=null&&$result['FunctionUnit']!=null)
				$tempArray[] = array('k'=>$result['FunctionUnitId'], 'v'=>$result['FunctionUnit'], 'pk'=>$result['SystemUnitId']);
			//array_push($tempArray, array($result['FunctionUnit'], $result['SystemUnitName']));
		}
		$reArray['FunctionUnit'] = $tempArray;

		$tempArray = array();
		$results = $this->configure_model->getAllSubFunctionUnit();
		foreach($results as $result)
		{
			if($result['FunctionUnitDetailId']!=null&&$result['FunctionUnitDetailName']!=null)
				$tempArray[] = array('k'=>$result['FunctionUnitDetailId'], 'v'=>$result['FunctionUnitDetailName'], 'pk'=>$result['FunctionUnitId']);
			//array_push($tempArray, array($result['FunctionUnit'], $result['SystemUnitName']));
		}
		$reArray['SubFunctionUnit'] = $tempArray;

		$tempArray = array();
		$results = $this->configure_model->getAllSystemUnit();
		foreach($results as $result)
		{
			if($result['SystemUnitId']!=null&&$result['SystemUnitName']!=null)
				$tempArray[] = array('k'=>$result['SystemUnitId'], 'v'=>$result['SystemUnitName']);
			//array_push($tempArray, $result['SystemUnitName']);
		}
		$reArray['SystemUnit'] = $tempArray;

		echo (json_encode($reArray));
	}

	public function updateCaseFilter($needInsertId = false)
	{
		$postData = json_decode(file_get_contents("php://input"));
		$result = $this->configure_model->updateTable($postData, $needInsertId);
		echo $result;
	}

	public function getHoliday($year, $month=null, $date=null){
		$externalURL = 'http://www.easybots.cn/api/holiday.php';
		
		if($month==null)
		{
			$parameter='?m='.$year.'01';
			for($i=2;$i<=12;$i++)
			{
				$parameter.=','.$year.($i>9?$i:'0'.$i);
			}
		}
		elseif($date==null)
		{
			$parameter='?m='.$year.$month;
		}
		else
		{
			$parameter='?d='.$year.$month.$date;
		}

  
		if(strtolower($_SERVER['REQUEST_METHOD'])=='post') {
			$ch = curl_init($externalURL);
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $_POST );
		}
		else
		{
			$ch = curl_init($externalURL.$parameter);
		}

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );

		list( $header, $contents ) = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ), 2 );

		$status = curl_getinfo( $ch );

		curl_close( $ch );

		echo ($contents);
	}

	public function isHoliday($date){
		$externalURL = 'http://www.easybots.cn/api/holiday.php';

		if(($timestamp = strtotime($date))!==false){
			$year = date('Y', $timestamp);
			$month = date('m', $timestamp);
			$day = date('d', $timestamp);
			$parameter = '?d='.$year.$month.$day;
		}
		else
			return;
  
		if(strtolower($_SERVER['REQUEST_METHOD'])=='post') {
			$ch = curl_init($externalURL);
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $_POST );
		}
		else
		{
			$ch = curl_init($externalURL.$parameter);
		}

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );

		list( $header, $contents ) = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ), 2 );

		$status = curl_getinfo( $ch );

		curl_close( $ch );

		return $contents;
	}
    
    public function cdnServerIPs(){
		$reArray = array();
		$results = $this->configure_model->getAllCDNServersIP();
        foreach($results as $ip){
            array_push($reArray, $ip['ServerIP']);
        }
        
        header('Content-Type: application/json');
		echo json_encode(array('ip'=>$reArray));
	}
    
    public function getCollectedInfo(){
		$postData = json_decode(file_get_contents("php://input"));
		$result = $this->configure_model->insertCollectedConnectionInfo($postData);
        echo $result;
	}
    
    public function track(){
        $_GET_lower = array_change_key_case($_GET, CASE_LOWER);
        $trackURL = $_GET_lower['url'];
        $resolvedInfo = $_GET_lower['resolvedinfo'];
        $resolvedIP = $_GET_lower['resolvedip'];
        $username = $_GET_lower['username'];
        $userIP = $_GET_lower['userip'];
        $latency = $_GET_lower['latency'];
        $lost = $_GET_lower['lost'];
		$result = $this->configure_model->insertCollectedConnectionInfo(array('URLAddress'=>$trackURL, 
                                                                             'ResolvedInfo'=>$resolvedInfo, 
                                                                             'ResolvedIP'=>$resolvedIP, 
                                                                             'UserName'=>$username, 
                                                                             'UserIP'=>$userIP, 
                                                                             'Latency'=>$latency, 
                                                                             'Lost'=>$lost));
        if($result)
            echo 'successful';
        else
            echo 'failed';
	}
}