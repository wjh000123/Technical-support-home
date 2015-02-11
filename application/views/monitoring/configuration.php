<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-list-alt"></i> Configuration</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-add btn-round"><i class="icon-plus"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class = "box-content" style = "overflow:hidden;">
			<div class="monitoring-list">
				<table class="table table-condensed" id="monitoringList">
					<thead>
						<th>Subject</th>
						<th>Server</th>
						<th>Script</th>
						<th>Status</th>
						<th>Action</th>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal hide edit-case" id="MonitoringModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Monitoring Info</h3>
	</div>
	<div class="modal-body modal-body-static">
		<table class="table" id="monitoringInfoTable" style="vertical-align:middle;">
			<tr>
				<td style="vertical-align:middle;">
					<label>Subject:</label>
				</td>
				<td>
					<input type="text" style = "width: 97%;margin:5px;" maxlength="255" id="monitoringSubject">
				</td>
			</tr>
			<tr>
				<td style="vertical-align:middle;">
					<label>Server:</label>
				</td>
				<td id="externalServer">
					<input type="text" style = "width: 97%;margin:5px;" maxlength="255">
					<span class="error" style="display:none;color:#bd4247;margin:0 6px;">
						only <strong>IP</strong> format supported.
					</span>
					<div class="check-server" style="display:none;"><i class="icon-refreshing"></i>checking server...</div>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:middle;">
					<label>Query SQL:</label>
				</td>
				<td>
					<textarea class="editor" id="monitoringSQL" style="min-height:150px;"></textarea>
				</td>
			</tr>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" class="btn btn-primary" data-loading-text="Saving..." id="saveMonitoring">Save</a>
		<a href="#" class="btn btn-info" data-loading-text="Saving..." id="savenewMonitoring">Save & New</a>
	</div>
</div>