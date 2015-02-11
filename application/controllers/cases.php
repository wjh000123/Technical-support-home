<?php
define('SFRootUrl', 'https://ap1.salesforce.com/');

class Cases extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
		$this->load->model('cases_model');
		$this->load->model('configure_model');
	}

	public function index()
	{
		/*
		if($year > 2000 & $year < 9999)
		{
			$cases = $this->cases_model->get_cases();
			$outputStr = '';
			$time = 0;
			for($mon = 1; $mon <= 12; $mon++)
			{
				$time = strtotime($year.'-'.$mon.'-1')*1000;
				$caseAmount = 0;
				foreach($cases as $case)
				{
					if(date('n', strtotime($case['ReportDate']))==$mon)
					{
						$caseAmount = $case['Amount'];
						break;
					}
				}
				$outputStr.=$time.','.$caseAmount.';';
			}
		}

		$data['caseData'] = $outputStr;
		$data['cases'] = $this->cases_model->get_cases();*/
		$data['title'] = 'Cases';
		$data['navigator'] = 'Overview';
		$this->load->view('templates/header', $data);
		$this->load->view('cases/templates/share', $data);
		$this->load->view('cases/index', $data);
		$this->load->view('templates/footer', $data);
	}

	public function test()
	{
		$this->load->model('salesforce_model');
		$results = $this->cases_model->getMemberinfo();
		
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('2013-07-01')));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime('2013-07-01'))));
		//var_dump($this->salesforce_model->getSFCase($startDate, $endDate, $results));
	}
	/*
	public function manage()
	{
		$year = 2013;
		$month = 7;
		$array1= array(array('a'=>'test1'), array('b'=>'test2', 'a'=>'test21'), array('c'=>'test3'));
		$array2 = array('test111', 'test2', 'test333');

		foreach($array1 as $a)
		{
			unset($a['a']);
		}
		$data['test'] = mdate('%Y-%m-%d %H:%i:%s', strtotime('+1 month', strtotime('2012-01-01')));

		$data['title'] = 'Cases';
		$data['navigator'] = 'Manage';
		$this->load->view('templates/header', $data);
		$this->load->view('cases/templates/share', $data);
		$this->load->view('cases/manage', $data);
		$this->load->view('templates/footer', $data);
	}*/

	public function report()
	{
		$data['title'] = 'Cases Report';
		$data['navigator'] = 'Report';
		$this->load->view('templates/header', $data);
		$this->load->view('cases/templates/share', $data);
		$this->load->view('cases/report', $data);
		$this->load->view('templates/footer', $data);
	}

	public function lists()
	{
		$data['title'] = 'Cases';
		$data['navigator'] = 'List';
		$this->load->view('templates/header', $data);
		$this->load->view('cases/templates/share', $data);
		$this->load->view('cases/lists', $data);
		$this->load->view('templates/footer', $data);
	}

	public function configuration()
	{
		$data['title'] = 'Cases Configuration';
		$data['navigator'] = 'Configuration';
		$this->load->view('templates/header', $data);
		$this->load->view('cases/templates/share', $data);
		$this->load->view('cases/configuration', $data);
		$this->load->view('templates/footer', $data);
	}

	public function viewMonthlyCaseByYear($year)
	{
		$systemUnitCountArray = array('Total'=>array());
		$caseAmountArray = array();
		
		if($year > 2000 & $year < 9999)
		{
			$hasData = false;
			$cases = $this->cases_model->getMonthlyCases();
			$caseAge = $this->cases_model->getCaseAgeAverageByYearMonth();
			//$systemUnits = $this->configure_model->getAllSystemUnit();
			for ($mon=1; $mon <=12 ; $mon++)
			{
				$totalAmount = 0;
				$date = $year.'-'.$mon.'-1';
				$time = strtotime($date)*1000;
				foreach($cases as $index => $case)
				{
					if(strtotime($case['ReportDate'])==strtotime($date))
					{
						$totalAmount += (int)$case['Amount'];
						if($case['SystemUnitName']==null)
						{
							continue;
						}
						if(!array_key_exists($case['SystemUnitName'], $systemUnitCountArray))
						{
							$systemUnitCountArray[$case['SystemUnitName']] = array();
							$caseAmountArray[$case['SystemUnitName']] = 0;
						}
						$caseAmountArray[$case['SystemUnitName']] = (int)$case['Amount'];
						array_push($systemUnitCountArray[$case['SystemUnitName']], array($time, $caseAmountArray[$case['SystemUnitName']]));
					}
				}

				if(!array_key_exists('CaseAge', $systemUnitCountArray))
					$systemUnitCountArray['CaseAge'] = array();
				foreach($caseAge as $index => $age)
				{
					if(strtotime($age['CaseYearMonth'])==strtotime($date))
					{
						$hasData = true;
						array_push($systemUnitCountArray['CaseAge'], array($time, round((float)$age['CaseAge']/60, 1)));
						break;
					}
				}
				if(!$hasData)
					array_push($systemUnitCountArray['CaseAge'], array($time, 0));
				array_push($systemUnitCountArray['Total'], array($time, $totalAmount));
				//var_dump(array_search($date, $caseAge[$mon-1]));
				//array_push($systemUnitCountArray['CaseAge'], array($time, $totalAmount));
			}
			//var_dump($caseAge);
		}

		echo json_encode($systemUnitCountArray);
	}

	public function viewMonthlyFunctionUnit($systemUnitId)
	{
		$functionUnitResultArray = array();
		$functionUnits = $this->cases_model->getMonthlyFunctionUnits($systemUnitId);
		foreach ($functionUnits as $functionUnit) {
			if(!array_key_exists($functionUnit['FunctionUnit'], $functionUnitResultArray))
			{
				$functionUnitResultArray[$functionUnit['FunctionUnit']] = array();
			}
			array_push($functionUnitResultArray[$functionUnit['FunctionUnit']], array(strtotime($functionUnit['ReportDate'])*1000, (int)$functionUnit['Amount']));
		}

		echo json_encode($functionUnitResultArray);
	}

	public function viewMonthlyCaseType()
	{
		$caseTypeResultArray = array();
		$functionUnits = $this->cases_model->getMonthlyCaseTypes();
		foreach ($functionUnits as $functionUnit) {
			if(!array_key_exists($functionUnit['CaseTypeName'], $caseTypeResultArray))
			{
				$caseTypeResultArray[$functionUnit['CaseTypeName']] = array();
			}
			array_push($caseTypeResultArray[$functionUnit['CaseTypeName']], array(strtotime($functionUnit['ReportDate'])*1000, (int)$functionUnit['Amount']));
		}
		
		echo json_encode($caseTypeResultArray);
	}

	public function viewDailyCaseByWeek($year, $month, $day)
	{
		$reArray = array();
		$date = $year.'-'.$month.'-'.$day;

		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('-1 week', strtotime($date))));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 day', strtotime($date))));
		
		if($year > 2000 & $year < 9999)
		{
			$cases = $this->cases_model->getCaseAmount($startDate, $endDate);
			$time = strtotime($startDate);
			for ($offset=0; $offset<8; $offset++)
			{
				$caseAmount = 0;
				foreach($cases as $case)
				{
					if(strtotime($case['Date']) - $time>=0 && strtotime($case['Date']) - $time<86.4)
					{
						$caseAmount = (int)$case['Amount'];
						break;
					}
				}
				array_push($reArray, array($time*1000, $caseAmount));
				$time = strtotime('+1 day', $time);
			}
		}

		echo json_encode($reArray);
	}

	public function viewSystemUnit($year, $month)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();
		
		if($year > 2000 & $year < 9999)
		{
			$results = $this->cases_model->getCaseSystemUnit($startDate, $endDate);
			//$results = $this->cases_model->getCaseSystemUnit($date);
			foreach($results as $result)
			{
				array_push($reArray, array($result['SystemUnitName'], (int)$result['Amount'], $result['SystemUnitId']));
			}
		}

		echo json_encode($reArray);
	}

	public function viewSystemUnitByTimeRange()
	{
		$reArray = array();
		$postData = json_decode(file_get_contents("php://input"));
		$results = $this->cases_model->getCaseSystemUnit($postData->from, $postData->to);
		//$results = $this->cases_model->getCaseSystemUnit($date);
		foreach($results as $result)
		{
			array_push($reArray, array($result['SystemUnitName'], (int)$result['Amount'], $result['SystemUnitId']));
		}

		echo json_encode($reArray);
	}

	public function viewFunctionUnitBy($id, $year, $month, $idType)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();
		
		if($year > 2000 & $year < 9999)
		{
			$results = $this->cases_model->getFunctionUnitBy($id, $startDate, $endDate, $idType);
			foreach($results as $result)
			{
				array_push($reArray, array($result['CaseTypeName'], $result['CaseTypeId'], $result['CaseTypeDetailName'], $result['CaseTypeDetailId'], (int)$result['Amount']));
			}
		}

		echo json_encode($reArray);
	}

	public function viewFunctionUnit($year, $month)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();
		
		if($year > 2000 & $year < 9999)
		{
			$results = $this->cases_model->getCaseFunctionUnit($startDate, $endDate);
			foreach($results as $result)
			{
				array_push($reArray, array($result['SystemUnitName'], $result['FunctionUnit'], (int)$result['Amount'], $result['FunctionUnitId']));
			}
		}

		echo json_encode($reArray);
	}

	public function viewFunctionUnitByTimeRange()
	{
		$reArray = array();
		$postData = json_decode(file_get_contents("php://input"));
		$results = $this->cases_model->getCaseFunctionUnit($postData->from, $postData->to);
		foreach($results as $result)
		{
			array_push($reArray, array($result['SystemUnitName'], $result['FunctionUnit'], (int)$result['Amount'], $result['FunctionUnitId']));
		}

		echo json_encode($reArray);
	}

	public function viewCaseSourceBy($id, $idType)
	{
		$postData = json_decode(file_get_contents("php://input"));
		$startDate = $postData->from;
		$endDate = $postData->to;
		$systemUnitArray = array();
		$systemUnitFlag = array();
		$functionUnitArray = array();
		$reArray = array();
		
		$results = $this->cases_model->getCaseSourceBy($id, $idType, $startDate, $endDate);
		foreach($results as $result)
		{
			if(!array_key_exists($result['SystemUnitName'], $systemUnitFlag)){
				$systemUnitFlag[$result['SystemUnitName']] = 1;
				array_push($systemUnitArray, array($result['SystemUnitName'], (int)$result['SystemUnitCount'], $result['SystemUnitId']));
			}
			array_push($functionUnitArray, array($result['SystemUnitName'], $result['FunctionUnitName'], (int)$result['FunctionUnitCount'], $result['FunctionUnitId']));
		}
		$reArray = array('systemUnitData'=>$systemUnitArray, 'functionUnitData'=>$functionUnitArray);
		echo json_encode($reArray);
	}

	public function viewSystemUnitDetail($suId, $year, $month)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();
		
		if($year > 2000 & $year < 9999)
		{
			$results = $this->cases_model->getCaseSystemUnitDetail($suId, $startDate, $endDate);
			foreach($results as $result)
			{
				array_push($reArray, array($result['SystemUnitDetailName'], (int)$result['Amount'], $result['SystemUnitDetailId']));
			}
		}

		echo json_encode($reArray);
	}

	public function viewSystemUnitDetailByTimeRange($suId)
	{
		$reArray = array();
		$postData = json_decode(file_get_contents("php://input"));
		
		$results = $this->cases_model->getCaseSystemUnitDetail($suId, $postData->from, $postData->to);
		foreach($results as $result)
		{
			array_push($reArray, array($result['SystemUnitDetailName'], (int)$result['Amount'], $result['SystemUnitDetailId']));
		}

		echo json_encode($reArray);
	}

	public function viewFunctionUnitDetail($fuId)
	{
		$postData = json_decode(file_get_contents("php://input"));
		$startDate = $postData->from;
		$endDate = $postData->to;
		$reArray = array();
		
		$results = $this->cases_model->getCaseFunctionUnitDetail($fuId, $startDate, $endDate);
		foreach($results as $result)
		{
			array_push($reArray, array($result['FunctionUnitDetailName'], (int)$result['Amount'], $result['FunctionUnitDetailId']));
		}

		echo json_encode($reArray);
	}

	public function viewSubFunctionUnitDetail($id, $year, $month, $isFunctionUnitId = false)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();
		
		if($year > 2000 & $year < 9999)
		{
			$results = $this->cases_model->getCaseSubFunctionUnitDetail($id, $startDate, $endDate, $isFunctionUnitId);
			foreach($results as $result)
			{
				array_push($reArray, array($result['CaseTypeName'], (int)$result['Amount'], $result['CaseTypeId']));
			}
		}

		echo json_encode($reArray);
	}

	public function viewSubFunctionUnitListDetail($id, $year, $month, $isFunctionUnitId = false)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();
		
		if($year > 2000 & $year < 9999)
		{
			$results = $this->cases_model->getCaseSubFunctionUnitList($id, $startDate, $endDate, $isFunctionUnitId);
			foreach($results as $result)
			{
				array_push($reArray, array($result['CaseTypeName'], $result['CaseTypeId'], $result['CaseTypeDetailName'], $result['CaseTypeDetailId'], (int)$result['Amount']));
			}
		}

		echo json_encode($reArray);
	}

	public function viewCaseTypeBy($id, $idType)
	{
		$postData = json_decode(file_get_contents("php://input"));
		$startDate = $postData->from;
		$endDate = $postData->to;
		$reArray = array();
		
		$results = $this->cases_model->getCaseTypeBy($id, $startDate, $endDate, $idType);
		foreach($results as $result)
		{
			array_push($reArray, array($result['CaseTypeName'], $result['CaseTypeId'], $result['CaseTypeDetailName'], $result['CaseTypeDetailId'], (int)$result['Amount']));
		}

		echo json_encode($reArray);
	}

	public function viewCaseType($year, $month)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();

		if($year > 2000 && $year < 9999)
		{
			$results = $this->cases_model->getCaseType($startDate, $endDate);
			foreach($results as $result)
			{
				array_push($reArray, array($result['CaseTypeName'], (int)$result['Amount'], $result['CaseTypeId']));
			}
		}
		echo json_encode($reArray);
	}

	public function viewCaseTypeDetail($caseTypeId, $year, $month)
	{
		$date = $year.'-'.$month.'-1';
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($date)));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($date))));
		$reArray = array();

		if($year > 2000 && $year < 9999)
		{
			$results = $this->cases_model->getCaseTypeDetail($caseTypeId, $startDate, $endDate);
			if(strtotime($date)<strtotime(NEWDATATIME))
			{
				foreach($results as $result)
				{
					array_push($reArray, array($result['SystemUnitName'].'_'.$result['FunctionUnit'], (int)$result['Amount']));
				}
			}
			else
			{
				foreach($results as $result)
				{
					array_push($reArray, array($result['CaseTypeDetailName'], (int)$result['Amount'], $result['CaseTypeDetailId']));
				}
			}
		}
		echo json_encode($reArray);
	}

	public function viewCaseOverview($year, $month)
	{
		$date = $year.'-'.$month.'-1';
		$reArray = array();

		if($year > 2000 && $year < 9999)
		{
			$results = $this->cases_model->getCaseOverview($date);
			foreach($results as $result)
			{
				array_push($reArray, array($result['SystemUnitName'], $result['FunctionUnit'], $result['CaseTypeName'], (int)$result['Amount']));
			}
		}
		echo json_encode($reArray);
	}

	public function viewTodayCaseList($sync = '')
	{
		$this->load->model('salesforce_model');
		$existingIds = array();
		$reArray = array();

		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+0 day', strtotime(date('Y-m-d')))));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 day', strtotime(date('Y-m-d')))));

		$syncDate = $this->cases_model->getLastSyncSalesforceCaseDate();
		if(local_to_gmt(time()) - strtotime($syncDate[0]['SyncDate']) >= 10*60 || $sync == 'sync')
		{
			$members = $this->cases_model->getMemberinfo();
			$sfCases = $this->salesforce_model->getSFCase($startDate, $endDate, $members, true);
			$results = $this->cases_model->getSFCaseId($startDate, $endDate);
			foreach ($results as $result) {
				array_push($existingIds, $result['SalesforceId']);
			}
			if($sfCases!=null)
			{
				if(gettype($sfCases)=='array'&&array_key_exists('records', $sfCases)&&count($sfCases['records'])>0)
				{
					$this->upsertSFCase($sfCases['records'], $existingIds);
				}
				else if(gettype($sfCases)=='object'&&property_exists($sfCases, 'records')&&count($sfCases->records)>0)
				{
					$this->upsertSFCase_soap($sfCases->records, $existingIds);
				}
			}
		}

		$sfCases = $this->cases_model->getCaseListOverview($startDate, $endDate);

		foreach($sfCases as $sfCase)
		{
			$row = $this->createShortCaseListRow($sfCase);
			array_push($reArray, $row);
		}

		echo json_encode(array('aaData'=>$reArray));
	}

	public function viewCaseList($year, $month, $status = NULL)
	{
		$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($year.'-'.$month,'-01')));
		$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($year.'-'.$month,'-01'))));
		$reArray = array();

		if($year > 2000 && $year < 9999)
		{
			$results = $this->cases_model->getCaseListOverview($startDate, $endDate);
			foreach($results as $result)
			{
				$row = $this->createCaseListRow($result, true);
				array_push($reArray, $row);
			}
		}
		echo json_encode($reArray);
	}

	public function viewEmergencyCaseList($year, $month = null, $status = NULL)
	{
		if($month!=null)
		{
			$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($year.'-'.$month.'-01')));
			$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($year.'-'.$month.'-01'))));
		}
		else
		{
			$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($year.'-01-01')));
			$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(($year+1).'-01-01')));
		}
		$reArray = array();

		if($year > 2000 && $year < 9999)
		{
			$results = $this->cases_model->getEmergencyCaseList($startDate, $endDate);
			foreach($results as $event)
			{
				$output = array(
					'id'=>$event['CaseId'],
					'title'=>$event['CaseSubject'],
					'reason'=>$event['CaseReason'],
					'incidence'=>$event['Incidence'],
					'start'=>mdate('%Y-%m-%dT%H:%i:%sZ', strtotime($event['StartDate'])),
					'end'=>mdate('%Y-%m-%dT%H:%i:%sZ', strtotime($event['EndDate'])),
					'to'=>mdate('%Y-%m-%dT%H:%i:%sZ', strtotime($event['EndDate'])),
					'allDay'=>false
				);
				array_push($reArray, $output);
			}
		}
		echo json_encode($reArray);
	}

	public function viewDailyCaseAmountOverview()
	{
		$reArray = array();
		$interval = 86400;	//24*3600 means 1 day for timestamp
		$i=0;

		$results = $this->cases_model->getDailyCaseAmount();
		foreach($results as $result)
		{
			$currentStamp = strtotime($result['Date']);
			if($i<count($results)-1)
			{
				$nextStamp = strtotime($results[($i+1)]['Date']);

				while (($nextStamp-$currentStamp)!=$interval) {
					array_push($reArray, array($currentStamp*1000, 0));
					$currentStamp+=$interval;
				}
			}
			array_push($reArray, array($currentStamp*1000, (int)$result['Amount']));

			$i++;
		}

		echo json_encode($reArray);
	}

	public function viewDailySystemUnitAmountOverview()
	{
		$totalCaseAmount = array();
		$systemUnitCountArray = array('Total'=>array());
		$caseAmountArray = array();
		$interval = 86400;	//24*3600 means 1 day for timestamp
		$i=0;

		$results = $this->cases_model->getDailyCaseBySystemUnit();
		foreach($results as $result)
		{
			$currentStamp = strtotime($result['Date'])*1000;
			if(!array_key_exists((string)$currentStamp, $totalCaseAmount))
			{
				$totalCaseAmount[(string)$currentStamp] = 0;
			}
			$totalCaseAmount[(string)$currentStamp]+=(int)$result['Amount'];
			if($result['SystemUnitName']==null)
			{
				continue;
			}
			if(!array_key_exists($result['SystemUnitName'], $systemUnitCountArray))
			{
				$systemUnitCountArray[$result['SystemUnitName']] = array();
				$caseAmountArray[$result['SystemUnitName']] = 0;
			}
			$caseAmountArray[$result['SystemUnitName']] = (int)$result['Amount'];
			array_push($systemUnitCountArray[$result['SystemUnitName']], array($currentStamp, $caseAmountArray[$result['SystemUnitName']]));
		}
		foreach ($totalCaseAmount as $timeStamp => $amount) {
			array_push($systemUnitCountArray['Total'], array((float)$timeStamp, $amount));
		}

		echo json_encode($systemUnitCountArray);
	}

	public function viewWeeklyCaseAmountOverview()
	{
		$reArray = array();
		$i=0;

		$results = $this->cases_model->getWeeklyCases();
		foreach($results as $result)
		{
			$currentStamp = strtotime($result['Date']);
			if($i<count($results)-1)
			{
				$nextStamp = strtotime($results[($i+1)]['Date']);

				while (($nextStamp-$currentStamp)!=$interval) {
					array_push($reArray, array($currentStamp*1000, 0));
					$currentStamp+=$interval;
				}
			}
			array_push($reArray, array($currentStamp*1000, (int)$result['Amount']));

			$i++;
		}

		echo json_encode($reArray);
	}

	public function viewOpenCase()
	{
		$reArray = array();

		$results = $this->cases_model->getOpenCaseDetail();
		foreach ($results as $result) {
			$row = $this->createCaseListRow($result, true);
			array_push($reArray, $row);
		}

		echo json_encode($reArray);
	}

	public function viewTopCaseType($caseTypeId){
		$reArray = array();
		$postData = json_decode(file_get_contents("php://input"));
		if($caseTypeId>0)
			$caseTypeList = $this->cases_model->getTopCaseTypeList($caseTypeId, $postData->from, $postData->to);
		else
			$caseTypeList = $this->cases_model->getCaseType($postData->from, $postData->to);
		foreach ($caseTypeList as $caseType) {
			array_push($reArray, array('CaseTypeName'=>$caseType['CaseTypeName'], 'CaseTypeId'=>$caseType['CaseTypeId'], 'CaseTypeDetailName'=>array_key_exists('CaseTypeDetailName', $caseType)?$caseType['CaseTypeDetailName']:'', 'CaseTypeDetailId'=>array_key_exists('CaseTypeDetailId', $caseType)?$caseType['CaseTypeDetailId']:'', 'Amount'=>(int)$caseType['Amount']));
		}
		echo json_encode($reArray);
	}

	public function viewCaseHandlingInfo($dataViewType){
		$distributionInterval = array(array(0, 0.5), array(0.5, 3), array(3, 24), array(24, 48), array(48, 72), array(72, 9999));
		$postData = json_decode(file_get_contents("php://input"));
		$reArray = array();
		$caseCreatedInfo = array();
		$caseClosedInfo = array();

		if(strcasecmp($dataViewType, 'hour')==0){
			$caseCreatedQuery = $this->cases_model->getCaseCreatedSummaryByHour($postData->from, $postData->to);
			$caseClosedQuery = $this->cases_model->getCaseClosedSummaryByHour($postData->from, $postData->to);
			$caseAgeQuery = $this->cases_model->getCaseAgeAverageByHour($postData->from, $postData->to);
			$caseAgeDistributionQuery = $this->cases_model->getCaseAgeDistribution($postData->from, $postData->to, $distributionInterval);
			$caseWayQuery = $this->cases_model->getCaseWay($postData->from, $postData->to);
		}
		elseif (strcasecmp($dataViewType, 'weekday')==0) {
			$caseCreatedQuery = $this->cases_model->getCaseCreatedSummaryByWeekDay($postData->from, $postData->to);
			$caseClosedQuery = $this->cases_model->getCaseClosedSummaryByWeekDay($postData->from, $postData->to);
			$caseAgeQuery = $this->cases_model->getCaseAgeAverageByWeekDay($postData->from, $postData->to);
			$caseAgeDistributionQuery = $this->cases_model->getCaseAgeDistribution($postData->from, $postData->to, $distributionInterval);
			$caseWayQuery = $this->cases_model->getCaseWay($postData->from, $postData->to);
		}
		$reArray['CreatedAmount'] = array();
		$reArray['ClosedAmount'] = array();
		$reArray['CaseAge'] = array();
		$hourIndex = 0;
		foreach($caseCreatedQuery as $caseCreatedInfo)
		{
			$createdHour = (int)$caseCreatedInfo['CreatedHour'];
			while($createdHour!=$hourIndex)
			{
				array_push($reArray['CreatedAmount'], array($hourIndex, 0));
				$hourIndex++;
			}
			if($createdHour==$hourIndex)
			{
				$hourIndex++;
				array_push($reArray['CreatedAmount'], array($createdHour, (int)$caseCreatedInfo['Amount']));
			}
		}
		$hourIndex = 0;
		foreach($caseClosedQuery as $caseClosedInfo)
		{
			$closedHour = (int)$caseClosedInfo['ClosedHour'];
			while($closedHour!=$hourIndex)
			{
				array_push($reArray['ClosedAmount'], array($hourIndex, 0));
				$hourIndex++;
			}
			if($closedHour==$hourIndex)
			{
				$hourIndex++;
				array_push($reArray['ClosedAmount'], array($closedHour, (int)$caseClosedInfo['Amount']));
			}
		}
		$hourIndex = 0;
		foreach($caseAgeQuery as $caseAgeInfo)
		{
			$createdHour = (int)$caseAgeInfo['CreatedHour'];
			while($createdHour!=$hourIndex)
			{
				array_push($reArray['CaseAge'], array($hourIndex, 0));
				$hourIndex++;
			}
			if($createdHour==$hourIndex)
			{
				$hourIndex++;
				array_push($reArray['CaseAge'], array($createdHour, round((int)$caseAgeInfo['CaseAge']/60, 1)));
			}
		}
		$reArray['CaseAgeDistribution'] = $caseAgeDistributionQuery;

		echo json_encode($reArray);
	}

	public function getCaseFilter()
	{
		$reArray = array();
		$tempArray = array();
		$results = $this->cases_model->getCaseTypeFilter();
		foreach($results as $result)
		{
			$tempArray[$result['CaseTypeId']] = $result['CaseTypeName'];
		}
		$reArray['CaseType'] = $tempArray;

		$tempArray = array();
		$results = $this->cases_model->getFunctionUnitFilter();
		foreach($results as $result)
		{
			$tempArray[$result['FunctionUnitId']] = array($result['FunctionUnit'], $result['SystemUnitName']);
			//array_push($tempArray, array($result['FunctionUnit'], $result['SystemUnitName']));
		}
		$reArray['FunctionUnit'] = $tempArray;

		$tempArray = array();
		$results = $this->cases_model->getSystemUnitFilter();
		foreach($results as $result)
		{
			$tempArray[$result['SystemUnitId']] = $result['SystemUnitName'];
			//array_push($tempArray, $result['SystemUnitName']);
		}
		$reArray['SystemUnit'] = $tempArray;

		echo (json_encode($reArray));
	}

	public function getTotalAmount()
	{
		$reStr = array();

		$result = $this->cases_model->getTotalCaseAmount();
		array_push($reStr, $result[0]['Amount']);

		$result = $this->cases_model->getLastSyncSalesforceCaseDate();
		array_push($reStr, date("Y-m-d H:i:s", strtotime($result[0]['SyncDate'].' UTC')));

		$result = $this->cases_model->getOpenCaseAmount();
		array_push($reStr, $result[0]['Amount']);

		echo json_encode($reStr);
	}

	public function AddCaseReport($caseDate, $caseTypeId, $functionUnitId, $amount)
	{
		$result = $this->cases_model->upsertCaseReport(mdate('%Y-%m-01', strtotime($caseDate)), $caseTypeId, $functionUnitId, $amount);
		if($result)
			echo 1;
		else
			echo 0;
	}

	public function UpsertCase($actionType='insert')
	{
		$postData = json_decode(file_get_contents("php://input"));
		if(!property_exists($postData, 'Amount'))
			$postData->Amount = 1;

		echo json_encode($this->cases_model->upsertCase($postData, $actionType));
	}

	public function upsertEmergency($actionType='insert')
	{
		$postData = json_decode(file_get_contents("php://input"));

		echo json_encode($this->cases_model->upsertEmergencyCase($postData, $actionType));
	}

	public function SyncCaseFromSalesforce($year, $month)
	{
		$this->load->model('salesforce_model');
		if($year>2000&&$year<9999&&$month>0&&$month<13)
		{
			$existingIds = array();
			$reArray = array();

			$startDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime($year.'-'.$month,'-01')));
			$endDate = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime('+1 month', strtotime($year.'-'.$month,'-01'))));

			$members = $this->cases_model->getMemberinfo();
			$sfCases = $this->salesforce_model->getSFCase($startDate, $endDate, $members, true);
			$results = $this->cases_model->getSFCaseId($startDate, $endDate);
			foreach ($results as $result) {
				array_push($existingIds, $result['SalesforceId']);
			}

			if($sfCases!=null)
			{
				if(gettype($sfCases)=='array'&&array_key_exists('records', $sfCases)&&count($sfCases['records'])>0)
				{
					echo $this->upsertSFCase($sfCases['records'], $existingIds);
				}
				else if(gettype($sfCases)=='object'&&property_exists($sfCases, 'records')&&count($sfCases->records)>0)
				{
					echo $this->upsertSFCase_soap($sfCases->records, $existingIds);
				}
				else 
					echo 1;
			}
		}
		else
			echo 1;
	}

	private function upsertSFCase($caseList, $existingIds)
	{
		$insertArray = array();
		$insertIndexes = array();
		$updateArray = array();
		$updateIndexes = array();
		$deleteArray = array();
		$isSingleCase = false;

		foreach ($caseList as $record) {
			if(gettype($record)!=='array')
			{
				$record = $caseList;
				$isSingleCase = true;
			}
			$upsertData = array(
				'SalesforceId'=>$record['Id'],
				'CaseNumber'=>$record['CaseNumber'],
				'Status'=>$record['Status'],
				'Owner'=>array_key_exists('Owner', $record)?$record['Owner']['Name']:NULL,
				'CreatedBy'=>array_key_exists('CreatedBy', $record)?$record['CreatedBy']['Name']:NULL,
				'CreatedDate'=>array_key_exists('CreatedDate', $record)?$record['CreatedDate']:NULL,
				'ClosedDate'=>array_key_exists('ClosedDate', $record)?$record['ClosedDate']:NULL,
				'UpdateDate'=>gmdate("Y-m-d H:i:s"),
				'UpdateBy'=>'ImportFromSF',
				'isDeleted'=>0
			);
			if(array_key_exists('Subject', $record))
				$upsertData['Subject'] = $record['Subject'];
			if(array_key_exists('Description', $record))
				$upsertData['Description'] = $record['Description'];

			if(in_array($record['Id'], $existingIds))
			{
				$updateIndexes[] = $record['Id'];
				$updateArray[] = $upsertData;
			}
			else
			{
				$insertIndexes[] = $record['Id'];
				$upsertData['InsertDate'] = gmdate("Y-m-d H:i:s");
				$upsertData['InsertBy'] = 'ImportFromSF';
				$insertArray[] = $upsertData;
			}

			if($isSingleCase)
				break;
	    }

	    $deleteArray = array_diff($existingIds, array_merge($updateIndexes, $insertIndexes));

	    $result = 1;
	    if(count($insertArray)>0)
			$result &= $this->cases_model->insertCaseOnSF($insertArray);
	    if(count($updateArray)>0)
			$result &= $this->cases_model->updateCaseOnSF($updateArray);
	    if(count($deleteArray)>0)
			$result &= $this->cases_model->deleteCaseOnSF($deleteArray);
		return $result;
	}

	private function upsertSFCase_soap($caseList, $existingIds)
	{
		$insertArray = array();
		$insertIndexes = array();
		$updateArray = array();
		$updateIndexes = array();
		$deleteArray = array();

		foreach ($caseList as $record) {
			$upsertData = array(
				'SalesforceId'=>$record->Id,
				'CaseNumber'=>$record->CaseNumber,
				'Status'=>property_exists($record, 'Status')?$record->Status:NULL,
				'Owner'=>property_exists($record, 'Owner')?$record->Owner->Name:NULL,
				'CreatedBy'=>property_exists($record, 'CreatedBy')?$record->CreatedBy->Name:NULL,
				'CreatedDate'=>property_exists($record, 'CreatedDate')?$record->CreatedDate:NULL,
				'ClosedDate'=>property_exists($record, 'ClosedDate')?$record->ClosedDate:NULL,
				'UpdateDate'=>gmdate("Y-m-d H:i:s"),
				'UpdateBy'=>'ImportFromSF',
				'isDeleted'=>0
			);
			if(property_exists($record, 'Subject'))
				$upsertData['Subject'] = $record->Subject;
			if(property_exists($record, 'Description'))
				$upsertData['Description'] = $record->Description;

			if(in_array($record->Id, $existingIds))
			{
				$updateIndexes[] = $record->Id;
				$updateArray[] = $upsertData;
			}
			else
			{
				$insertIndexes[] = $record->Id;
				$upsertData['InsertDate'] = gmdate("Y-m-d H:i:s");
				$upsertData['InsertBy'] = 'ImportFromSF';
				$insertArray[] = $upsertData;
			}
	    }

	    $deleteArray = array_diff($existingIds, array_merge($updateIndexes, $insertIndexes));

	    $result = 1;
	    if(count($insertArray)>0)
			$result &= $this->cases_model->insertCaseOnSF($insertArray);
	    if(count($updateArray)>0)
			$result &= $this->cases_model->updateCaseOnSF($updateArray);
	    if(count($deleteArray)>0)
			$result &= $this->cases_model->deleteCaseOnSF($deleteArray);
		return $result;
	}

	private function createCaseListRow($data, $editable = false)
	{
		$descriptionCol = '';
		$responseTimeCol = '';
		$caseAgeCol = '';
		$createCol = '';
		$caseTypeCol = '';
		$functionUnitCol = '';
		$statusCol = '';
		$editCol = '';

		if($data['Status']=='Closed')
			$statusStyle = 'label-success';
		else if($data['Status']=='In Progress')
			$statusStyle = 'label-info';
		else
			$statusStyle = 'label-important';
		$href = $data['SalesforceId']==''?'#':SFRootUrl.$data['SalesforceId'];
		$target = $data['SalesforceId']==''?'':'target = "_blank"';
		$caseTitle = $data['SalesforceId']==''?htmlspecialchars($data['Subject']):htmlspecialchars($data['CaseNumber'].' - '.$data['Subject']);

		$descriptionCol = '<span class="long-text"><a class = "data" href="'.$href.'" '.$target.' data-trigger = "manual" data-html="true" data-rel="popover" data-placement="bottom" data-content="'.nl2br(htmlspecialchars($data['Description'])).'" data-original-title="'.$caseTitle.'" >'.$data['Subject'].'</a></span>';
		
		$createCol = $data['CreatedBy'].'<span class="createDate" style="display:none;">'.mdate("%Y-%m-%d %H:%i:%s", strtotime($data['CreatedDate'].' UTC')).'</span>';
		
		$rTime = (float)$data['ResponseMinute'];
		if($rTime<=30)
			$responseTimeStyle = 'label-success';
		else if($rTime<180)
			$responseTimeStyle = 'label-info';
		else
			$responseTimeStyle = 'label-important';
		$responseDateVal = $data['ResponseDate']?mdate("%Y-%m-%d %H:%i:%s", strtotime($data['ResponseDate'].' UTC')):'';
		$responseTimeCol = '<span class="response-time label '.$responseTimeStyle.'">'.$data['ResponseMinute'].'</span>'.'<span class="responseDate" style="display:none;">'.$responseDateVal.'</span>';;

		$cAge = (float)$data['CaseHour'];
		if($cAge<=0.5)
			$caseAgeStyle = 'label-success';
		else if($cAge<3)
			$caseAgeStyle = 'label-info';
		else
			$caseAgeStyle = 'label-important';
		$caseAgeCol = '<span class="case-age label '.$caseAgeStyle.'">'.$data['CaseHour'].'</span>'.'<span class="caseAge" style="display:none;">'.$data['CaseHour'].'</span>';;

        $ownerCol = $data['Owner'];
        
		$statusCol = '<span class="label '.$statusStyle.'">'.$data['Status'].'</span>';
		
		if($editable)
		{		
			$caseTypeCol = '<span class="caseType long-text">'.$data['CaseTypeDetailName'].'</span>';
			
			$functionUnitCol = '<span class="functionUnit long-text">'.$data['FunctionUnitDetailName'].'</span>';
		
			$extraAttr = 'data-caseId="'.$data['CaseId'].'" data-caseTypeDetailId="'.$data['CaseTypeDetailId'].'" data-functionUnitDetailId="'.$data['FunctionUnitDetailId'].'"';
			$editCol = $editable?"<a class='btn btn-mini edit' title='Edit' $extraAttr><i class='icon-edit'></i></a>":'';
		}

		return array($descriptionCol, $createCol, $responseTimeCol, $caseAgeCol, $functionUnitCol, $caseTypeCol, $ownerCol, $statusCol, $editCol);
	}

	private function createShortCaseListRow($data)
	{
		if($data['Status']=='Closed')
			$statusStyle = 'label-success';
		else
			$statusStyle = 'label-important';
		$href = $data['SalesforceId']==''?'#':SFRootUrl.$data['SalesforceId'];
		$target = $data['SalesforceId']==''?'':'target = "_blank"';
		$caseTitle = $data['SalesforceId']==''?htmlspecialchars($data['Subject']):htmlspecialchars($data['CaseNumber'].' - '.$data['Subject']);

		$descriptionCol = '<a class = "data" href="'.$href.'" '.$target.' data-trigger = "manual" data-html="true" data-rel="popover" data-placement="bottom" data-content="'.nl2br(htmlspecialchars($data['Description'])).'" data-original-title="'.$caseTitle.'" >'.$data['Subject'].'</a>';
		
		$createCol = $data['CreatedBy'].'<span class="createDate" style="display:none;">'.mdate("%Y-%m-%d %H:%i:%s", strtotime($data['CreatedDate'].' UTC')).'</span>';

		$ownerCol = $data['Owner'];

		$statusCol = '<span class="label '.$statusStyle.'">'.$data['Status'].'</span>';

		return array($descriptionCol, $createCol, $ownerCol, $statusCol);
	}
}
