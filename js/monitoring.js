var mlTable;
var isIPValid = false;
var ValidIpAddressRegex = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$";
var ValidHostnameRegex = "^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?)*\.?$";

mlTable = $('#monitoringList').dataTable({
	"sDom": "<'row-fluid'<'span4'l><'span2 modify'><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
	"sPaginationType": "bootstrap",
	"bAutoWidth" : false,
	"iDisplayLength": 15,
	"sAjaxSource": '/monitoring/getList',
	"bSort": false,
	"aLengthMenu": [[15, 30, 50, 100, -1], [15, 30, 50, 100, "All"]],
	"oLanguage": {
		"sLengthMenu": "_MENU_"
	},
	"fnDrawCallback": function(){
	}
});

$('body').on('click', '.btn-add', function(e){
	$('#MonitoringModal').modal('show');
});

$('body').on('click', '.long-script', function(){
	$this = $(this);
	if($this.hasClass('condensed'))
		$this.removeClass('condensed');
	else
		$this.addClass('condensed');
});

$('#monitoringSQL').focusout(function(){
});

$('#externalServer input').focusout(function(e){
	$this = $(this);
	var inputIP = $this.val();
	var async = true;
	if(inputIP.length)
	{
		if(inputIP.match(ValidIpAddressRegex))
		{
			if(typeof $this.data('async')!=='undefined')
				async = $this.data('async');
			$.ajax({
				url:'/monitoring/checkServer/'+inputIP,
				async: async,
				timeout: 5000,
				beforeSend:function(){
					$('#externalServer .check-server').show();
					$('#externalServer .error').hide();
				},
				success:function(data){
					if(data)
					{
						$('#externalServer .check-server').hide();
						isIPValid = true;
					}
					else
					{
						$('#externalServer .check-server').hide();
						$('#externalServer .error').html('Failed to connect server.');
						$('#externalServer .error').show();
						isIPValid=false;
					}
				},
				error:function(){
					$('#externalServer .check-server').hide();
					$('#externalServer .error').html('Failed to connect server.');
					$('#externalServer .error').show();
					isIPValid=false;
				}
			});
		}
		else
		{
			$('#externalServer .check-server').hide();
			$('#externalServer .error').show();
			isIPValid=false;
		}
	}
	else
	{
		isIPValid=false;
	}
});

$('#MonitoringModal').on('show.bs.modal', function() {
	initialMonitoringModal();
})

$('body').on('click', '#saveMonitoring', function(){
    $("#externalServer input").data('async', false).focusout();
    callForEditEvent($(this));
});

$('body').on('click', '.edit-event', function(e){
    $this = $(this);
	var row = $this.parents('tr');
	var id = $this.data('id');
	var subject = row.find('td:eq(0)').text();
	var server = row.find('td:eq(1)').text();
	var sqlScript = row.find('td:eq(2)').text();
	updateMonitoringEventModal({
		subject: subject,
		server: server,
		script: sqlScript,
		id: id
	},
	true);
});

$('body').on('click', '.delete-event', function(e){
	if(confirm('Are you sure to delete this event?'))
	{
		$this = $(this);
		$this.data('action', 'delete');
		callForEditEvent($this, true);
	}
});

function updateMonitoringEventModal(info, show){
	if(show)
	{
		$('#MonitoringModal').modal('show');
	}
	$('#monitoringSubject').val(info.subject);
	$('#externalServer input').val(info.server);
	$('#monitoringSQL').val(info.script);
	$('#saveMonitoring').text('Update').data('action', 'update').data('id', info.id);
	$('#savenewMonitoring').hide();
}

function initialMonitoringModal(){
	//$('#MonitoringModal').removeData();
	$('#monitoringSubject').val('').removeData();
	$('#externalServer input').val('').removeData();
	$('#monitoringSQL').val('').removeData();
	$('#saveMonitoring').text('Save').removeData();
	$('#savenewMonitoring').text('Save & New').removeData();
	$('#savenewMonitoring').show();
}

function callForEditEvent(trigger, directCall){
	if($('#monitoringSQL').val().length&&$('#monitoringSubject').val().length&&isIPValid||directCall)
	{
		var url = '';
		var id;
		if(trigger.data('action')=='update')
		{
			url = '/monitoring/updateEvent';
		}
		else if(trigger.data('action')=='delete')
			url = '/monitoring/deleteEvent';
		else
			url = '/monitoring/addEvent';
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			contentType: "application/json",
			data: JSON.stringify({
				subject: $('#monitoringSubject').val(), 
				server: $('#externalServer input').val(), 
				sql: $('#monitoringSQL').val(),
				id: trigger.data('id')
			}),
			beforeSend: function(){

			},
			success: function(data){
				$('#MonitoringModal').modal('hide');
				mlTable.fnReloadAjax();
			},
			error: function(){
				alert('Error occurred while modify the monitoring event.');
			}
		});
	}
}

function addMonitoringEventLogPanel(panelInfo){
	var panel = '<div class="panel panel-default">\
		<div class="panel-heading">\
			<h4 class="panel-title">\
			<a class="accordion-toggle" data-toggle="collapse" data-parent="'+panelInfo['appenderParent']+'" href="'+panelInfo['href']+'">\
		    	'+panelInfo['title']+'\
			</a>\
			</h4>\
		</div>\
		<div id="collapseOne" class="panel-collapse collapse">\
			<div class="panel-body">\
				'+panelInfo['content']+'\
			</div>\
		</div>\
	</div>';
}