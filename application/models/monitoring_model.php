<?php
class Monitoring_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getEvent($eventId = 'Id')
	{
		$sql = "SELECT * FROM MonitoringEvents me WHERE me.isDeleted = 0 AND me.Id = $eventId";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getMonitoringLog($eventId){
		$sql = 'SELECT * FROM MonitoringLog ml WHERE ml.MonitoringEventId = '.$eventId;
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function addEvent($subject, $server, $query){
		$insertData = array(
			'Subject'=>$subject, 
			'ExternalServer'=>$server, 
			'QuerySQL'=>$query, 
			'isActive'=>1, 
			'isDeleted'=>0, 
			'InsertDate'=>date("Y-m-d H:i:s",local_to_gmt(time())), 
			'InsertBy'=>'TechSupport', 
			'UpdateDate'=>date("Y-m-d H:i:s",local_to_gmt(time())), 
			'UpdateBy'=>'TechSupport'
		);
		//$sql = "INSERT INTO MonitoringList VALUES(NULL, '$subject', '$server', '$query');";
		$query = $this->db->insert('MonitoringEvents', $insertData);
		return $query;
	}

	public function addMonitoringLog($eventId, $logTitle, $logMessage){
		$insertData = array(
			'LogTitle'=>$logTitle,
			'LogMessage'=>$logMessage,
			'MonitoringEventId'=>$eventId,
			'InsertDate'=>date("Y-m-d H:i:s",local_to_gmt(time())),
			'InsertBy'=>'TechSupport',
			'UpdateDate'=>date("Y-m-d H:i:s",local_to_gmt(time())),
			'UpdateBy'=>'TechSupport'
		);
		$query = $this->db->insert('MonitoringLog', $insertData);
		//$query = $this->db->delete('MonitoringEvents', array('Id' => $id));
		return $query;
	}

	public function updateEvent($id, $subject, $server, $query){
		$updateData = array(
			'Subject'=>$subject, 
			'ExternalServer'=>$server, 
			'QuerySQL'=>$query, 
			'UpdateDate'=>date("Y-m-d H:i:s",local_to_gmt(time())), 
			'UpdateBy'=>'TechSupport'
		);
		$query = $this->db->update('MonitoringEvents', $updateData, "Id = $id");
		return $query;
	}

	public function deleteEvent($id){
		$updateData = array(
			'isDeleted'=>1, 
			'UpdateDate'=>date("Y-m-d H:i:s",local_to_gmt(time())), 
			'UpdateBy'=>'TechSupport'
		);
		$query = $this->db->update('MonitoringEvents', $updateData, "Id = $id");
		//$query = $this->db->delete('MonitoringEvents', array('Id' => $id));
		return $query;
	}

	public function registerAgent($agentName){
		$this->db->trans_start();
		$existingAgentQuery = $this->db->get_where('Agent', array('AgentName'=>$agentName));
		if($existingAgentQuery->num_rows()==0)
		{
			$insertData = array('AgentName'=>$agentName, 'LastRegisterTime'=>date("Y-m-d H:i:s",local_to_gmt(time())));
			//$sql = "INSERT INTO MonitoringList VALUES(NULL, '$subject', '$server', '$query');";
			$query = $this->db->insert('Agent', $insertData);
		}
		else
		{
			$updateData = array('LastRegisterTime'=>date("Y-m-d H:i:s",local_to_gmt(time())));
			$query = $this->db->update('Agent', $updateData, array('AgentId'=>$existingAgentQuery->first_row()->AgentId));
		}
		$this->db->trans_complete();
		return $query;
	}

	public function executeMSSQLScript($sql, $server){
		$connect = new PDO("odbc:Driver=FreeTDS; Server=$server; Port=1433; Database=et_main; UID=etownreader; PWD=fishing22;");
		//$sql = 'select top 10 username from members';
		$result = $connect->query($sql);

		if(!$result)
			return NULL;
		return $result->fetchAll();
	}
}