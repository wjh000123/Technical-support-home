<div class="row-fluid">
	<div class="box">
		<div class="box-header well">
				<h2><i class="icon-list-alt"></i> Case List</h2>
				<div class="box-icon">
					<a href="#" class="btn btn-round" id="viewSFCaseChart"><i class="icon icon-black icon-image"></i></a>
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

<div class="modal hide" id="CaseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Edit Case</h3>
	</div>
	<div class="modal-body modal-body-static">
		<table class="table table-bordered table-striped" id="CaseInfoTable" style="vertical-align:middle;">
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Subject</h4>
				</td>
				<td>
					<input type="text" style = "width: 97%;margin:5px;" maxlength="255" id="caseSubject">
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Descriptions</h4>
				</td>
				<td>
					<div class="editor" id="caseDescription"></div>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Function Unit</h4>
				</td>
				<td>
					<select id="selectSystemUnit" data-rel="chosen">
					</select>
					<i class= 'icon-chevron-right'></i>
					<select id="selectFunctionUnit" data-rel="chosen">
					</select>
					<i class= 'icon-chevron-right'></i>
					<select id="selectSubFunctionUnit" data-rel="chosen">
					</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Case Type</h4>
				</td>
				<td>
					<select id="selectCaseType" data-rel="chosen">
					</select>
					<i class= 'icon-chevron-right'></i>
					<select id="selectSubCaseType" data-rel="chosen">
					</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Case Date</h4>
				</td>
				<td>
					<div class="input-append">
						<input type="text" class="datetimepicker" id="caseDatetime" style="cursor:pointer;width:190px;"><span class="add-on"><i class="icon-calendar"></i></span>
					</div>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;">
					<h4>Amount</h4>
				</td>
				<td>
					<div style="width:10%;"><input type="text" id="caseAmount" style="border: 0; font-weight: bold;" value="1"/></div>
					<div class="slider" style="width:220px;"></div>
				</td>
			</tr>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" class="btn btn-primary" data-loading-text="Saving..." id="saveCase">Save</a>
		<a href="#" class="btn btn-info" data-loading-text="Saving..." id="savenewCase">Save & New</a>
	</div>
</div>