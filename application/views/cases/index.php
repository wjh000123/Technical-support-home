<div class="box">
	<div class="box-header well">
		<h2><i class="icon-list-alt"></i> Overview - English Center</h2>
		<div class="box-icon">
			<a href="#" class="btn btn-round" id="CaseAdd"><i class="icon-plus"></i></a>
			<a href="#" class="btn btn-round" id="viewSFCaseChart"><i class="icon-picture"></i></a>
			<a href="#" class="btn btn-round" id="CaseList"><i class="icon-list"></i></a>
			<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
		</div>
	</div>
	<div class = "box-content row-fluid" style = "overflow:hidden;">
		<div class="span8">
			<div class="page-header" style="margin-bottom:0px;padding-bottom:5px;">
				<span class="title">Today's Cases </span>
			</div>
			<table class="table table-condensed datatableSmall" id="todayCaseList">
				<thead>
					<tr>
						<th>Subject</th>
						<th>Created By</th>
						<th>Owner</th>
						<th>Status<a href="#" class='refresh' style='float:right;'><i class='icon-refresh'></i></a></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="span4" style="margin-left:0px;">
			<div id = "weeklyCaseReport" class="center span12" style = "overflow:hidden;">
				<div id="loading">
					<div class="center">
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
