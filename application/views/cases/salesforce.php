<div class="row-fluid">
	<div class="box">
		<div class="box-header well">
				<h2><i class="icon-list-alt"></i> Case List</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-round" id="viewSFCaseChart"><i class="icon-picture"></i></a>
					<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
				</div>
			</div>
		<div class = "box-content" style = "overflow:hidden;" id="SFCaseData">
			<div style="text-align: center;">
				<div id="chartReminder" style="display: inline-block;margin-bottom: 10px;">
					
				</div>
			</div>
			<table class="table table-condensed datatable" style="min-width:850px;" id="SFCaseDataTable">
				<thead>
				  <tr>
					<th>Subject</th>
					<th>Created By</th>
					<th>Function Unit Detail</th>
					<th>Case Type Detail</th>
					<th>Owner</th>
					<th>Status</th>
					<th>Action</th>
				  </tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<div class="modal hide fade" id="CaseChartModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Salesforce Case</h3>
	</div>
	<div class="modal-body" style="min-width:800px;">
		
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
	</div>
</div>