<div class="subnav">
    <a class="ajax-link" href="/cases/">
        <span class="icon-home icon-white"></span><span class="text">Dashboard</span>
    </a>
    <a class="ajax-link" href="/cases/lists/">
        <span class="icon-list icon-white"></span><span class="text">List</span>
    </a>
    <a class="ajax-link" href="/cases/report/">
        <span class="icon-adjust icon-white"></span><span class="text">Report</span>
    </a>
    <a class="ajax-link" href="/cases/configuration/">
        <span class="icon-wrench icon-white"></span><span class="text">Setting</span>
    </a>
</div>

<div class="modal hide" id="CaseChartModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>Daily Case Chart</h3>
	</div>
	<div class="modal-body" style="min-width:800px;">
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
	</div>
</div>

<div class="modal hide edit-case" id="CaseModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Case Info</h3>
    </div>
    <div class="modal-body modal-body-static">
        <table class="table" id="CaseInfoTable" style="vertical-align:middle;">
            <tr>
                <td style="vertical-align:middle;">
                    <label>Subject:</label>
                </td>
                <td>
                    <input type="text" style = "width: 97%;margin:5px;" maxlength="255" id="caseSubject">
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <label>Descriptions:</label>
                </td>
                <td>
                    <div class="editor" id="caseDescription"></div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <label>Function Unit:</label>
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
                <td style="vertical-align:middle;">
                    <label>Case Type:</label>
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
                <td style="vertical-align:middle;">
                    <label>Case Date:</label>
                </td>
                <td>
                    <div class="input-append" style="display:inline">
                        <input type="text" class="datetimepicker" id="caseDatetime" style="cursor:pointer;width:190px;"><span class="add-on"><i class="icon-calendar"></i></span>
                    </div>
                    <span style="vertical-align:middle;margin-left:20px">
                        <i class="icon-bell"></i> <label style="display:inline">Response Date:</label>
                    </span>
                    <span>
                        <div class="input-append" style="display:inline">
                            <input type="text" class="datetimepicker" id="responseDatetime" style="cursor:pointer;width:190px;"><span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <label>Case Age:</label>
                </td>
                <td>
                    <span><input type="text" id="caseAge" style="padding:0;text-align:center;border-top:0 none;border-left:0 none;border-right:0 none;border-bottom:1 solid #d0dde9;box-shadow:none;width:35px" value="1"/></span> hour
                </td>
            </tr>
            <tr>
                <td style="">
                    <label>Amount:</label>
                </td>
                <td>
                    <div style="width:10%;"><input type="text" id="caseAmount" style="border:0;font-weight:600;box-shadow:none;" value="1"/></div>
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