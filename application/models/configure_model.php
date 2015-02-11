<?php
class Configure_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->helper('date');
	}

	public function getAllSystemUnit()
	{
		$sql = "SELECT
					sul.SystemUnitId,
					sul.SystemUnitName
				FROM
					SystemUnit_lkp sul
				WHERE
					sul.SystemUnitName IS NOT NULL
				ORDER BY
					sul.SystemUnitId;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllFunctionUnit()
	{
		$sql = "SELECT
					sul.SystemUnitId,
					sul.SystemUnitName,
					ful.FunctionUnitId,
					ful.FunctionUnit
				FROM
					SystemUnit_lkp sul
				LEFT JOIN FunctionUnit_lkp ful ON sul.SystemUnitId = ful.SystemUnitId
				LEFT JOIN (
					SELECT
						ful2.FunctionUnitId,
						COUNT(1) AS FUAmount
					FROM
						Cases c
					INNER JOIN FunctionUnitDetail fud2 ON fud2.FunctionUnitDetailId = c.FunctionUnitDetailId
					INNER JOIN FunctionUnit_lkp ful2 ON ful2.FunctionUnitId = fud2.FunctionUnitId
					GROUP BY
						ful2.FunctionUnitId
				) T ON ful.FunctionUnitId = T.FunctionUnitId
				WHERE
					(
						sul.SystemUnitId IS NULL
						OR (
							sul.SystemUnitId IS NOT NULL
							AND sul.SystemUnitName IS NOT NULL
						)
					)
				AND (
					ful.FunctionUnitId IS NULL
					OR (
						ful.FunctionUnitId IS NOT NULL
						AND ful.FunctionUnit IS NOT NULL
					)
				)
				ORDER BY
					sul.SystemUnitName,
					T.FUAmount DESC,
					ful.FunctionUnit;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllSubFunctionUnit()
	{
		$sql = "SELECT
					sul.SystemUnitId,
					sul.SystemUnitName,
					ful.FunctionUnitId,
					ful.FunctionUnit,
					fud.FunctionUnitDetailId,
					fud.FunctionUnitDetailName
				FROM
					SystemUnit_lkp sul
				LEFT JOIN FunctionUnit_lkp ful ON sul.SystemUnitId = ful.SystemUnitId
				LEFT JOIN FunctionUnitDetail fud ON ful.FunctionUnitId = fud.FunctionUnitId
				LEFT JOIN (
					SELECT
						ful2.FunctionUnitId,
						COUNT(1) AS FUAmount
					FROM
						Cases c
					INNER JOIN FunctionUnitDetail fud2 ON fud2.FunctionUnitDetailId = c.FunctionUnitDetailId
					INNER JOIN FunctionUnit_lkp ful2 ON ful2.FunctionUnitId = fud2.FunctionUnitId
					GROUP BY
						ful2.FunctionUnitId
				) T ON ful.FunctionUnitId = T.FunctionUnitId
				LEFT JOIN (
					SELECT
						c.FunctionUnitDetailId,
						COUNT(1) AS FUDAmount
					FROM
						Cases c
					GROUP BY
						c.FunctionUnitDetailId
				) T2 ON fud.FunctionUnitDetailId = T2.FunctionUnitDetailId
				ORDER BY
					sul.SystemUnitId,
					T.FUAmount DESC,
					ful.FunctionUnit,
					T2.FUDAmount DESC,
					fud.FunctionUnitDetailName";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllCaseType()
	{
		$sql = "SELECT
					ctl.CaseTypeId,
					ctl.CaseTypeName
				FROM
					CaseType_lkp ctl
				WHERE
					ctl.CaseTypeName IS NOT NULL
				ORDER BY
					ctl.CaseTypeId;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllSubCaseType()
	{
		$sql = "SELECT
					ctl.CaseTypeId,
					ctl.CaseTypeName,
					ctd.CaseTypeDetailId,
					ctd.CaseTypeDetailName
				FROM
					CaseType_lkp ctl
				LEFT JOIN CaseTypeDetail ctd ON ctl.CaseTypeId = ctd.CaseTypeId
				WHERE
					(
						ctl.CaseTypeId IS NULL
						OR (
							ctl.CaseTypeId IS NOT NULL
							AND ctl.CaseTypeName IS NOT NULL
						)
					)
				AND (
					ctd.CaseTypeDetailId IS NULL
					OR (
						ctd.CaseTypeDetailId IS NOT NULL
						AND ctd.CaseTypeDetailName IS NOT NULL
					)
				)
				ORDER BY
					ctl.CaseTypeId,
					ctd.CaseTypeDetailId;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
    
    public function getAllCDNServersIP()
	{
		$sql = "SELECT
					ServerIP
				FROM
					CDNServers cdns
				WHERE
					isDeleted = 0";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
    
    public function insertCollectedConnectionInfo($insertList)
	{
		$dateInfo = array('InsertDate'=>date('Y-m-d H:i:s', local_to_gmt(time())) ,
                     'UpdateDate'=>date('Y-m-d H:i:s', local_to_gmt(time())) );
        $insertData = array_merge($dateInfo, $insertList);
		if($this->db->insert('NetworkTrack', $insertData))
			return true;
        return false;
	}

	public function updateTable($data, $needInsertId = false)
	{
		$result = 1;
		$updateList = $this->adjustDataToDB($data, 'update');
		$insertList = $this->adjustDataToDB($data, 'insert');

		$this->db->trans_start();
		foreach ($updateList as $dbTable => $dataSet) {
			foreach ($dataSet as $field => $dataArray) {
				$this->db->update_batch($dbTable, $dataArray, $field);
			}
		}
		foreach ($insertList as $dbTable => $dataSet) {
			$this->db->insert_batch($dbTable, $dataSet);
		}
		if($needInsertId)
		{
			$result = $this->db->insert_id();
		}
		$this->db->trans_complete();

		return $result;
	}

	private function adjustDataToDB($raw, $type)
	{
		$reArray = array();
		switch(strtolower($type))
		{
			case 'update':
				foreach ($raw as $r) {
					$dbTable = $this->getDBTableByDataType($r->DataType);
					$dbBatchArray = $this->adjustDataToUpdateBatch($r);
					$dbOnField = $this->getFieldReferenceByDataType($r->DataType);
					
					if($dbTable!=null&&$dbBatchArray!=null&&$dbOnField!=null)
					{
						if(!array_key_exists($dbTable, $reArray))
							$reArray[$dbTable][$dbOnField] = array();
						array_push($reArray[$dbTable][$dbOnField], $dbBatchArray);
					}
				}
			break;
			case 'insert':
				foreach ($raw as $r) {
					$dbTable = $this->getDBTableByDataType($r->DataType);
					$dbBatchArray = $this->adjustDataToInsertBatch($r);
					
					if($dbTable!=null&&$dbBatchArray!=null)
					{
						if(!array_key_exists($dbTable, $reArray))
							$reArray[$dbTable] = array();
						array_push($reArray[$dbTable], $dbBatchArray);
					}
				}
			break;
		}
		
		return $reArray;
	}

	private function getDBTableByDataType($sourceType)
	{
		switch(strtolower($sourceType))
		{
			case 'data-functionunit':
				return 'FunctionUnit_lkp';
			break;
			case 'data-systemunit':
				return 'SystemUnit_lkp';
			break;
			case 'data-subfunctionunit':
				return 'FunctionUnitDetail';
			break;
			case 'data-casetype':
				return 'CaseType_lkp';
			break;
			case 'data-subcasetype':
				return 'CaseTypeDetail';
			break;
			default:
				return null;
			break;
		}
	}

	private function getFieldReferenceByDataType($sourceType)
	{
		switch(strtolower($sourceType))
		{
			case 'data-functionunit':
				return 'FunctionUnitId';
			break;
			case 'data-systemunit':
				return 'SystemUnitId';
			break;
			case 'data-subfunctionunit':
				return 'FunctionUnitDetailId';
			break;
			case 'data-casetype':
				return 'CaseTypeId';
			break;
			case 'data-subcasetype':
				return 'CaseTypeDetailId';
			break;
			default:
				return null;
			break;
		}
	}

	private function adjustDataToUpdateBatch($sourceData)
	{
		if(stripos($sourceData->Index, 'p|')!==false)
			return null;

		$re = array();

		switch(strtolower($sourceData->DataType))
		{
			case 'data-functionunit':
				$re['FunctionUnitId'] = $sourceData->Index;
				$re['FunctionUnit'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			case 'data-systemunit':
				$re['SystemUnitId'] = $sourceData->Index;
				$re['SystemUnitName'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			case 'data-subfunctionunit':
				$re['FunctionUnitDetailId'] = $sourceData->Index;
				$re['FunctionUnitDetailName'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			case 'data-casetype':
				$re['CaseTypeId'] = $sourceData->Index;
				$re['CaseTypeName'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			case 'data-subcasetype':
				$re['CaseTypeDetailId'] = $sourceData->Index;
				$re['CaseTypeDetailName'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			default:
				return null;
			break;
		}
		$re['UpdateDate'] =  date('Y-m-d H:i:s', local_to_gmt(time()));
		$re['UpdateBy'] =  'Manual';

		return $re;
	}

	private function adjustDataToInsertBatch($sourceData)
	{
		if(stripos($sourceData->Index, 'p|')===false)
			return null;

		$re = array();

		switch(strtolower($sourceData->DataType))
		{
			case 'data-systemunit':
				$re['SystemUnitName'] = $sourceData->Value;
			break;
			case 'data-functionunit':
				$re['SystemUnitId'] = str_replace('p|', '', $sourceData->Index);
				$re['FunctionUnit'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			case 'data-subfunctionunit':
				$re['FunctionUnitId'] = str_replace('p|', '', $sourceData->Index);
				$re['FunctionUnitDetailName'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			case 'data-casetype':
				$re['CaseTypeName'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			case 'data-subcasetype':
				$re['CaseTypeId'] = str_replace('p|', '', $sourceData->Index);
				$re['CaseTypeDetailName'] = empty($sourceData->Value)?NULL:$sourceData->Value;
			break;
			default:
				return null;
			break;
		}
		$re['InsertDate'] = date('Y-m-d H:i:s', local_to_gmt(time()));
		$re['InsertBy'] =  'Manual';
		$re['UpdateDate'] = date('Y-m-d H:i:s', local_to_gmt(time()));
		$re['UpdateBy'] =  'Manual';

		return $re;
	}
}