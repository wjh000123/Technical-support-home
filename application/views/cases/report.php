<div class="row-fluid sortable ui-sortable">
	<div class="box">
		<div class="box-header well">
			<h2><i class="icon-list-alt"></i> Monthly Report - English Center</h2>
			<div class="box-icon">
					<a href="#" class="btn btn-round" id="viewSFCaseChart"><i class="icon-picture"></i></a>
				<a href="#" class="btn btn-round" id="CaseAdd"><i class="icon-plus"></i></a>
				<a href="#" class="btn btn-round" id="CaseList"><i class="icon-list"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class = "box-content row-fluid" style="padding:0;">
			<div style="text-align: center;">
				<div id="chartReminder" style="display: inline-block;margin-bottom: 10px;">
					
				</div>
			</div>
			<div name="TimeSelector" style="right:50px;position:absolute;z-index:1;">
				<div class="btn-toolbar" style="margin: 0;">
					<div class="btn-group">
						<a class="btn yearPicker" href="#" id="monthlyReportYearPicker" style="position:relative">
							<span></span>
							<i class="caret"></i>
						</a>
					</div>
			    </div>
			</div>
			<div id = "HCmonthlyReport" class="center" style = "border-bottom:1px solid #BBB;">
				<div id="loading">
					<div class="center">
					</div>
				</div>
			</div>
			<div id = "SystemUnit" style = "float: left;width: 50%;min-height:1px;border-bottom:1px solid #BBB;"></div>
			<div id = "CaseType" style = "float: left; width: 50%;min-height:1px;border-bottom:1px solid #BBB;"></div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="box trends-function-unit span6">
			<div class="box-header well">
				<h2><i class="icon-align-left"></i> Trends - Function Unit</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
				</div>
			</div>
			<div class="box-content row-fluid" style="padding:10px 0px;">
				<div class="center-wrapper">
					<div class="btn-group" id="SystemUnitContainer" style="display:inline-block;vertical-align:middle;">
						<a href="#" class="btn btn-default">Salesforce</a>
						<a href="#" class="btn btn-default">OBOE</a>
						<a href="#" class="btn btn-default">Etown</a>
					</div>
				</div>
				<div class= "list-content" id="trends-functionunit-content">
				</div>
			</div>
		</div>
		<div class="box overview-list-system-unit span6">
			<div class="box-header well">
				<h2><i class="icon-align-left"></i> Overview - Function Unit</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
				</div>
			</div>
			<div class="box-content row-fluid" style="padding:10px 0px;">
				<div class="range-criteria">
					<div class="center-wrapper">
						<div class="btn-group" style="display:inline-block;vertical-align:middle;">
							<div id="overViewListRange" class="btn reportRange">
								<i class="icon-calendar"></i>
								<span></span> <i class="caret"></i>
							</div>
							<a href="#" class="btn submit"><i class="icon-zoom-in"></i></a>
						</div>
					</div>
				</div>
				<div class= "list-content" id="SystemUnitByTimeRange">
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="box trends-case-type span6">
			<div class="box-header well">
				<h2><i class="icon-align-left"></i> Trends - Case Type</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
				</div>
			</div>
			<div class="box-content row-fluid" style="padding:10px 0px;">
				<div class="center-wrapper">
					<a href="#" class="btn btn-default" id="caseTypeTrendsLoad">Go</a>
				</div>
				<div class= "list-content" id="trends-casetype-content">
				</div>
			</div>
		</div>
		<div class="box top-list-case-type span6">
			<div class="box-header well">
				<h2><i class="icon-align-left"></i> Top List - Case Type</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
				</div>
			</div>
			<div class="box-content row-fluid" style="padding:10px 0px;">
				<div class="range-criteria">
					<div class="center-wrapper">
						<div class="btn-group" style="display:inline-block;vertical-align:middle;">
							<div id="topListRange" class="btn reportRange">
								<i class="icon-calendar"></i>
								<span></span> <i class="caret"></i>
							</div>
							<select data-rel="chosen" style="width:150px;display:none;" id="CriteriaOfCaseType">
								<option value="-1">All Case Type</option>
							</select>
							<a href="#" class="btn submit"><i class="icon-zoom-in"></i></a>
						</div>
					</div>
				</div>
				<div class= "list-content" id="top-list-content">
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="box overview-case-handling-info span12">
			<div class="box-header well">
				<h2><i class="icon-align-left"></i> Case Handling Info</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
				</div>
			</div>
			<div class="box-content row-fluid" style="padding:10px 0px;">
				<div class="range-criteria">
					<div class="center-wrapper">
						<div class="btn-group" style="display:inline-block;vertical-align:middle;">
							<span id="caseHandlingOverviewRange" class="btn reportRange">
								<i class="icon-calendar"></i>
								<span></span> <i class="caret"></i>
							</span>
							<select class="dataViewType" data-rel="chosen" style="width:110px;display:none;">
								<option value="hour">By Hour</option>
								<option value="weekday">By Weekday</option>
							</select>
							<a href="#" class="btn submit"><i class="icon-zoom-in"></i></a>
						</div>
					</div>
				</div>
				<div class= "list-content" id="case-handling-content">
				</div>
			</div>
		</div><!--
		<div class="box trends-case-type span6">
			<div class="box-header well">
				<h2><i class="icon-align-left"></i> Trends - Case Type</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
				</div>
			</div>
			<div class="box-content row-fluid" style="padding:10px 0px;">
				<div class= "list-content" id="trends-casetype-content">
				</div>
			</div>
		</div>-->
	</div>
	<div class="box" style="margin: 0 auto;">
		<div class="box-header well">
			<h2><i class="icon-warning-sign"></i> Emergency Event</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content row-fluid">
			<div class="comment">
				<div class="item priority-1"></div><span class="item-text">single school</span>
				<div class="item priority-2"></div><span class="item-text">some schools</span>
				<div class="item priority-3"></div><span class="item-text">whole city</span>
				<div class="item priority-4"></div><span class="item-text">some cities</span>
				<div class="item priority-5"></div><span class="item-text">whole country</span>
			</div>
			<div class="timeline-box">
				<div id="emergencyCaseTimeline" style="width:90%;display:inline-block;">
					<div class="timeline-html-wrap" style="display: none">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal hide" id="CaseDataModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Data List</h3>
	</div>
	<div class="modal-body">
		<table class="table table-bordered table-striped table-condensed datatable" id="CaseDataTable" style="min-width:850px;">
			<thead>
			  <tr>
				  <th>System Unit</th>
				  <th>Function Unit</th>
				  <th>Case Type</th>
				  <th>Amount</th>
			  </tr>
			</thead>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary">Save changes</a>
	</div>
</div>

<div class="modal hide" id="CaseDetailModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Report</h3>
	</div>
	<div class="modal-body" style="min-width:800px;min-height:400px;">
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
	</div>
</div>

<!--
<div class="row-fluid sortable">
	<div class="box">
		<div class="box-header well">
						<h2><i class="icon-list-alt"></i> Monthly Report</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
						</div>
					</div>
		<div class = "box-content">
			<div name="TimeSelector" style="margin-bottom:5px;text-align: right;">
				<div class="btn-toolbar" style="margin: 0;">
	              <div class="btn-group">
	                <button class="btn">2012</button>
	                <button class="btn active">2013</button>
	                <button class="btn">2014</button>
	                <button class="btn">2015</button>
	              </div>
	            </div>
			</div>
			<div id="-monthlyReport" class="center" style="height:300px;"><div style="text-align: center;">Loading...</div></div>
		</div>
	</div>
</div>
-->
