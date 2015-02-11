<?php

class Cases_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function getMonthlyCases()
	{
		$divideTime = NEWDATATIME;
		$divideUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		$sql = "SELECT
					`sul`.`SystemUnitName` AS `SystemUnitName`,
					sum(`cr`.`Amount`) AS `Amount`,
					`cr`.`CaseMonth` AS `ReportDate`
				FROM
					`CaseReport` `cr`
				LEFT JOIN FunctionUnit_lkp ful ON cr.FunctionUnitId = ful.FunctionUnitId
				LEFT JOIN SystemUnit_lkp sul ON ful.SystemUnitId = sul.SystemUnitId
				WHERE
					`cr`.`CaseMonth` < '$divideTime'
				GROUP BY
					`cr`.`CaseMonth`,
					`ful`.`SystemUnitId`
				UNION
					SELECT
						`sul`.`SystemUnitName` AS `SystemUnitName`,
						count(1) AS `Amount`,
						CONCAT(
							YEAR (`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR),
							'-',
							MONTH (`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR),
							'-1'
						) AS `ReportDate`
					FROM
						`Cases` `c`
					LEFT JOIN `FunctionUnitDetail` `fud` ON `c`.`FunctionUnitDetailId` = `fud`.`FunctionUnitDetailId`
					LEFT JOIN `FunctionUnit_lkp` `ful` ON `fud`.`FunctionUnitId` = `ful`.`FunctionUnitId`
					LEFT JOIN `SystemUnit_lkp` `sul` ON `ful`.`SystemUnitId` = `sul`.`SystemUnitId`
					WHERE
						`c`.`isDeleted` = 0
						AND `c`.`CreatedDate` >= '$divideUTCTime'
					GROUP BY
						YEAR (
							`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						MONTH (
							`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						`ful`.`SystemUnitId`;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getMonthlyFunctionUnits($systemUnitId)
	{
		$sql = "SELECT
					ful.FunctionUnit,
					count(1) AS Amount,
					CONCAT(
						YEAR (
							`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						'-',
						MONTH (
							`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						'-1'
					) AS `ReportDate`
				FROM
					FunctionUnit_lkp ful
				LEFT JOIN FunctionUnitDetail fud ON ful.FunctionUnitId = fud.FunctionUnitId
				INNER JOIN Cases c ON fud.FunctionUnitDetailId = c.FunctionUnitDetailId
				WHERE
					SystemUnitId = '$systemUnitId'
					AND c.isDeleted = 0
					AND (
						SELECT
							EXTRACT(YEAR_MONTH FROM (`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR))
					) < (
						SELECT
							EXTRACT(YEAR_MONTH FROM (UTC_TIMESTAMP() + INTERVAL ".TIMEZONEOFFSET." HOUR))
					)
				GROUP BY
					YEAR (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					MONTH (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					ful.FunctionUnitId
				ORDER BY
					YEAR (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					MONTH (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					Amount DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getMonthlyCaseTypes()
	{
		$sql = "SELECT
					ctl.CaseTypeName,
					count(1) AS Amount,
					CONCAT(
						YEAR (
							`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						'-',
						MONTH (
							`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						'-1'
					) AS `ReportDate`
				FROM
					CaseType_lkp ctl
				LEFT JOIN CaseTypeDetail ctd ON ctl.CaseTypeId = ctd.CaseTypeId
				INNER JOIN Cases c ON ctd.CaseTypeDetailId = c.CaseTypeDetailId
				WHERE
					(
						SELECT
							EXTRACT(YEAR_MONTH FROM (`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR))
					) < (
						SELECT
							EXTRACT(YEAR_MONTH FROM (UTC_TIMESTAMP() + INTERVAL ".TIMEZONEOFFSET." HOUR))
					)
					AND c.isDeleted = 0
				GROUP BY
					YEAR (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					MONTH (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					ctl.CaseTypeId
				ORDER BY
					YEAR (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					MONTH (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					Amount DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getWeeklyCases()
	{
		$divideUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		$sql = "SELECT
					`sul`.`SystemUnitName` AS `SystemUnitName`,
					count(1) AS `Amount`,
					concat(
						YEAR (`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR),
						'-',
						WEEK (`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR)
					) AS `ReportDate`
				FROM
					`Cases` `c`
				LEFT JOIN `FunctionUnitDetail` `fud` ON `c`.`FunctionUnitDetailId` = `fud`.`FunctionUnitDetailId`
				LEFT JOIN `FunctionUnit_lkp` `ful` ON `fud`.`FunctionUnitId` = `ful`.`FunctionUnitId`
				LEFT JOIN `SystemUnit_lkp` `sul` ON `ful`.`SystemUnitId` = `sul`.`SystemUnitId`
				WHERE
					`c`.`isDeleted` = 0
				AND `c`.`CreatedDate` >= '$divideUTCTime'
				GROUP BY
					YEARWEEK (
						`c`.`CreatedDate` + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					`ful`.`SystemUnitId`;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getDailyCaseBySystemUnit($systemUnitId = NULL)
	{
		$sql = "SELECT
					sul.SystemUnitName,
					count(1) AS Amount,
					DATE(CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) AS Date
				FROM
					Cases c
				LEFT JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
				LEFT JOIN FunctionUnit_lkp ful ON fud.FunctionUnitId = ful.FunctionUnitId
				LEFT JOIN SystemUnit_lkp sul ON ful.SystemUnitId = sul.SystemUnitId
				WHERE
					c.isDeleted = 0
				AND CreatedDate IS NOT NULL
				GROUP BY
					DATE(CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR),
					ful.SystemUnitId";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseSystemUnit($startDate, $endDate)
	{
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						`sul`.`SystemUnitName` AS `SystemUnitName`,
						`sul`.`SystemUnitId` AS `SystemUnitId`,
						sum(`cr`.`Amount`) AS `Amount`
					FROM
						`CaseReport` `cr`
					JOIN `FunctionUnit_lkp` `ful` ON `cr`.`FunctionUnitId` = `ful`.`FunctionUnitId`
					JOIN `SystemUnit_lkp` `sul` ON `sul`.`SystemUnitId` = `ful`.`SystemUnitId`
					WHERE
						CaseMonth >= '$startDate'
					AND CaseMonth < '$endDate'
					GROUP BY
						`cr`.`CaseMonth`,
						`sul`.`SystemUnitId`
					ORDER BY
						Amount DESC,
						`cr`.`CaseMonth`,
						`ful`.`SystemUnitId`,
						`cr`.`FunctionUnitId`";
	    else
	    	$sql = "SELECT
						`sul`.`SystemUnitName` AS `SystemUnitName`,
						`sul`.`SystemUnitId` AS `SystemUnitId`,
						count(1) AS `Amount`
					FROM
						`Cases` `c`
					JOIN `FunctionUnitDetail` `fud` ON `c`.`FunctionUnitDetailId` = `fud`.`FunctionUnitDetailId`
					JOIN `FunctionUnit_lkp` `ful` ON `fud`.`FunctionUnitId` = `ful`.`FunctionUnitId`
					JOIN `SystemUnit_lkp` `sul` ON `ful`.`SystemUnitId` = `sul`.`SystemUnitId`
					WHERE
						`c`.`isDeleted` = 0
					AND `c`.`CreatedDate` >= '$startDate'
					AND `c`.`CreatedDate` < '$endDate'
					GROUP BY
						`sul`.`SystemUnitId`
					ORDER BY
						count(1) DESC,
						`sul`.`SystemUnitId`";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseFunctionUnit($startDate, $endDate)
	{
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						`sul`.`SystemUnitName` AS `SystemUnitName`,
						`ful`.`FunctionUnit` AS `FunctionUnit`,
						`ful`.`FunctionUnitId` AS `FunctionUnitId`,
						sum(`cr`.`Amount`) AS `Amount`,
						`cr`.`CaseMonth` AS `ReportDate`
					FROM
						`CaseReport` `cr`
					JOIN `FunctionUnit_lkp` `ful` ON `cr`.`FunctionUnitId` = `ful`.`FunctionUnitId`
					JOIN `SystemUnit_lkp` `sul` ON `sul`.`SystemUnitId` = `ful`.`SystemUnitId`
					WHERE
						CaseMonth >= '$startDate'
					AND CaseMonth < '$endDate'
					GROUP BY
						`cr`.`FunctionUnitId`
					ORDER BY
						`ful`.`SystemUnitId`,
						Amount DESC;";
	    else
	    	$sql = "SELECT
						`sul`.`SystemUnitName` AS `SystemUnitName`,
						`ful`.`FunctionUnit` AS `FunctionUnit`,
						`ful`.`FunctionUnitId` AS `FunctionUnitId`,
						count(1) AS `Amount`
					FROM
						`Cases` `c`
					JOIN `FunctionUnitDetail` `fud` ON `c`.`FunctionUnitDetailId` = `fud`.`FunctionUnitDetailId`
					JOIN `FunctionUnit_lkp` `ful` ON `fud`.`FunctionUnitId` = `ful`.`FunctionUnitId`
					JOIN `SystemUnit_lkp` `sul` ON `ful`.`SystemUnitId` = `sul`.`SystemUnitId`
					WHERE
						`c`.`isDeleted` = 0
					AND `c`.`CreatedDate` >= '$startDate'
					AND `c`.`CreatedDate` < '$endDate'
					GROUP BY
						`ful`.`FunctionUnitId`
					ORDER BY
						`ful`.`SystemUnitId`,
						count(1) DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseFunctionUnitBy($id, $startDate, $endDate, $idType)
	{
		switch ($idType) {
			case 1:
				//By Case Type
				$idQuery = "ful.SystemUnitId = $id";
				$idQuery2 = "ful2.SystemUnitId = $id";
				break;
			case 2:
				//By Sub Case Type
				$idQuery = "ful.FunctionUnitId = $id";
				$idQuery2 = "ful2.FunctionUnitId = $id";
				break;
			default:
				$idQuery = "1=1";
				$idQuery2 = "1=1";
				break;
		}
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						sul.SystemUnitName,
						ful.FunctionUnit,
						SUM(Amount) AS Amount
					FROM
						CaseReport cr
					INNER JOIN FunctionUnit_lkp ful ON cr.FunctionUnitId = ful.FunctionUnitId
					INNER JOIN SystemUnit_lkp sul ON ful.SystemUnitId = sul.SystemUnitId
					INNER JOIN CaseType_lkp ctl ON cr.CaseTypeId = ctl.CaseTypeId
					WHERE
						ctl.CaseTypeId = '$ctId'
					AND `cr`.`CaseMonth` >= '$startDate'
					AND `cr`.`CaseMonth` < '$endDate'
					GROUP BY
						cr.FunctionUnitId
					ORDER BY
						Amount DESC";
		else
	        $sql = "SELECT
						`ctd`.`CaseTypeDetailName` AS `CaseTypeDetailName`,
						`ctd`.`CaseTypeDetailId` AS `CaseTypeDetailId`,
						count(1) AS `Amount`
					FROM
						`Cases` `c`
					JOIN `CaseTypeDetail` `ctd` ON `c`.`CaseTypeDetailId` = `ctd`.`CaseTypeDetailId`
					JOIN `CaseType_lkp` `ctl` ON `ctd`.`CaseTypeId` = `ctl`.`CaseTypeId`
					WHERE
						`c`.`isDeleted` = 0
					AND ctl.CaseTypeId = '$ctId'
					AND `c`.`CreatedDate` >= '$startDate'
					AND `c`.`CreatedDate` < '$endDate'
					GROUP BY
						`ctd`.`CaseTypeDetailId`
					ORDER BY
						count(1) DESC,
						`ctd`.`CaseTypeId`";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseSystemUnitDetail($systemUnitId, $startDate, $endDate)
	{
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
					ctl.CaseTypeName AS `SystemUnitDetailName`,
					ctl.CaseTypeId AS `SystemUnitDetailId`,
					SUM(Amount) AS Amount
				FROM
					CaseReport cr
				INNER JOIN FunctionUnit_lkp ful ON cr.FunctionUnitId = ful.FunctionUnitId
				INNER JOIN SystemUnit_lkp sul ON ful.SystemUnitId = sul.SystemUnitId
				INNER JOIN CaseType_lkp ctl ON cr.CaseTypeId = ctl.CaseTypeId
				WHERE
					sul.SystemUnitId = $systemUnitId
				AND cr.CaseMonth >= '$startDate'
				AND cr.CaseMonth < '$endDate'
				GROUP BY
					cr.CaseTypeId
				ORDER BY
					Amount DESC";
		else
			$sql = "SELECT
						`ful`.`FunctionUnit` AS `SystemUnitDetailName`,
						`ful`.`FunctionUnitId` AS `SystemUnitDetailId`,
						count(1) AS `Amount`
					FROM
						`Cases` `c`
					JOIN `FunctionUnitDetail` `fud` ON `c`.`FunctionUnitDetailId` = `fud`.`FunctionUnitDetailId`
					JOIN `FunctionUnit_lkp` `ful` ON `fud`.`FunctionUnitId` = `ful`.`FunctionUnitId`
					JOIN `CaseTypeDetail` `ctd` ON `c`.`CaseTypeDetailId` = `ctd`.`CaseTypeDetailId`
					JOIN `CaseType_lkp` `ctl` ON `ctd`.`CaseTypeId` = `ctl`.`CaseTypeId`
					WHERE
						`c`.`isDeleted` = 0
					AND `c`.`CreatedDate` >= '$startDate'
					AND `c`.`CreatedDate` < '$endDate'
					AND `ful`.`SystemUnitId` = '$systemUnitId'
					GROUP BY
						`ful`.`FunctionUnitId`
					ORDER BY
						Amount DESC,
						`ful`.`FunctionUnitId`";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseFunctionUnitDetail($functionUnitId, $startDate, $endDate)
	{
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						ctl.CaseTypeName AS FunctionUnitDetailName,
						ctl.CaseTypeId AS FunctionUnitDetailId,
						SUM(Amount) AS Amount
					FROM
						CaseReport cr
					INNER JOIN FunctionUnit_lkp ful ON cr.FunctionUnitId = ful.FunctionUnitId
					INNER JOIN CaseType_lkp ctl ON cr.CaseTypeId = ctl.CaseTypeId
					WHERE
						ful.FunctionUnitId = '$functionUnitId'
					AND cr.CaseMonth >= '$startDate'
					AND cr.CaseMonth < '$endDate'
					GROUP BY
						cr.CaseTypeId
					ORDER BY
						Amount DESC";
		else
			$sql = "SELECT
						`fud`.`FunctionUnitDetailName` AS `FunctionUnitDetailName`,
						`fud`.`FunctionUnitDetailId` AS `FunctionUnitDetailId`,
						count(1) AS `Amount`
					FROM
						`Cases` `c`
					JOIN `FunctionUnitDetail` `fud` ON `c`.`FunctionUnitDetailId` = `fud`.`FunctionUnitDetailId`
					JOIN `FunctionUnit_lkp` `ful` ON `fud`.`FunctionUnitId` = `ful`.`FunctionUnitId`
					JOIN `SystemUnit_lkp` `sul` ON `ful`.`SystemUnitId` = `sul`.`SystemUnitId`
					JOIN `CaseTypeDetail` `ctd` ON `c`.`CaseTypeDetailId` = `ctd`.`CaseTypeDetailId`
					JOIN `CaseType_lkp` `ctl` ON `ctd`.`CaseTypeId` = `ctl`.`CaseTypeId`
					WHERE
						`c`.`isDeleted` = 0
					AND `c`.`CreatedDate` >= '$startDate'
					AND `c`.`CreatedDate` < '$endDate'
					AND `ful`.`FunctionUnitId` = '$functionUnitId'
					GROUP BY
						`fud`.`FunctionUnitDetailId`
					ORDER BY
						`ful`.`SystemUnitId`,
						Amount DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseSubFunctionUnitDetail($id, $startDate, $endDate, $isFunctionUnitId)
	{
		$idQuery = $isFunctionUnitId ? "fud.FunctionUnitId = $id" : "fud.FunctionUnitDetailId = $id";
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						ctl.CaseTypeName,
						ctl.CaseTypeId,
						SUM(Amount) AS Amount
					FROM
						CaseReport cr
					INNER JOIN FunctionUnitDetail fud ON cr.FunctionUnitId = fud.FunctionUnitId
					INNER JOIN CaseType_lkp ctl ON cr.CaseTypeId = ctl.CaseTypeId
					WHERE
						$idQuery
					AND cr.CaseMonth >= '$startDate'
					AND cr.CaseMonth < '$endDate'
					GROUP BY
						cr.CaseTypeId
					ORDER BY
						Amount DESC";
		else
			$sql = "SELECT
						ctl.CaseTypeName,
						ctl.CaseTypeId,
						count(1) AS Amount
					FROM
						Cases c
					INNER JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
					INNER JOIN CaseTypeDetail ctd ON c.CaseTypeDetailId = ctd.CaseTypeDetailId
					INNER JOIN CaseType_lkp ctl ON ctd.CaseTypeId = ctl.CaseTypeId
					WHERE
						$idQuery
					AND c.isDeleted = 0
					AND c.CreatedDate > '$startDate'
					AND c.CreatedDate < '$endDate'
					GROUP BY
						ctl.CaseTypeId
					ORDER BY
						Amount DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseSubFunctionUnitList($id, $startDate, $endDate, $isFunctionUnitId)
	{
		$idQuery = $isFunctionUnitId ? "fud.FunctionUnitId = $id" : "fud.FunctionUnitDetailId = $id";
		$id2Query = $isFunctionUnitId ? "fud2.FunctionUnitId = $id" : "fud2.FunctionUnitDetailId = $id";
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						ctl.CaseTypeName,
						ctl.CaseTypeId,
						SUM(Amount) AS Amount
					FROM
						CaseReport cr
					INNER JOIN FunctionUnitDetail fud ON cr.FunctionUnitId = fud.FunctionUnitId
					INNER JOIN CaseType_lkp ctl ON cr.CaseTypeId = ctl.CaseTypeId
					WHERE
						$idQuery
					AND cr.CaseMonth >= '$startDate'
					AND cr.CaseMonth < '$endDate'
					GROUP BY
						cr.CaseTypeId
					ORDER BY
						Amount DESC";
		else
			$sql = "SELECT
				ctl.CaseTypeName,
				ctl.CaseTypeId,
				ctd.CaseTypeDetailName,
				ctd.CaseTypeDetailId,
				COUNT(1) AS Amount
			FROM
				Cases c
			INNER JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
			INNER JOIN CaseTypeDetail ctd ON c.CaseTypeDetailId = ctd.CaseTypeDetailId
			INNER JOIN CaseType_lkp ctl ON ctd.CaseTypeId = ctl.CaseTypeId
			LEFT JOIN (
				SELECT
					ctl2.CaseTypeId,
					COUNT(1) AS CTAmount
				FROM
					Cases c2
				INNER JOIN FunctionUnitDetail fud2 ON c2.FunctionUnitDetailId = fud2.FunctionUnitDetailId
				INNER JOIN CaseTypeDetail ctd2 ON c2.CaseTypeDetailId = ctd2.CaseTypeDetailId
				INNER JOIN CaseType_lkp ctl2 ON ctd2.CaseTypeId = ctl2.CaseTypeId
				WHERE
					$id2Query
				AND c2.isDeleted = 0
				AND c2.CreatedDate > '$startDate'
				AND c2.CreatedDate < '$endDate'
				GROUP BY
					ctl2.CaseTypeId
			) T ON ctl.CaseTypeId = T.CaseTypeId
			WHERE
				$idQuery
			AND c.isDeleted = 0
			AND c.CreatedDate > '$startDate'
			AND c.CreatedDate < '$endDate'
			GROUP BY
				ctd.CaseTypeDetailId
			ORDER BY
				T.CTAmount DESC,
				T.casetypeid,
				Amount DESC,
				ctl.CaseTypeId";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseTypeBy($id, $startDate, $endDate, $idType)
	{
		switch ($idType) {
			case 1:
				//By System Unit
				$idQuery = "ful.SystemUnitId = $id";
				$idQuery2 = "ful2.SystemUnitId = $id";
				break;
			case 2:
				//By Function Unit
				$idQuery = "ful.FunctionUnitId = $id";
				$idQuery2 = "ful2.FunctionUnitId = $id";
				break;
			case 3:
				//By Sub Function Unit
				$idQuery = "fud.FunctionUnitDetailId = $id";
				$idQuery2 = "fud2.FunctionUnitDetailId = $id";
				break;
			
			default:
				$idQuery = "1=1";
				$idQuery2 = "1=1";
				break;
		}
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						ctl.CaseTypeName,
						ctl.CaseTypeId,
						SUM(Amount) AS Amount
					FROM
						CaseReport cr
					INNER JOIN FunctionUnitDetail fud ON cr.FunctionUnitId = fud.FunctionUnitId
					INNER JOIN FunctionUnit_lkp ful ON fud.FunctionUnitId = ful.functionUnitId
					INNER JOIN CaseType_lkp ctl ON cr.CaseTypeId = ctl.CaseTypeId
					WHERE
						$idQuery
					AND cr.CaseMonth >= '$startDate'
					AND cr.CaseMonth < '$endDate'
					GROUP BY
						cr.CaseTypeId
					ORDER BY
						Amount DESC";
		else
			$sql = "SELECT
				ctl.CaseTypeName,
				ctl.CaseTypeId,
				ctd.CaseTypeDetailName,
				ctd.CaseTypeDetailId,
				COUNT(1) AS Amount
			FROM
				Cases c
			INNER JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
			INNER JOIN FunctionUnit_lkp ful ON fud.FunctionUnitId = ful.functionUnitId
			INNER JOIN CaseTypeDetail ctd ON c.CaseTypeDetailId = ctd.CaseTypeDetailId
			INNER JOIN CaseType_lkp ctl ON ctd.CaseTypeId = ctl.CaseTypeId
			LEFT JOIN (
				SELECT
					ctl2.CaseTypeId,
					COUNT(1) AS CTAmount
				FROM
					Cases c2
				INNER JOIN FunctionUnitDetail fud2 ON c2.FunctionUnitDetailId = fud2.FunctionUnitDetailId
				INNER JOIN FunctionUnit_lkp ful2 ON fud2.FunctionUnitId = ful2.functionUnitId
				INNER JOIN CaseTypeDetail ctd2 ON c2.CaseTypeDetailId = ctd2.CaseTypeDetailId
				INNER JOIN CaseType_lkp ctl2 ON ctd2.CaseTypeId = ctl2.CaseTypeId
				WHERE
					$idQuery2
				AND c2.isDeleted = 0
				AND c2.CreatedDate > '$startDate'
				AND c2.CreatedDate < '$endDate'
				GROUP BY
					ctl2.CaseTypeId
			) T ON ctl.CaseTypeId = T.CaseTypeId
			WHERE
				$idQuery
			AND c.isDeleted = 0
			AND c.CreatedDate > '$startDate'
			AND c.CreatedDate < '$endDate'
			GROUP BY
				ctd.CaseTypeDetailId
			ORDER BY
				T.CTAmount DESC,
				T.casetypeid,
				Amount DESC,
				ctl.CaseTypeId";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseSourceBy($id, $idType, $startDate, $endDate){
		switch ($idType) {
			case 1:
				# by case type
				$idQuery = "CaseTypeId = $id";
				break;
			case 2:
				# by sub case type
				$idQuery = "c.CaseTypeDetailId = $id";
				break;
			default:
				$idQuery = '1=1';
				break;
		}

		$sql = "SELECT
					suc.*,
					fuc.FunctionUnitId,
					fuc.FunctionUnit AS FunctionUnitName,
					fuc.FunctionUnitCount
				FROM
					(
						SELECT
							ful.SystemUnitId,
							sul.SystemUnitName,
							COUNT(1) AS SystemUnitCount
						FROM
							Cases c
						INNER JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
						INNER JOIN FunctionUnit_lkp ful ON fud.FunctionUnitId = ful.FunctionUnitId
						INNER JOIN SystemUnit_lkp sul ON ful.SystemUnitId = sul.SystemUnitId
						INNER JOIN CaseTypeDetail ctd ON c.CaseTypeDetailId = ctd.CaseTypeDetailId
						WHERE
							$idQuery
						AND isDeleted = 0
						AND c.CreatedDate > '$startDate'
						AND c.CreatedDate < '$endDate'
						GROUP BY
							ful.SystemUnitId
						ORDER BY
							SystemUnitCount DESC,
							sul.SystemUnitId
					) AS suc
				INNER JOIN (
					SELECT
						ful.SystemUnitId,
						ful.FunctionUnitId,
						ful.FunctionUnit,
						COUNT(1) AS FunctionUnitCount
					FROM
						Cases c
					INNER JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
					INNER JOIN FunctionUnit_lkp ful ON fud.FunctionUnitId = ful.FunctionUnitId
					INNER JOIN CaseTypeDetail ctd ON c.CaseTypeDetailId = ctd.CaseTypeDetailId
					WHERE
						$idQuery
					AND isDeleted = 0
					AND c.CreatedDate > '$startDate'
					AND c.CreatedDate < '$endDate'
					GROUP BY
						fud.FunctionUnitId
					ORDER BY
						ful.SystemUnitId,
						FunctionUnitCount DESC
				) AS fuc ON suc.SystemUnitId = fuc.SystemUnitId
				ORDER BY
					suc.SystemUnitCount DESC,
					fuc.FunctionUnitCount DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseType($startDate, $endDate)
	{
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));

		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						`ctl`.`CaseTypeName` AS `CaseTypeName`,
						`ctl`.`CaseTypeId` AS `CaseTypeId`,
						sum(`cr`.`Amount`) AS `Amount`
					FROM
						`CaseReport` `cr`
					JOIN `CaseType_lkp` `ctl` ON `cr`.`CaseTypeId` = `ctl`.`CaseTypeId`
					WHERE
						`cr`.`CaseMonth` >= '$startDate'
					AND `cr`.`CaseMonth` < '$endDate'
					GROUP BY
						`cr`.`CaseMonth`,
						`cr`.`CaseTypeId`
					ORDER BY
						sum(`cr`.`Amount`) DESC";
		else
			$sql = "SELECT
						`ctl`.`CaseTypeName` AS `CaseTypeName`,
						`ctl`.`CaseTypeId` AS `CaseTypeId`,
						count(1) AS `Amount`
					FROM
						`Cases` `c`
					JOIN `CaseTypeDetail` `ctd` ON `c`.`CaseTypeDetailId` = `ctd`.`CaseTypeDetailId`
					JOIN `CaseType_lkp` `ctl` ON `ctd`.`CaseTypeId` = `ctl`.`CaseTypeId`
					WHERE
						`c`.`isDeleted` = 0
					AND `c`.`CreatedDate` >= '$startDate'
					AND `c`.`CreatedDate` < '$endDate'
					GROUP BY
						`ctd`.`CaseTypeId`
					ORDER BY
						count(1) DESC,
						`ctd`.`CaseTypeId`";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseTypeDetail($ctId, $startDate, $endDate)
	{
		$newDataUTCTime = mdate('%Y-%m-%dT%H:%i:%sZ', local_to_gmt(strtotime(NEWDATATIME)));
		if($endDate <= $newDataUTCTime)
			$sql = "SELECT
						sul.SystemUnitName,
						ful.FunctionUnit,
						SUM(Amount) AS Amount
					FROM
						CaseReport cr
					INNER JOIN FunctionUnit_lkp ful ON cr.FunctionUnitId = ful.FunctionUnitId
					INNER JOIN SystemUnit_lkp sul ON ful.SystemUnitId = sul.SystemUnitId
					INNER JOIN CaseType_lkp ctl ON cr.CaseTypeId = ctl.CaseTypeId
					WHERE
						ctl.CaseTypeId = '$ctId'
					AND `cr`.`CaseMonth` >= '$startDate'
					AND `cr`.`CaseMonth` < '$endDate'
					GROUP BY
						cr.FunctionUnitId
					ORDER BY
						Amount DESC";
		else
	        $sql = "SELECT
						`ctd`.`CaseTypeDetailName` AS `CaseTypeDetailName`,
						`ctd`.`CaseTypeDetailId` AS `CaseTypeDetailId`,
						count(1) AS `Amount`
					FROM
						`Cases` `c`
					JOIN `CaseTypeDetail` `ctd` ON `c`.`CaseTypeDetailId` = `ctd`.`CaseTypeDetailId`
					JOIN `CaseType_lkp` `ctl` ON `ctd`.`CaseTypeId` = `ctl`.`CaseTypeId`
					WHERE
						`c`.`isDeleted` = 0
					AND ctl.CaseTypeId = '$ctId'
					AND `c`.`CreatedDate` >= '$startDate'
					AND `c`.`CreatedDate` < '$endDate'
					GROUP BY
						`ctd`.`CaseTypeDetailId`
					ORDER BY
						count(1) DESC,
						`ctd`.`CaseTypeId`";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseOverview($date)
	{
		$sql = "SELECT
                    `sul`.`SystemUnitName` AS `SystemUnitName`,
                    `ful`.`FunctionUnit` AS `FunctionUnit`,
                    `ctl`.`CaseTypeName` AS `CaseTypeName`,
                    `cr`.`Amount` AS `Amount`,
                    `cr`.`CaseMonth` AS `ReportDate`
                FROM
                    (
                        (
                            (
                                `CaseReport` `cr`
                                JOIN `CaseType_lkp` `ctl` ON (
                                    (
                                        `ctl`.`CaseTypeId` = `cr`.`CaseTypeId`
                                    )
                                )
                            )
                            JOIN `FunctionUnit_lkp` `ful` ON (
                                (
                                    `cr`.`FunctionUnitId` = `ful`.`FunctionUnitId`
                                )
                            )
                        )
                        JOIN `SystemUnit_lkp` `sul` ON (
                            (
                                `sul`.`SystemUnitId` = `ful`.`SystemUnitId`
                            )
                        )
                    )
                WHERE
                    YEAR ('".$date."') = YEAR (CaseMonth)
                AND MONTH ('".$date."') = MONTH (CaseMonth)
                ORDER BY
                    `cr`.`CaseMonth`,
                    `sul`.`SystemUnitName`,
                    `ful`.`FunctionUnit`,
                    `cr`.`Amount` DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseListOverview($startDate, $endDate)
	{
		$sql = "SELECT
					c.CaseId,
					c.SalesforceId,
					c.CaseNumber,
					c.CreatedBy,
					c.CreatedDate,
					c.ResponseDate,
					ROUND(TIME_TO_SEC(TIMEDIFF(c.ResponseDate, c.CreatedDate))/60) As ResponseMinute,
					c.CaseHour,
					c.`Owner`,
					c.`Status`,
					c.`Subject`,
					c.Description,
					c.CaseTypeDetailId,
					ctd.CaseTypeDetailName,
					c.FunctionUnitDetailId,
					fud.FunctionUnitDetailName
				FROM
					Cases c
				LEFT JOIN CaseTypeDetail ctd ON c.CaseTypeDetailId = ctd.CaseTypeDetailId
				LEFT JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
				WHERE
					c.CreatedDate > '$startDate'
				AND c.CreatedDate < '$endDate'
				AND c.isDeleted = 0
				ORDER BY
					CASE
				WHEN c.`Status` = 'closed' THEN
					4
				WHEN c.`Status` = 'In Progress' THEN
					1
				WHEN c.`Status` = 'new' THEN
					2
				ELSE
					3
				END,
				 c.CreatedDate DESC,
				 c.CaseNumber DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseAgeAverageByHour($startDate, $endDate)
	{
		//$maxCaseAge = 72;	//assume max case age to be 3*24 hours
		$sql = "SELECT
					ROUND(
						AVG(
							TIMESTAMPDIFF(
								MINUTE,
								c.CreatedDate,
								c.ClosedDate
							)
						),
						1
					) AS CaseAge,
					HOUR (
						c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
					) AS CreatedHour
				FROM
					Cases c
				WHERE
					c.ClosedDate IS NOT NULL
				AND c.SalesforceId IS NOT NULL
				AND c.CreatedDate > '$startDate'
				AND c.ClosedDate < '$endDate'
				AND c.isDeleted = 0
				GROUP BY
					HOUR (
						c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
					)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseAgeAverageByWeekDay($startDate, $endDate)
	{
		//$maxCaseAge = 72;	//assume max case age to be 3*24 hours
		$sql = "SELECT
					ROUND(
						AVG(
							TIMESTAMPDIFF(
								MINUTE,
								c.CreatedDate,
								c.ClosedDate
							)
						),
						1
					) AS CaseAge,
					WEEKDAY (
						c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
					) AS CreatedHour
				FROM
					Cases c
				WHERE
					c.ClosedDate IS NOT NULL
				AND c.SalesforceId IS NOT NULL
				AND c.CreatedDate > '$startDate'
				AND c.ClosedDate < '$endDate'
				AND c.isDeleted = 0
				GROUP BY
					WEEKDAY (
						c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
					)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseAgeAverageByYearMonth()
	{
		//$maxCaseAge = 72;	//assume max case age to be 3*24 hours
		$sql = "SELECT
					ROUND(
						AVG(
							TIMESTAMPDIFF(
								MINUTE,
								c.CreatedDate,
								c.ClosedDate
							)
						),
						1
					) AS CaseAge,
					CONCAT(
						YEAR (
							c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						'-',
						MONTH (
							c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
						),
						'-1'
					) AS CaseYearMonth
				FROM
					Cases c
				WHERE
					c.ClosedDate IS NOT NULL
				AND c.SalesforceId IS NOT NULL
				AND c.isDeleted = 0
				GROUP BY
					YEAR (
						c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
					),
					MONTH (
						c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR
					)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseAgeDistribution($startDate, $endDate, $distributionIntervals)
	{
		$sql = "";
		foreach ($distributionIntervals as $interval) {
			if($sql=="")
				$sql.= "SELECT
							COUNT(1) AS Amount,
							'$interval[0]h ~ $interval[1]h' AS Criteria
						FROM
							Cases c
						WHERE
							c.ClosedDate IS NOT NULL
						AND c.isDeleted = 0
						AND c.SalesforceId IS NOT NULL
						AND c.CreatedDate > '$startDate'
						AND c.ClosedDate < '$endDate'
						AND TIMESTAMPDIFF(
							HOUR,
							c.CreatedDate,
							c.ClosedDate
						) >= $interval[0]
						AND TIMESTAMPDIFF(
							HOUR,
							c.CreatedDate,
							c.ClosedDate
						) < $interval[1]";
			else
				$sql.= "
						UNION
						SELECT
							COUNT(1) AS Amount,
							'$interval[0]h ~ $interval[1]h' AS Criteria
						FROM
							Cases c
						WHERE
							c.ClosedDate IS NOT NULL
						AND c.isDeleted = 0
						AND c.SalesforceId IS NOT NULL
						AND c.CreatedDate > '$startDate'
						AND c.ClosedDate < '$endDate'
						AND TIMESTAMPDIFF(
							HOUR,
							c.CreatedDate,
							c.ClosedDate
						) >= $interval[0]
						AND TIMESTAMPDIFF(
							HOUR,
							c.CreatedDate,
							c.ClosedDate
						) < $interval[1]";
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseCreatedSummaryByHour($startDate, $endDate)
	{
		$sql = "SELECT
					COUNT(1) AS Amount,
					HOUR (c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) AS CreatedHour
				FROM
					Cases c
				WHERE
					c.ClosedDate IS NOT NULL
					AND c.isDeleted = 0
					AND c.SalesforceId IS NOT NULL
					AND c.CreatedDate > '$startDate'
					AND c.ClosedDate < '$endDate'
				GROUP BY
					HOUR (c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseCreatedSummaryByWeekDay($startDate, $endDate)
	{
		$sql = "SELECT
					COUNT(1) AS Amount,
					WEEKDAY (c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) AS CreatedHour
				FROM
					Cases c
				WHERE
					c.ClosedDate IS NOT NULL
					AND c.isDeleted = 0
					AND c.SalesforceId IS NOT NULL
					AND c.CreatedDate > '$startDate'
					AND c.ClosedDate < '$endDate'
				GROUP BY
					WEEKDAY (c.CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseClosedSummaryByHour($startDate, $endDate)
	{
		$sql = "SELECT
					COUNT(1) AS Amount,
					HOUR (c.ClosedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) AS ClosedHour
				FROM
					Cases c
				WHERE
					c.ClosedDate IS NOT NULL
					AND c.isDeleted = 0
					AND c.SalesforceId IS NOT NULL
					AND c.CreatedDate > '$startDate'
					AND c.ClosedDate < '$endDate'
				GROUP BY
					HOUR (c.ClosedDate + INTERVAL ".TIMEZONEOFFSET." HOUR)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseClosedSummaryByWeekDay($startDate, $endDate)
	{
		$sql = "SELECT
					COUNT(1) AS Amount,
					WEEKDAY (c.ClosedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) AS ClosedHour
				FROM
					Cases c
				WHERE
					c.ClosedDate IS NOT NULL
					AND c.isDeleted = 0
					AND c.SalesforceId IS NOT NULL
					AND c.CreatedDate > '$startDate'
					AND c.ClosedDate < '$endDate'
				GROUP BY
					WEEKDAY (c.ClosedDate + INTERVAL ".TIMEZONEOFFSET." HOUR)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseWay($startDate, $endDate){
		$sql = "SELECT
					*
				FROM
					(
						SELECT
							COUNT(1) AS OtherCaseSourceAmount
						FROM
							Cases c
						WHERE
							SalesforceId IS NULL
						AND c.isDeleted = 0
						AND HOUR (CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) > '$startDate'
						AND HOUR (CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) < '$endDate'
					) AS O
				JOIN (
					SELECT
						COUNT(1) AS SalesforceCaseSourceAmount
					FROM
						Cases
					WHERE
						SalesforceId IS NOT NULL
					AND isDeleted = 0
					AND HOUR (CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) > '$startDate'
					AND HOUR (CreatedDate + INTERVAL ".TIMEZONEOFFSET." HOUR) < '$endDate'
				) AS SF";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getEmergencyCaseList($startDate, $endDate)
	{
		$sql = "SELECT
					ec.CaseId,
					ec.CaseSubject,
					ec.CaseReason,
					ec.Incidence,
					ec.StartDate,
					ec.EndDate
				FROM
					EmergencyCase ec
				WHERE
					ec.StartDate >= '$startDate'
				AND ec.StartDate < '$endDate'
				ORDER BY
					ec.StartDate";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getDailyCaseAmount()
	{
		$sql = "SELECT count(1) AS Amount,DATE(CONVERT_TZ(CreatedDate, '+0:00', '+".TIMEZONEOFFSET.":00')) AS Date FROM Cases WHERE isDeleted = 0 AND CreatedDate IS NOT NULL GROUP BY DATE(CONVERT_TZ(CreatedDate, '+0:00', '+".TIMEZONEOFFSET.":00'))";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseTypeFilter()
	{
		$sql = "select `CaseType_lkp`.`CaseTypeId` AS `CaseTypeId`,`CaseType_lkp`.`CaseTypeName` AS `CaseTypeName` from `CaseType_lkp` order by `CaseType_lkp`.`CaseTypeId`";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getFunctionUnitFilter()
	{
		$sql = "select `sul`.`SystemUnitName` AS `SystemUnitName`,`ful`.`FunctionUnitId` AS `FunctionUnitId`,`ful`.`FunctionUnit` AS `FunctionUnit` from (`FunctionUnit_lkp` `ful` join `SystemUnit_lkp` `sul` on((`ful`.`SystemUnitId` = `sul`.`SystemUnitId`))) order by `sul`.`SystemUnitName`,`ful`.`FunctionUnitId`";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getSystemUnitFilter()
	{
		$sql = "select `SystemUnit_lkp`.`SystemUnitId` AS `SystemUnitId`,`SystemUnit_lkp`.`SystemUnitName` AS `SystemUnitName` from `SystemUnit_lkp` order by `SystemUnit_lkp`.`SystemUnitName`";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function upsertCase($data, $actionType)
	{
		$insertArray = array();
		$updateArray = array();
		switch(strtolower($actionType))
		{
			case 'insert':
				$insertData['CaseNumber'] = '';
				$insertData['Status'] = 'Closed';
				$insertData['Owner'] = 'Tech Support';
				$insertData['Subject'] = $data->Subject;
				$insertData['Description'] = $data->Description;
				$insertData['CreatedBy'] = 'ManualAdd';
				$insertData['CreatedDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->CaseDate)));
				$insertData['ResponseDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->ResponseDate)));
				$insertData['ClosedDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->CaseDate)));
				$insertData['CaseHour'] = (float)$data->CaseAge;
				$insertData['CaseTypeDetailId'] = $data->SubCaseType;
				$insertData['FunctionUnitDetailId'] = $data->SubFunctionUnit;
				$insertData['InsertDate'] = date("Y-m-d H:i:s",local_to_gmt(time()));
				$insertData['InsertBy'] = 'ManualAdd';
				$insertData['UpdateDate'] = date("Y-m-d H:i:s",local_to_gmt(time()));
				$insertData['UpdateBy'] = 'ManualAdd';

				for($i=0;$i<$data->Amount;$i++)
				{
					array_push($insertArray, $insertData);
				}
			break;
			case 'update':
				$updateData['Owner'] = 'Tech Support';
				$updateData['UpdateDate'] = date("Y-m-d H:i:s",local_to_gmt(time()));
				$updateData['UpdateBy'] = 'ManualAdd';
				$updateData['CaseId'] = $data->CaseId;
				if(property_exists($data, 'Subject'))
					$updateData['Subject'] = $data->Subject;
				if(property_exists($data, 'Description'))
					$updateData['Description'] = $data->Description;
				if(property_exists($data, 'CaseDate'))
					$updateData['CreatedDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->CaseDate)));
				if(property_exists($data, 'ResponseDate'))
					$updateData['ResponseDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->ResponseDate)));
				if(property_exists($data, 'CaseAge'))
					$updateData['CaseHour'] = (float)$data->CaseAge;
				if(property_exists($data, 'SubCaseType'))
					$updateData['CaseTypeDetailId'] = $data->SubCaseType;
				if(property_exists($data, 'SubFunctionUnit'))
					$updateData['FunctionUnitDetailId'] = $data->SubFunctionUnit;
				array_push($updateArray, $updateData);
			break;
		}

		$this->db->trans_start();

		if(count($insertArray)>0)
			$result = $this->db->insert_batch('Cases', $insertArray);

		if(count($updateArray)>0)
			$result = $this->db->update_batch('Cases', $updateArray, 'CaseId');

		$this->db->trans_complete();

		return $result;
	}

	public function upsertEmergencyCase($data, $actionType)
	{
		$insertArray = array();
		$updateArray = array();
		switch(strtolower($actionType))
		{
			case 'insert':
				$insertData['CaseSubject'] = $data->title;
				$insertData['CaseReason'] = $data->reason;
				$insertData['Incidence'] = $data->incidence;
				$insertData['StartDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->startDate)));
				$insertData['EndDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->endDate)));
				$insertData['InsertDate'] = date("Y-m-d H:i:s",local_to_gmt(time()));
				$insertData['InsertBy'] = 'TechSupport';
				$insertData['UpdateDate'] = date("Y-m-d H:i:s",local_to_gmt(time()));
				$insertData['UpdateBy'] = 'TechSupport';
				array_push($insertArray, $insertData);
			break;
			case 'update':
				$updateData['CaseId'] = $data->id;
				if(property_exists($data, 'title'))
					$updateData['CaseSubject'] = $data->title;
				if(property_exists($data, 'reason'))
					$updateData['CaseReason'] = $data->reason;
				if(property_exists($data, 'incidence'))
					$updateData['Incidence'] = $data->incidence;
				if(property_exists($data, 'startDate'))
					$updateData['StartDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->startDate)));
				if(property_exists($data, 'endDate'))
					$updateData['EndDate'] = date("Y-m-d H:i:s",local_to_gmt(strtotime($data->endDate)));
				$updateData['UpdateDate'] = date("Y-m-d H:i:s",local_to_gmt(time()));
				$updateData['UpdateBy'] = 'TechSupport';
				array_push($updateArray, $updateData);
			break;
		}
		$this->db->trans_start();

		if(count($insertArray)>0)
		{
			$this->db->insert_batch('EmergencyCase', $insertArray);
			$result = $this->db->insert_id();
		}

		if(count($updateArray)>0)
			$result = $this->db->update_batch('EmergencyCase', $updateArray, 'CaseId');

		$this->db->trans_complete();

		return $result;
	}

	public function upsertCaseReport($caseDate, $caseTypeId, $functionUnitId, $amount)
	{
		$sql = "SELECT CaseReportId 
                FROM CaseReport 
                WHERE FunctionUnitId = $functionUnitId 
                AND CaseTypeId = $caseTypeId
                AND YEAR('$caseDate') = YEAR(CaseMonth)
                AND Month('$caseDate') = MONTH(CaseMonth);";
		$query = $this->db->query($sql);
		$results = $query->result_array();
        
        $id = 'NULL';
        if(count($results) > 0)
            $id = $results[0]['CaseReportId'];
        
        $sql = "INSERT INTO `CaseReport`
        		VALUES($id, $caseTypeId, $functionUnitId, $amount, NULL, '$caseDate')
                ON DUPLICATE KEY UPDATE
                `Amount` = `Amount` + $amount;";
		$query = $this->db->query($sql);
        
		return $query;
	}

	public function getTopCaseTypeList($caseTypeId, $startDate, $endDate){
		$sql = "SELECT
					ctl.CaseTypeName,
					ctl.CaseTypeId,
					ctd.CaseTypeDetailName,
					ctd.CaseTypeDetailId,
					COUNT(1) AS Amount
				FROM
					CaseTypeDetail ctd
				LEFT JOIN CaseType_lkp ctl ON ctd.CaseTypeId = ctl.CaseTypeId
				LEFT JOIN Cases c ON ctd.CaseTypeDetailId = c.CaseTypeDetailId
				WHERE
					c.InsertDate > '$startDate'
				AND c.InsertDate < '$endDate'
				AND c.isDeleted = 0
				AND ctl.CaseTypeId = '$caseTypeId'
				GROUP BY
					c.CaseTypeDetailId
				ORDER BY
					Amount DESC
				LIMIT 0,
				 10";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCaseAmount($startDate, $endDate)
	{
		$sql = "SELECT count(1) AS Amount,DATE(CONVERT_TZ(CreatedDate, '+0:00', '+".TIMEZONEOFFSET.":00')) AS Date FROM Cases WHERE CreatedDate >= '$startDate' AND CreatedDate < '$endDate' AND isDeleted = 0 GROUP BY DATE(CONVERT_TZ(CreatedDate, '+0:00', '+".TIMEZONEOFFSET.":00'))";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getTotalCaseAmount()
	{
		$sql = "SELECT Count(1) AS Amount FROM Cases WHERE isDeleted = 0;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getLastSyncSalesforceCaseDate()
	{
		$sql = "SELECT ConfigurationValue AS SyncDate FROM Configuration where ConfigurationId = 1;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getOpenCaseAmount()
	{
		$sql = "SELECT Count(1) AS Amount FROM Cases WHERE (Status!='Closed' or Status IS NULL) AND isDeleted = 0;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getOpenCaseDetail()
	{
		$sql = "SELECT
					c.CaseId,
					c.SalesforceId,
					c.CaseNumber,
					c.CreatedBy,
					c.CreatedDate,
					c.`Owner`,
					c.`Status`,
					c.`Subject`,
					c.Description,
					c.CaseTypeDetailId,
					ctd.CaseTypeDetailName,
					c.FunctionUnitDetailId,
					fud.FunctionUnitDetailName
				FROM
					Cases c
				LEFT JOIN CaseTypeDetail ctd ON c.CaseTypeDetailId = ctd.CaseTypeDetailId
				LEFT JOIN FunctionUnitDetail fud ON c.FunctionUnitDetailId = fud.FunctionUnitDetailId
				WHERE
					(
						c.`Status` != 'Closed'
						OR c.`Status` IS NULL
					)
				AND c.`isDeleted` = 0
				ORDER BY
					CASE
				WHEN c.`Status` = 'In Progress' THEN
					1
				WHEN c.`Status` = 'new' THEN
					2
				ELSE
					3
				END,
				 CreatedDate DESC;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getMemberAlias()
	{
		$sql = "SELECT Alias FROM Members";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getMemberinfo()
	{
		$sql = "SELECT * FROM Members";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getSFCaseId($startDate, $endDate)
	{
		$sql = "SELECT DISTINCT SalesforceId FROM Cases WHERE SalesforceId IS NOT NULL AND CreatedDate >= '$startDate' AND CreatedDate < '$endDate';";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getSFSimpleCaseId($startDate, $endDate)
	{
		$sql = "SELECT DISTINCT SalesforceId FROM Cases WHERE SalesforceId IS NOT NULL AND (Subject IS NULL OR Description IS NULL) AND CreatedDate >= '$startDate' AND CreatedDate < '$endDate';";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function insertCaseOnSF($array)
	{
		$values = '';

		try
		{
			/*
			foreach ($array as $a) {
				if($values=='')
					$values.="(NULL, ".$a[0].", ".$a[1].", ".$a[2].", ".$a[3].", NULL, NULL, ".$a[4].", ".$a[5].", ".$a[6].", NULL, NULL, '".gmdate("Y-m-d H:i:s")."', 'ImportFromSF', '".gmdate("Y-m-d H:i:s")."', 'ImportFromSF', 0)";
				else
					$values.=", (NULL, ".$a[0].", ".$a[1].", ".$a[2].", ".$a[3].", NULL, NULL, ".$a[4].", ".$a[5].", ".$a[6].", NULL, NULL, '".gmdate("Y-m-d H:i:s")."', 'ImportFromSF', '".gmdate("Y-m-d H:i:s")."', 'ImportFromSF', 0)";
			}*/
			$this->db->insert_batch('Cases', $array);
			$sql = "UPDATE Configuration SET ConfigurationValue = '".gmdate("Y-m-d H:i:s")."' WHERE ConfigurationId = 1;";
			$query = $this->db->query($sql);
			return $query;
		}
		catch(Exception $e)
		{
			print $e->getMessage();
		}
	}

	public function updateCaseOnSF($array)
	{
		$data = array();
		try
		{
			/*
			$sql = '';
			foreach ($array as $a) {
				$sql.="UPDATE Cases SET CaseNumber = ".$a[1].", Status = ".$a[2].", Owner = ".$a[3].", Subject = NULL, Description = NULL, CreatedBy = ".$a[4].", CreatedDate = ".$a[5].", ClosedDate = ".$a[6].", CaseCategoryId = NULL, CaseRootCauseId = NULL, UpdateDate = '".gmdate("Y-m-d H:i:s")."', UpdateBy = 'ImportFromSF', isDeleted = 0 WHERE SalesforceId = ".$a[0].";";
			}*/

			$this->db->trans_start();
			$this->db->update_batch('Cases', $array, 'SalesforceId');
			$sql = "UPDATE Configuration SET ConfigurationValue = '".gmdate("Y-m-d H:i:s")."' WHERE ConfigurationId = 1;";
			$query = $this->db->query($sql);
			$this->db->trans_complete();

			return $query;
		}
		catch(Exception $e)
		{
			print $e->getMessage();
		}
	}

	public function deleteCaseOnSF($array)
	{
		try
		{
			$ids = '';
			foreach ($array as $key => $value) {
				if($ids=='')
					$ids.="('".$value."'";
				else
					$ids.=", '".$value."'";
			}
			$ids.= ")";
			$sql = "UPDATE Cases SET isDeleted = 1 WHERE SalesforceId in".$ids;
			$query = $this->db->query($sql);

			$sql = "UPDATE Configuration SET ConfigurationValue = '".gmdate("Y-m-d H:i:s")."' WHERE ConfigurationId = 1";
			$query = $this->db->query($sql);
			return $query;
		}
		catch(Exception $e)
		{
			print $e->getMessage();
		}
	}
}
