<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-list-alt"></i> Configuration</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class = "box-content" style = "overflow:hidden;">

			<ul class="nav nav-tabs" id="configTab">
				<li class="active"><a href="#functionUnitTab"><strong>Function Unit</strong></a></li>
				<li><a href="#caseTypeTab"><strong>Case Type</strong></a></li>
			</ul>
			 
			<div id="configTabContent" class="tab-content">

				<div class="tab-pane active" id="functionUnitTab">
					<table class="table table-condensed" id="functionUnitList">
						<thead>
							<tr>
								<th>System Unit  <i class='icon-plus pointer'></i></th>
								<th>Function Unit  <i class='icon-plus pointer'></i></th>
								<th>Sub Function Unit  <i class='icon-plus pointer'></i></th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>

				<div class="tab-pane" id="caseTypeTab">
					<table class="table table-condensed datatablePure" id="caseTypeList">
						<thead>
							<tr>
								<th>Case Type  <i class='icon-plus pointer'></th>
								<th>Case Type Detail  <i class='icon-plus pointer'></th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>

<div class="modal hide" id="addSystemUnitModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Add System Unit</h3>
	</div>
	<div class="modal-body modal-body-static">
		<table class="table table-bordered" style="vertical-align:middle;">
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>New System Unit</h4>
				</td>
				<td>
					<div class="input-append" style="margin:0px;"><input type="text" style="margin-bottom:0px;" id="inputSystemUnit"/><button class="btn" type="button">Add</button></div>
					<select id="selectSystemUnit_NSU" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" class="btn btn-primary" data-loading-text="Saving..." id="saveSUAdd">Save</a>
		<a href="#" class="btn btn-info" data-loading-text="Saving..." id="savenewSUAdd">Save & New</a>
	</div>
</div>
<div class="modal hide" id="addFunctionUnitModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Add Function Unit</h3>
	</div>
	<div class="modal-body modal-body-static">
		<table class="table table-bordered" style="vertical-align:middle;">
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>System Unit</h4>
				</td>
				<td>
					<select id="selectSystemUnit_NFU" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>New Function Unit</h4>
				</td>
				<td>
					<div class="input-append" style="margin:0px;"><input type="text" style="margin-bottom:0px;" id="inputFunctionUnit"/><button class="btn" type="button">Add</button></div>
					<select id="selectFunctionUnit_NFU" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" class="btn btn-primary" data-loading-text="Saving..." id="saveFUAdd">Save</a>
		<a href="#" class="btn btn-info" data-loading-text="Saving..." id="savenewFUAdd">Save & New</a>
	</div>
</div>
<div class="modal hide" id="addSubFunctionUnitModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Add Function Unit Detail</h3>
	</div>
	<div class="modal-body modal-body-static">
		<table class="table table-bordered" style="vertical-align:middle;">
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>System Unit</h4>
				</td>
				<td>
					<select id="selectSystemUnit_NSFU" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Function Unit</h4>
				</td>
				<td>
					<select id="selectFunctionUnit_NSFU" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Function Unit Detail</h4>
				</td>
				<td>
					<div class="input-append" style="margin:0px;"><textarea class="autosize" id="inputSubFunctionUnit" style="height: 18px;margin-bottom:0px;"></textarea><button class="btn" type="button">Add</button></div>
					<select id="selectSubFunctionUnit_NSFU" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" class="btn btn-primary" data-loading-text="Saving..." id="saveSFUAdd">Save</a>
		<a href="#" class="btn btn-info" data-loading-text="Saving..." id="savenewSFUAdd">Save & New</a>
	</div>
</div>
<div class="modal hide" id="addCaseTypeModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Add Case Type</h3>
	</div>
	<div class="modal-body modal-body-static">
		<table class="table table-bordered" style="vertical-align:middle;">
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>New Case Type</h4>
				</td>
				<td>
					<div class="input-append" style="margin:0px;"><input type="text" style="margin-bottom:0px;" id="inputCaseType"/><button class="btn" type="button">Add</button></div>
					<select id="selectCaseType_NCT" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" class="btn btn-primary" data-loading-text="Saving..." id="saveCTAdd">Save</a>
		<a href="#" class="btn btn-info" data-loading-text="Saving..." id="savenewCTAdd">Save & New</a>
	</div>
</div>
<div class="modal hide" id="addSubCaseTypeModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Add Case Type Detail</h3>
	</div>
	<div class="modal-body modal-body-static">
		<table class="table table-bordered" style="vertical-align:middle;">
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Case Type</h4>
				</td>
				<td>
					<select id="selectCaseType_NSCT" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;vertical-align:middle;">
					<h4>Case Type Detail</h4>
				</td>
				<td>
					<div class="input-append" style="margin:0px;"><textarea class="autosize" id="inputSubCaseType" style="height: 18px;margin-bottom:0px;"></textarea><button class="btn" type="button">Add</button></div>
					<select id="selectSubCaseType_NSCT" data-rel="chosen" data-placeholder="Loading">
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" class="btn btn-primary" data-loading-text="Saving..." id="saveSCTAdd">Save</a>
		<a href="#" class="btn btn-info" data-loading-text="Saving..." id="savenewSCTAdd">Save & New</a>
	</div>
</div>