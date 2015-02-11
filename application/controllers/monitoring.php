<?php

class Monitoring extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
		$this->load->model('monitoring_model');
	}
	public function index()
	{
		$data['title'] = 'Monitoring';
		$this->load->view('templates/header', $data);
		$this->load->view('monitoring/index', $data);
		$this->load->view('templates/footer', $data);
	}

	public function configuration()
	{
		$data['title'] = 'Monitoring';
		$this->load->view('templates/header', $data);
		$this->load->view('monitoring/configuration', $data);
		$this->load->view('templates/footer', $data);
		$this->load->view('monitoring/templates/footer', $data);
	}

	public function eventdetail($eventId){
		$eventInfo = $this->monitoring_model->getEvent($eventId);
		$data['title'] = 'Monitoring Event Detail';
		if(count($eventInfo)>0){
			$data['eventInfo'] = $eventInfo[0];
			$data['eventSubject'] = $eventInfo[0]['Subject'];
			$data['eventServer'] = $eventInfo[0]['ExternalServer'];
			$data['eventQuerySQL'] = $eventInfo[0]['QuerySQL'];
			$data['eventStatus'] = $eventInfo[0]['isActive']?'Active':'Deactive';

			$data['eventLog'] = $this->_viewLog($eventInfo[0]['Id']);
		}
		$this->load->view('templates/header', $data);
		$this->load->view('monitoring/eventDetail', $data);
		$this->load->view('templates/footer', $data);
		$this->load->view('monitoring/templates/footer', $data);
	}

	public function addEvent()
	{
		$postData = json_decode(file_get_contents("php://input"));
		$result = $this->monitoring_model->addEvent($postData->subject, $postData->server, $postData->sql);
		echo json_encode($result);
	}

	public function updateEvent()
	{
		$postData = json_decode(file_get_contents("php://input"));
		$result = $this->monitoring_model->updateEvent($postData->id, $postData->subject, $postData->server, $postData->sql);
		echo json_encode($result);
	}

	public function deleteEvent()
	{
		$postData = json_decode(file_get_contents("php://input"));
		$result = $this->monitoring_model->deleteEvent($postData->id);
		echo json_encode($result);
	}

	public function getList()
	{
		$reArray = array();
		$results = $this->monitoring_model->getEvent();
		foreach($results as $result)
		{
			$statusCol = '<span class="label label-success">'.($result['isActive']?'Active':'Deactive').'</span>';
			$editBtn = '<a class="btn btn-mini edit-event" title="Edit" data-id="'.$result['Id'].'"><i class="icon-edit"></i></a>';
			$deleteBtn = '<a class="btn btn-mini delete-event" title="Delete" data-id="'.$result['Id'].'"><i class="icon-trash"></i></a>';
			$detailBtn = '<a class="btn btn-mini view-event" title="View Detail" data-id="'.$result['Id'].'" target="_blank" href="/monitoring/eventdetail/'.$result['Id'].'"><i class="icon-zoom-in"></i></a>';
			array_push($reArray, array($result['Subject'], $result['ExternalServer'], '<pre>'.$result['QuerySQL'].'</pre>', $statusCol, $editBtn.$deleteBtn.$detailBtn));
		}
		echo json_encode(array('aaData'=>$reArray));
	}

	public function getEventList()
	{
		$reArray = array();
		$results = $this->monitoring_model->getEvent();
		foreach($results as $result)
		{
			array_push($reArray, array($result['Subject'], $result['ExternalServer'], $result['QuerySQL']));
		}
		echo json_encode($reArray);
	}

	public function checkServer($ipAddress){
		$this->load->model('freetds_model');
		echo $this->freetds_model->checkServer($ipAddress);
	}

	public function registerAgent($agentName){
		//$postData = json_decode(file_get_contents("php://input"));
		echo $this->monitoring_model->registerAgent($agentName);
	}

	public function viewLog($eventId){
		$logItems = $this->_viewLog($eventId);
		foreach ($logItems as $item) {
			echo $item['LogTitle'].'<br>'.$item['LogMessage'];
		}
	}

	private function _viewLog($eventId){
		$returnStr = array();
		$results = $this->monitoring_model->getMonitoringLog($eventId);
		foreach ($results as $log) {
			array_push($returnStr, array('LogId'=>$log['LogId'], 'LogTitle'=>$log['LogTitle'], 'LogMessage'=>$log['LogMessage']));
		}
		return $returnStr;
	}

	public function sendNotification_deleted($message){
		require_once 'Mail.php';
		require_once 'Mail/mime.php';
 
 		//authorization info
		$host = "ssl://smtp.gmail.com";
		$port = '465';
		$username = "wjh000123";
		$password = "mland995511gmail";

		//mail sending info
 		$crlf = "\n";
		$from = "wjh000123@gmail.com";
		$to = "lee.wangjh@ef.com";
		$subject = "Monitoring Notification";
		$body = $message;
		$headers =	array(
					'From'			=> $from,
					'To'			=> $to,
					'Subject'		=> $subject
					);

		$mime = new Mail_mime($crlf);
		$mime->setTXTBody($message);
		$mime->setHTMLBody('<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" xmlns="http://www.w3.org/TR/REC-html40"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="Generator" content="Microsoft Word 14 (filtered medium)" />
<style>
html {
	font-family:Calibri,宋体;
}
div {
	font-family:Calibri,宋体;
}
.item {
	border-bottom: 1px solid #aaa;
}
table {
	font-family:Calibri,宋体;
	text-align: left;
}
</style></head><body><div>'.$message.'</div></body></html>');
		//$mimeparams['text_encoding']="8bit";
		$mimeparams['text_charset']="UTF-8";
		$mimeparams['html_charset']="UTF-8";
		$mimeparams['head_charset']="UTF-8";
		$body = $mime->get($mimeparams);
		$headers = $mime->headers($headers);

		$smtp = Mail::factory('smtp',
		array (
			'host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password
			)
		);
		$mail = $smtp->send($to, $headers, $body);
		if (PEAR::isError($mail)) {
			return (array('success'=>false, 'message'=>$mail->getMessage(), 'time'=>date('Y-m-d H:i:s', local_to_gmt(time()))));
		} else {
			return (array('success'=>true, 'message'=>'Message successfully sent!', 'time'=>date('Y-m-d H:i:s', local_to_gmt(time()))));
		}
	}

	public function sendNotification($message){
		$to = 'ec_support@ef.com';
		$cc = 'yihui.cao@ef.com';
		$subject = 'monitoring result';
		$htmlBody = '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" xmlns="http://www.w3.org/TR/REC-html40"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="Generator" content="Microsoft Word 14 (filtered medium)" />
<style>
html {
	font-family:Calibri,宋体;
}
div {
	font-family:Calibri,宋体;
}
.item {
	border-bottom: 1px solid #aaa;
}
table {
	font-family:Calibri,宋体;
	text-align: left;
}
</style></head><body><div>'.$message.'</div></body></html>';

		return $this->_SendCloudHTTP($to, $cc, $subject, $htmlBody);
	}

	private function _SendCloudHTTP($to, $cc, $subject, $body){
		$url = 'https://sendcloud.sohu.com/webapi/mail.send.json';
		$param = array('api_user' => 'postmaster@devops.sendcloud.org',
				'api_key' => 'cjNRorBm',
				'from' => 'noreply.ec_support@ef.com',
				'fromname' => 'devops',
				'to' => $to,
				'cc' => $cc,
				'subject' => $subject,
				'html' => $body);

		$options = array
		(
			'http' => array
			(
				'method'  => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($param)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		return $result;
	}

	private function formatArrayToHtmlTable($sqlResultArray){
		$table = '';
		$headers = array();
		$tableHeader = '';
		$tableContent = '<tbody>';

		foreach ($sqlResultArray as $result) {
			$tableContent.='<tr>';
			foreach ($result as $key => $value) {
				if(!is_int($key))
				{
					if(!in_array($key, $headers))
						array_push($headers, $key);
					$tableContent.='<td>'.$value.'</td>';
				}
			}
			$tableContent.='</tr>';
		}
		$tableContent.='</tbody>';
		$tableHeader.='<thead><tr>';
		foreach ($headers as $header) {
			$tableHeader.='<th>'.$header.'</th>';
		}
		$tableHeader.='</tr></thead>';
		$table = '<table class="table-condensed">'.$tableHeader.$tableContent.'</table>';

		return $table;
	}

	public function executeSQL(){
		$workStartingHour = 1;
		$OffHour = 13;
		$this->load->model('freetds_model');
		$eventResults = array();
		$count = 0;
		$message = "";

		$currentGMTHour = gmdate('H');
		$this->load->library('../controllers/configure');
		$result = $this->configure->isHoliday(date('Y-m-d'));
		$result = str_replace('"', '', $result);
		$isHoliday = substr($result, strrpos($result, ':')+1, 1);
		if($currentGMTHour<$workStartingHour||$currentGMTHour>$OffHour||($isHoliday=='1'||$isHoliday=='2'))
			return;

		$monitoringEvents = $this->monitoring_model->getEvent();
		//$postData = json_decode(file_get_contents("php://input"));
		foreach ($monitoringEvents as $event) {
			array_push($eventResults, $this->freetds_model->executeScript($event['QuerySQL'], $event['ExternalServer']));
		}
		
		foreach($eventResults as $result)
		{
			if(($amount=count($result))>0)
			{
				$tableList = $this->formatArrayToHtmlTable($result);
				$resultOutput = '<div class="item"><br>';
				$resultOutput.= 'Total <b>'.$amount.'</b> for <span style="text-decoration:underline;">'.$monitoringEvents[$count]['Subject'].'</span>:';
				$resultOutput.= '<br><br>';
				$resultOutput.= $tableList;
				$resultOutput.= '<br></div>';
				$message.=$resultOutput;

				$logTitle = date('Y-m-d H:i:s', local_to_gmt(time())).'(GMT) - Total <b>'.$amount.'</b> records';
				$logMessage = $tableList;
				$this->monitoring_model->addMonitoringLog($monitoringEvents[$count]['Id'], $logTitle, $logMessage);
			}
			$count++;
		}
		//$message = '<html><body style="font-family:calibri;">'.$message.'</body></html>';
		//echo $message;
		if(!empty($message))
		{
			$mailResult = $this->sendNotification($message);
			echo 'GMT '.mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(time())).' - '.$mailResult.' \r\n';
		}
	}

	public function executeSQL2(){
		$this->load->model('freetds_model');
		$eventResults = array();
		$count = 0;
		$message = "";

		$monitoringEvents = $this->monitoring_model->getEvent();
		//$postData = json_decode(file_get_contents("php://input"));
		foreach ($monitoringEvents as $event) {
			array_push($eventResults, $this->freetds_model->executeScript($event['QuerySQL'], $event['ExternalServer']));
		}

		foreach($eventResults as $result)
		{
			if(($amount=count($result))>0)
			{
				$message.= '<div class="item"><br>';
				$message.= 'Total <b>'.$amount.'</b> for <span style="text-decoration:underline;">'.$monitoringEvents[$count]['Subject'].'</span>:';
				$message.= '<br><br>';
				$message.= $this->formatArrayToHtmlTable($result);
				$message.= '<br></div>';
			}
			$count++;
		}
		//$message = '<html><body style="font-family:calibri;">'.$message.'</body></html>';
		echo $message;
		//$mailResult = $this->sendNotification($message);
		//echo 'GMT '.mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(time())).' - '.$mailResult.' \r\n';
	}
}
