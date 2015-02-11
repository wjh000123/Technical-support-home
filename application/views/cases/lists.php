<div class="box">
	<div class="box-header well">
		<h2><i class="icon-list-alt"></i><span class="header-title"> Daily Case</span></h2>
		<div class="box-icon">
			<a href="#" class="btn btn-round" id="switcher" style="overflow: hidden;"><div class="toggle-wrapper" style="width:50px;margin-left:-33px"><i class="icon-chevron-left second"></i><i class="icon-chevron-right first" style="margin-left:10px;"></i></div></a>
			<a href="#" class="btn btn-round" id="viewSFCaseChart"><i class="icon-picture"></i></a>
			<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
		</div>
	</div>
	<div class = "box-content" id="SFCaseData" style = "overflow:hidden;">
		<div style="text-align: center;">
			<div id="chartReminder" style="display: inline-block;margin-bottom: 10px;">
			</div>
		</div>
		<div style = "overflow:hidden;width:200%;">
		<div id="DailyCase" style="float: left; width: 50%">
			<table class="table table-condensed datatable" id="SFCaseDataTable">
				<thead>
				  <tr>
					<th>Subject</th>
					<th>Created By</th>
					<th>Response Time(min)</th>
					<th>Case Age(hour)</th>
					<th>Function Unit Detail</th>
					<th>Case Type Detail</th>
					<th>Owner</th>
					<th>Status</th>
					<th>Action</th>
				  </tr>
				</thead>
			</table>
		</div>
		<div id="EmergencyCase" class="calendar-wrapper" style="float:left;margin-top:10px;margin-left:0.5%;width:49%;">
			<div id="calendar">
				
			</div>
			<div class="new-event popup" style="display:none;">
				<div class="pointer">
					<div class="arrow"></div>
					<div class="arrow_border"></div>
				</div>
				<i class="close-pop icon-remove"></i>
				<h5>Emergency Case</h5>
				<div class="field">
                    <table class="table table-no-border table-condensed emergency-info" style="vertical-align:middle;">
						<tr>
							<td >
								<label>Date:</label>
							</td>
							<td>
								 <input type="text" class="date time durationStartDate" id="emergencyStartDate" style="float:left;text-align:center;width:80px;">
								<div class="bootstrap-timepicker" style="float:left;">
						            <input id="emergencyStartTime" class="time" type="text" style="text-align:center;width:37px;">
						        </div>
						        <label style="float:left;margin:0 5px 0 10px;">to</label>
						        <input type="text" class="date time durationEndDate" id="emergencyEndDate" style="float:left;text-align:center;width:80px;">
								<div class="bootstrap-timepicker" style="float:left;">
						            <input id="emergencyEndTime" class="time" type="text" style="text-align:center;width:37px;">
						        </div>
							</td>
						</tr>
						<tr>
							<td >
								<label>Subject:</label>
							</td>
							<td>
								<input type="text" class="event-input" maxlength="255" id="emergencySubject">
							</td>
						</tr>
						<tr>
							<td>
								<label>Reason:</label>
							</td>
							<td>
								<input type="text" class="event-input" maxlength="255" id="emergencyReason">
							</td>
						</tr>
						<tr>
							<td >
								<label>Incidence:</label>
							</td>
							<td>
								<div id="emergencyIncidence" class="rating_container" data-value="1">
									<a href="#" class="rating_selected" data-value="1"></a>
									<a href="#" class="rating_off" data-value="2"></a>
									<a href="#" class="rating_off" data-value="3"></a>
									<a href="#" class="rating_off" data-value="4"></a>
									<a href="#" class="rating_off" data-value="5"></a>
									<span style="position:relative;top:3px;margin-left:5px;" class="note"></span>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<a href="#" data-loading-text="Saving..." id="emergencySave" class="btn btn-primary">Save</a>
            </div>
		</div>
		</div>
	</div>
</div>

<!--[if lte IE 8]>
<style>
	#SFCaseDataTable .long-text
    {
        max-width: 195px;
        display: block;
    }
    .chzn-container.chzn-container-single
    {
		width: 230px !important;
    }
</style>
<![endif]-->
<!--[if lte IE 7]>
<style>
	#SFCaseDataTable .long-text
    {
        width: 195px;
        display: block;
    }
</style>
<![endif]-->
<!--[if lte IE 9]>
<style>
	#SFCaseDataTable .long-text
    {
        width: 195px;
        display: block;
    }
</style>
<![endif]-->