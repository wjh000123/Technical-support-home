var today = new Date();
var DEFAULTYEAR = today.getFullYear();
var DEFAULTMONTH = today.getMonth();
var currentYear = DEFAULTYEAR;
var currentMonth = DEFAULTMONTH;
var selectedDate = today;
var Months = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var WeekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Staturday', 'Sunday'];
var Hours = [	'1:00', '2:00', '3:00', '4:00', '5:00', '6:00', '7:00', '8:00', 
				'9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', 
				'17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00', '24:00'
			];
var emergencyBriefNotes = ['School', 'Schools', 'City', 'Cities', 'Country'];
var emergencyDetailNotes = ['single school', 'some schools', 'the whole city', 'some cities', 'the whole country'];
var caseFilterData;
var changedElement = [];
var caseType;
var addedCaseType = [];
var subCaseType;
var addedSubCaseType = [];
var systemUnit;
var addedSystemUnit = [];
var functionUnit;
var addedFunctionUnit = [];
var subFunctionUnit;
var addedSubFunctionUnit = [];
var oFunctionUnitData;
var oCaseTypeData;
var oFontColor;
var addedObj;
var addLogHeader = 'Added List';
var oCaseInfo = {};
var counts=0;
var fulTable;
var ctlTable;
var caseTable;
var emergencyCalendar, lastUpdatedEvent, datepickerClicked;
var dayoff;
var holiday;
var overviewlistRangeStart, overviewlistRangeEnd;
var topListChart, systemUnitTrendsChart, caseTypeTrendsChart;
var toplistRangeStart, toplistRangeEnd;
var caseHandlingOverviewRangeStart, caseHandlingOverviewRangeEnd;

function tsDocReady()
{
	if($('#CaseChartModal').length)
	{
		callForHoliday();
	}
	$('.dataTables_empty').html('Loading case...');
	$('#caseDescription').wysiwyg();
	if((filter=getAnchorValue(window.location.hash, "date"))!=null)
	{
		var year = filter.split("-")[0];
		var month = filter.split("-")[1];
		selectedDate = setSelectedDate(year, month);
	}
	else
		selectedDate = new Date();

	callForCaseFilterData();
	//callForMonthlyCaseTypeAmount();
	initializeSystemUnitTrends();

	{
		$criteriaOfCaseType = $('#CriteriaOfCaseType');
		if($criteriaOfCaseType.length>0&&caseType.length>0)
		{
			allCaseType = $.merge([{k:'-1',v:'All Case Type'}], caseType);
			setChildSelectValue('#CriteriaOfCaseType', allCaseType);
		}
	}

	callForOpenCaseAmount('.notification-count');
	callForEmergencyCaseReportByYear();
	callForCaseReportByYear();
	callForRecentOneWeekCaseReport();
	if(getAnchorValue(window.location.hash, 'open'))
		callForOpenCaseListOverview('#SFCaseDataTable');
	else
		refreshSFDataTable('#SFCaseDataTable');
	//callForOpenCaseListOverview('#SFCaseDataTable');

	$("#caseAmount").val($('.slider').slider("value"));

	setDateSelector();

    var picker = $('#monthSelector').datepicker({
    	format: 'yyyy-mm-dd',
    	autoclose: true,
    	minViewMode: 'months'
    }).on('changeDate', function(ev){
		var date = new Date(ev.date.valueOf());
		selectedDate = setSelectedDate(date.getFullYear(), date.getMonth()+1, 1);
		setDateSelector();
		picker.hide();
    }).data('datepicker');

	$('.datatableSmall').dataTable({
		"sPaginationType": "bootstrap",
		"sDom": "prt",
		"bAutoWidth" : false,
		"bProcessing" : false,
		"iDisplayLength": 10,
		"sAjaxSource": '/cases/viewTodayCaseList',
		"aaSorting": [],
		"oLanguage": {
			"sLoadingRecords": "Loading case..."
		},
		"fnDrawCallback" : function() {
		    $('[rel="popover"],[data-rel="popover"]').popover();
		    $(".createDate").fromNow();
			//$("abbr.timeago").timeago();
		}
	});

	fulTable = $('#functionUnitList').dataTable({
		"sDom": "<'row-fluid'<'span4'l><'span2 modify'><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
		"sPaginationType": "bootstrap",
		"bAutoWidth" : false,
		"iDisplayLength": 15,
		"sAjaxSource": '/configure/getSubFunctionUnitTableList',
		"bSort": false,
		"aLengthMenu": [[15, 30, 50, 100, -1], [15, 30, 50, 100, "All"]],
		"oLanguage": {
			"sProcessing": "Processing...",
			"sLoadingRecords": "Loading...",
			"sLengthMenu": "_MENU_"
		},
		"aoColumns": [
	        { "sClass": "editable" },
	        { "sClass": "editable" },
	        { "sClass": "editable" },
	        { "sClass": "" }
	    ],
		"fnDrawCallback": function(){
			oFontColor = $(this).css('color');
			if($('#functionUnitList  tbody .editable').length)
			{
				var allData = fulTable.fnGetData();
				if($(allData).not(oFunctionUnitData).length>0)
				{
					oFunctionUnitData = allData;
					systemUnit = [];
					functionUnit = [];
					subFunctionUnit = [];
					var sudupes = {};
					var fudupes = {};
					var sfudupes = {};
					$.each(allData, function(key, value){
						var su = $(value[3]).attr('data-systemunit');
						var suId = su.split('|')[0];
						var suName = su.split('|')[1];
						var fu = $(value[3]).attr('data-functionunit');
						var fuId = fu.split('|')[0];
						var fuName = fu.split('|')[1];
						var sfu = $(value[3]).attr('data-subfunctionunit');
						var sfuId = fu.split('|')[0];
						var sfuName = sfu.split('|')[1];

						var temp = {};
						if(!sudupes[su]&&suName.length>0)
						{
							sudupes[su] = true;
							temp['k'] = suId;
							temp['v'] = suName;
							systemUnit.push(temp);
						}
						temp = {};
						if(!fudupes[fu]&&fuName.length>0)
						{
							fudupes[fu] = true;
							temp['k'] = fuId;
							temp['v'] = fuName;
							temp['pk'] = suId;
							functionUnit.push(temp);
						}
						temp = {};
						if(!sfudupes[sfu]&&sfuName.length>0)
						{
							sfudupes[sfu] = true;
							temp['k'] = sfuId;
							temp['v'] = sfuName;
							temp['pk'] = fuId;
							subFunctionUnit.push(temp);
						}
					});
				}
				
				$('#functionUnitList tbody .editable').inlineEdit({
					buttons: '<a href="#" class="save"><i class="icon-ok"></i></a>',
					buttonsTag: 'a',
  					cancelOnBlur: true,
  					placeholder: 'Click to add',
				    save: function(e, data) {
				    	var dataRef;
				    	var colIndex = $(this).index();
				    	var rowIndex = $(this).parent().index();

				    	dataRef = getDataRefByColumnIndex(colIndex);

				    	valIndex = getDataSourceArrayByColumn($(this))[0];
				    	oldVal = getDataSourceArrayByColumn($(this))[1];
				    	allValHost = getAllDataSourceByColumn($(this));

				    	if(data.value!=oldVal)
				    	{
				    		existingElement = $.grep(changedElement, function(value) {
								return (value[0][0]==rowIndex&&value[0][1]==colIndex&&value[3][0]==allValHost[0]);
							});
							if(existingElement.length==1)
							{
								changedElement[$.inArray(existingElement[0], changedElement)] = new Array(new Array(rowIndex, colIndex), new Array(valIndex, data.value, dataRef), oldVal, allValHost);
							}
							else
				    			changedElement.push(new Array(new Array(rowIndex, colIndex), new Array(valIndex, data.value, dataRef), oldVal, allValHost));
					    	$(this).css({color: '#08c'});
					    	$('.span2.modify').html('<a class="btn" id="applyAll"><i class="icon-ok"></i></a><a class="btn" id="cancelAll"><i class="icon-remove"></i></a>');
				    	}
				    	else
				    	{
				    		changedElement = $.grep(changedElement, function(value) {
								return !(value[0][0]==rowIndex&&value[0][1]==colIndex&&value[3][0]==allValHost[0]);
							});
					    	$(this).css({color: oFontColor});
					    	if(changedElement.length==0)
					    		$('.span2.modify').html('');
				    	}
				    }
			    });
			}
		}
	});

	ctlTable = $('#caseTypeList').dataTable({
		"sDom": "<'row-fluid'<'span4'l><'span2 modify'><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
		"sPaginationType": "bootstrap",
		"bAutoWidth" : false,
		"iDisplayLength": 15,
		"sAjaxSource": '/configure/getSubCaseTypeTableList',
		"bSort": false,
		"aLengthMenu": [[15, 30, 50, 100, -1], [15, 30, 50, 100, "All"]],
		"oLanguage": {
			"sProcessing": "Processing...",
			"sLoadingRecords": "Loading...",
			"sLengthMenu": "_MENU_"
		},
		"aoColumns": [
	        { "sClass": "editable" },
	        { "sClass": "editable" },
	        { "sClass": "" }
	    ],
		"fnDrawCallback": function(){
			oFontColor = $(this).css('color');
			if($('#caseTypeList tbody .editable').length)
			{
				var allData = ctlTable.fnGetData();
				if($(allData).not(oCaseTypeData).length>0)
				{
					oCaseTypeData = allData;
					caseType = [];
					subCaseType = [];
					var ctdupes = {};
					var sctdupes = {};
					$.each(allData, function(key, value){
						var ct = $(value[2]).attr('data-casetype');
						var ctId = ct.split('|')[0];
						var ctName = ct.split('|')[1];
						var sct = $(value[2]).attr('data-subcasetype');
						var sctId = sct.split('|')[0];
						var sctName = sct.split('|')[1];

						var temp = {};
						if(!ctdupes[ct]&&ctName.length>0)
						{
							ctdupes[ct] = true;
							temp['k'] = ctId;
							temp['v'] = ctName;
							caseType.push(temp);
						}
						temp = {};
						if(!sctdupes[sct]&&sctName.length>0)
						{
							sctdupes[sct] = true;
							temp['pk'] = ctId;
							temp['k'] = sctId;
							temp['v'] = sctName;
							subCaseType.push(temp);
						}
					});
				}
				
				$('#caseTypeList tbody .editable').inlineEdit({
					buttons: '<a href="#" class="save"><i class="icon-ok"></i></a>',
					buttonsTag: 'a',
  					cancelOnBlur: true,
  					placeholder: 'Click to add',
				    save: function(e, data) {
				    	var dataRef;
				    	var colIndex = $(this).index();
				    	var rowIndex = $(this).parent().index();

				    	dataRef = getDataRefByColumnIndex(colIndex, 'caseType');

				    	valIndex = getDataSourceArrayByColumn($(this), 'caseType')[0];
				    	oldVal = getDataSourceArrayByColumn($(this), 'caseType')[1];
				    	allValHost = getAllDataSourceByColumn($(this));

				    	if(data.value!=oldVal)
				    	{
				    		existingElement = $.grep(changedElement, function(value) {
								return (value[0][0]==rowIndex&&value[0][1]==colIndex&&value[3][0]==allValHost[0]);
							});
							if(existingElement.length==1)
							{
								changedElement[$.inArray(existingElement[0], changedElement)] = new Array(new Array(rowIndex, colIndex), new Array(valIndex, data.value, dataRef), oldVal, allValHost);
							}
							else
				    			changedElement.push(new Array(new Array(rowIndex, colIndex), new Array(valIndex, data.value, dataRef), oldVal, allValHost));
					    	$(this).css({color: '#08c'});
					    	$('.span2.modify').html('<a class="btn" id="applyAll"><i class="icon-ok"></i></a><a class="btn" id="cancelAll"><i class="icon-remove"></i></a>');
				    	}
				    	else
				    	{
				    		changedElement = $.grep(changedElement, function(value) {
								return !(value[0][0]==rowIndex&&value[0][1]==colIndex&&value[3][0]==allValHost[0]);
							});
					    	$(this).css({color: oFontColor});
					    	if(changedElement.length==0)
					    		$('.span2.modify').html('');
				    	}
				    }
			    });
			}
		}
	});
}

function setChangedValue(position, newValue, oldValue, rawDataHost)
{
	reObj = new object();
	reObj.Position.x = position[1];
	reObj.Position.y = position[0];
	reObj.NewValue.index = newValue[0];
	reObj.NewValue.value = newValue[1];
	reObj.NewValue.type = NewValue[2];
	reObj.OldValue.value = oldValue;
	reObj.RawDataHost = rawDataHost;

	return reObj;
}

function getDataRefByColumnIndex(colIndex, listType)
{
	dataRef = '';
	if(listType&&listType.toLowerCase()=='casetype')
	{
		if(colIndex==0)
			dataRef = 'data-casetype';
		else if(colIndex==1)
			dataRef = 'data-subcasetype';
	}
	else
	{
		if(colIndex==0)
			dataRef = 'data-systemunit';
		else if(colIndex==1)
			dataRef = 'data-functionunit';
		else if(colIndex==2)
			dataRef = 'data-subfunctionunit';
	}

	return dataRef;
}

function getParentDataRefByColumnIndex(colIndex)
{
	parentDataRef = '';
	if(colIndex==0)
		parentDataRef = '';
	else if(colIndex==1)
		parentDataRef = 'data-systemunit';
	else if(colIndex==2)
		parentDataRef = 'data-functionunit';

	return parentDataRef;
}

function getParentDataRefByDataRef(childDataRef)
{
	parentDataRef = '';
	childDataRef = childDataRef.toLowerCase();
	if(childDataRef=='data-systemunit')
		parentDataRef = '';
	else if(childDataRef=='data-functionunit')
		parentDataRef = 'data-systemUnit';
	else if(childDataRef=='data-subfunctionunit')
		parentDataRef = 'data-functionUnit';
	else if(childDataRef=='data-casetype')
		parentDataRef = '';
	else if(childDataRef=='data-subcasetype')
		parentDataRef = 'data-casetype';

	return parentDataRef;
}

function transferToJson(toBeTransfer)
{
	reArray = [];
	reJson = {};
	$.each(toBeTransfer, function(key, value){
		transferObj = {};
		transferObj.DataType = value[1][2];
		transferObj.Index = value[1][0];
		transferObj.Value = value[1][1];
		if(transferObj.Index=='')
		{
			transferObj.Index = 'p|'+value[3].attr(getParentDataRefByDataRef(value[1][2])).split('|')[0];
		}
		reArray.push(transferObj);
	});
	reJson = reArray;
	return JSON.stringify(reJson);
}

function getDataSourceArrayByColumn(column, listType)
{
	dataRef = getDataRefByColumnIndex(column.index(), listType);
	return column.parent().find('td').last().find('a').attr(dataRef).toString().split('|');
}

function getAllDataSourceByColumn(column)
{
	return column.parent().find('td').last().find('a');
}

var previousPoint;
function highCharts(dataArray)
{
	var ChartsTitle = '<b>'+selectedDate.getFullYear()+' Cases</b>';
	var ChartsSubTitle = 'Jan ~ Dec';
	var series = [];
	var totalAmountPerMonth = [];
	/*$.each(dataArray, function(key, value){
		$.each(value, function(k, v){
			if(!totalAmountPerMonth[k])
			{
				totalAmountPerMonth[k]=[v[0], 0];
			}
			totalAmountPerMonth[k][1]+=v[1];
		});
	});*/
	series.push({
		type: 'column',
		data: dataArray.Total,
		name: 'Total',
		events: {
			click: function(event) {
				switchToDetailReport(selectedDate.getFullYear(), new Date(event.point.x).getMonth()+1);
			}
		},
		allowPointSelect: true,
		yAxis: 0
	});
	delete dataArray.Total;

	series.push({
		type: 'spline',
		data: dataArray.CaseAge,
		name: 'Case Age',
		dashStyle: 'dash',
		dataLabels: {
			enabled: true,
			style: {
				color: '#bbb'
			},
			formatter: function(){ return this.point.y+' h'; }
		},
		color: '#ccc',
		allowPointSelect: false,
		yAxis: 1,
		tooltip: {
			enabled: false
		}
	});
	delete dataArray.CaseAge;

	$.each(dataArray, function(key, value){
		for(i=value.length-1;i>-1;i--){
			if(value[i][1]==0)
			{
				value.splice(i,1);
			}
			else
				break;
		}
		series.push({
			type: 'spline',
			data: value,
			name: key,
			shadow: true,
			dataLabels: {
				enabled: false
			},
			marker: {
				fillColor: '#EEE',
				lineWidth: 2,
				lineColor: null
			},
			yAxis: 0
		});
	});
	$('#HCmonthlyReport').highcharts({
        chart: {
            height: 420
        },
		exporting: {
			sourceWidth: 860,
			sourceHeight: 380
		},
        credits: {
        	enabled: false
        },
        title: {
            text: ChartsTitle
        },
        subtitle: {
        	text: ChartsSubTitle
        },
        xAxis: {
            type: 'datetime',
            tickInterval: 1000*60*60*24*30,
            minTickInterval: 1000*60*60*24*30,
            dateTimeLabelFormats: {month: '%b'}
        },
        yAxis: [{
			title: {
				text: '# case'
			}
        },
        {
        	min: 5,
        	title: {
				text: 'hour'
			},
			opposite: true
        }],
		tooltip: {
			formatter: function(){
				var tooltipStr = '';
				if(this.y>0)
				{
					tooltipStr+=Highcharts.dateFormat('%B, %Y', this.x);
					tooltipStr+='<br>'
					$.each(this.points, function(i, point) {
						if(point.series.name.toLowerCase()!='total'&&point.series.name.toLowerCase()!='case age')
							tooltipStr += '<br/><span style="color:'+point.series.color+';">'+ point.series.name +'</span>: <b>'+point.y+'</b>';
					});
					tooltipStr+='<br><br>Click for more.';
				}
				else
					tooltipStr = false;
				return tooltipStr;
			},
			shared: true,
			style: {
				fontFamily: '"Open Sans", verdana,"Ubuntu", sans-serif',
				fontWeight: '600'
			}
		},
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true
                },
				cursor: 'pointer',
				states: {
					select: {
						color: 'rgba(38, 60, 83, 0.7)',
						borderColor: '#fff'
					}
				}
            }
        },
        series: series
    });
}

function callForCaseReportByYear()
{
	if($("#HCmonthlyReport").length)
	{
		$.ajax({
			url:"/cases/viewMonthlyCaseByYear/"+selectedDate.getFullYear(),
			success:function(data){
				highCharts(JSON.parse(data));
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			}
		});
	}
}

function callForMonthlyFunctionUnitAmount(systemUnitId)
{
		$.ajax({
			url:"/cases/viewMonthlyFunctionUnit/"+systemUnitId,
			beforeSend: function(){
				if(systemUnitTrendsChart!=null)
					systemUnitTrendsChart.showLoading('Loading...');
			},
			success:function(data){
				systemUnitTrendsChart = DrawTrendsChart(systemUnitTrendsChart, JSON.parse(data), '#trends-functionunit-content');
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			}
		});
}

function callForMonthlyCaseTypeAmount()
{
	if($('#trends-casetype-content').length>0)
	{
		$.ajax({
			url:"/cases/viewMonthlyCaseType",
			beforeSend: function(){
				if(caseTypeTrendsChart!=null)
					caseTypeTrendsChart.showLoading('Loading...');
			},
			success:function(data){
				caseTypeTrendsChart = DrawTrendsChart(caseTypeTrendsChart, JSON.parse(data), '#trends-casetype-content');
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			}
		});
	}
}

function callForEmergencyCaseReportByYear()
{
	if($('#emergencyCaseTimeline').length)
	{
		var renderTimeline = function(rawData){
			if(JSON.parse(rawData).length)
			{
				transferJsonToTimelineHtmlNode(JSON.parse(rawData));
				$('#emergencyCaseTimeline').timelinexml();
			}
			else
			{
				$('#emergencyCaseTimeline').html('No Emergency Case yet.');
			}
		};
		fetchEmergencyCase(selectedDate.getFullYear(), '', renderTimeline);
	}
}

function callForRecentOneWeekCaseReport()
{
	if($("#weeklyCaseReport").length)
	{
		$.ajax({
			url:"/cases/viewDailyCaseByWeek/"+selectedDate.getFullYear()+"/"+(selectedDate.getMonth()+1)+"/"+selectedDate.getDate(),
			success:function(data){
				$("#weeklyCaseReport").highcharts({
					chart: {
		                type: 'line',
		                height: 200
					},
					credits: {
						enabled: false
					},
					title : {
						text : 'Recent 1 week Overview'
					},
					xAxis: {
						type: 'datetime',
						labels: {
							formatter: function() {
								return Highcharts.dateFormat('%a', this.value);                  
							}
						},
						tickInterval: 1000*60*60*24,
						minTickInterval: 1000*60*60*24
					},
					yAxis: {
						title: {
							text: '# Cases'
						},
						min: 0
					},
					tooltip: {
						enabled: false
					},
					series : [{
						dataLabels: {
	                        enabled: true,
	                        distance: -2
	                    },
						name : 'Total Case #',
						marker : {
							enabled : true,
							radius : 3
						},
						data : JSON.parse(data)
					}]
				});
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			}
		});
	}
}

function switchToDetailReport(year, month)
{
	selectedDate = setSelectedDate(year, month);

	callForReportData(year, month);
}

var requestResults;
var appenders;
var names;
var chartOptions;
function callForReportData(year, month)
{
	requestResults = [];
	appenders = [];
	names = [];
	chartOptions = [];

	$('#SystemUnit').html('<div id="loading"><div class="center"></div></div>');
	$('#CaseType').html('<div id="loading"><div class="center"></div></div>');

	callForCaseSystemUnit(year, month);
}

function callForCaseSystemUnit(year, month)
{
	$.ajax({
		url:"/cases/viewSystemUnit/"+year+"/"+month,
		success:function(data){
			result = (JSON.parse(data)).concat();
			requestResults.push(result);
			appenders.push('SystemUnit');
			names.push('System Unit');
			chartOptions.push(null);
			callForCaseFunctionUnit(year, month);
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: ' + error);
		}
	});
}

function callForCaseSystemUnitByTimeRange(startDate, endDate)
{
	requestResults = [];
	appenders = [];
	names = [];
	chartOptions = [];

	$.ajax({
		type: 'POST',
		url: '/cases/viewSystemUnitByTimeRange',
		dataType: 'json',
		data: JSON.stringify({
			from: startDate,
			to: endDate
		}),
		success:function(response){
			requestResults.push(response);
			appenders.push('SystemUnitByTimeRange');
			names.push('System Unit By Time Range');
			chartOptions.push(null);
			callForCaseFunctionUnitByTimeRange(startDate, endDate);
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		}
	});
}

function callForCaseFunctionUnit(year, month)
{
	$.ajax({
		url:"/cases/viewFunctionUnit/"+year+"/"+month,
		success:function(data){
			result = (JSON.parse(data)).concat();
			requestResults.push(result);
			appenders.push('SystemUnit');
			names.push('Function Unit');
			chartOptions.push(null);
			callForCaseType(year, month);
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		}
	});
}

function callForCaseFunctionUnitByTimeRange(startDate, endDate)
{
	$.ajax({
		type: 'POST',
		url: '/cases/viewFunctionUnitByTimeRange',
		dataType: 'json',
		data: JSON.stringify({
			from: startDate,
			to: endDate
		}),
		success:function(response){
			requestResults.push(response);
			appenders.push('SystemUnitByTimeRange');
			names.push('Function Unit By Time Range');
			chartOptions.push({subtitle:{text: null}});
			drawDetailCharts(requestResults, appenders, names, undefined, chartOptions);
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		}
	});
}

function fetchEmergencyCase(year, month, successCallback)
{
	$.ajax({
		url: '/cases/viewEmergencyCaseList/' + year+'/'+month,
		success: function(data){
			successCallback(data);
		},
		error: function() {
			alert('there was an error while fetching events!');
		}
	});
}

function callForCaseSystemUnitDetail(clickedPoint)
{
	requestResults=[];
	appenders=[];
	names=[];
	chartOptions = [];
	year = selectedDate.getFullYear();
	month = selectedDate.getMonth()+1;
	startDate = moment(year+'-'+month).startOf('month').utc().format('YYYY-MM-DD HH:mm');
	endDate = moment(year+'-'+month).endOf('month').utc().format('YYYY-MM-DD HH:mm');

	$.ajax({
		url:"/cases/viewSystemUnitDetail/"+clickedPoint.id+"/"+year+"/"+month,
		success:function(data){
			data = (JSON.parse(data)).concat();
			result = [];
			$.each(data, function(key, value){
				value.push(startDate);
				value.push(endDate);
				result.push(value);
			});
			setInfoList("#CaseDetailModal .modal-body", result, clickedPoint.name, clickedPoint.id, 'functionUnit');
			$("#CaseDetailModal .modal-body .footer .item").click();

			//drawDetailCharts(requestResults, appenders, names);
			centerModal($('#CaseDetailModal'));
			//$('#CaseChartModal').modal('show');
		},
		complete:function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend:function(){
			showModal($('#CaseDetailModal'), true);
			//loadingNoty();
		}
	});
}

function callForCaseSystemUnitDetailByTimeRange(clickedPoint)
{
	requestResults=[];
	appenders=[];
	names=[];
	chartOptions = [];
	startDate = overviewlistRangeStart.utc().format('YYYY-MM-DD HH:mm');
	endDate = overviewlistRangeEnd.utc().format('YYYY-MM-DD HH:mm');

	$.ajax({
		type: 'POST',
		url: "/cases/viewSystemUnitDetailByTimeRange/"+clickedPoint.id,
		dataType: 'json',
		data: JSON.stringify({
			from: startDate,
			to: endDate
		}),
		success:function(response){
			result = [];
			$.each(response, function(key, value){
				value.push(startDate);
				value.push(endDate);
				result.push(value);
			});
			setInfoList("#CaseDetailModal .modal-body", result, clickedPoint.name, clickedPoint.id, 'functionUnit');
			$("#CaseDetailModal .modal-body .footer .item").click();

			//drawDetailCharts(requestResults, appenders, names);
			centerModal($('#CaseDetailModal'));
			//$('#CaseChartModal').modal('show');
		},
		complete:function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend:function(){
			showModal($('#CaseDetailModal'), true);
			//loadingNoty();
		}
	});
}

function callForCaseFunctionUnitDetail(clickedPoint)
{
	requestResults=[];
	appenders=[];
	names=[];
	chartOptions = [];
	year = selectedDate.getFullYear();
	month = selectedDate.getMonth()+1;
	startDate = moment(year+'-'+month).startOf('month').utc().format('YYYY-MM-DD HH:mm');
	endDate = moment(year+'-'+month).endOf('month').utc().format('YYYY-MM-DD HH:mm');

	$.ajax({
		type: 'post',
		dataType: 'json',
		url:"/cases/viewFunctionUnitDetail/"+clickedPoint.id,
		data: JSON.stringify({
			from: startDate,
			to: endDate
		}),
		success:function(data){
			result = [];
			$.each(data, function(key, value){
				value.push(startDate);
				value.push(endDate);
				result.push(value);
			});

			setInfoList("#CaseDetailModal .modal-body", result, clickedPoint.x+' - '+clickedPoint.name, clickedPoint.id, 'subFunctionUnit');
			$("#CaseDetailModal .modal-body .footer .item").click();
/*
			requestResults.push(result);
			appenders.push($('#CaseDetailModal .modal-body .modal-body-chart')[0]);
			names.push(functionUnitName+' Case Detail');

			drawDetailCharts(requestResults, appenders, names);*/
			centerModal($('#CaseDetailModal'));
			//$('#CaseChartModal').modal('show');
		},
		complete:function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend:function(){
			showModal($('#CaseDetailModal'), true);
			//loadingNoty();
		}
	});
}

function callForCaseFunctionUnitDetailByTimeRange(clickedPoint){
	requestResults=[];
	appenders=[];
	names=[];
	chartOptions = [];
	startDate = overviewlistRangeStart.utc().format('YYYY-MM-DD HH:mm');
	endDate = overviewlistRangeEnd.utc().format('YYYY-MM-DD HH:mm');

	$.ajax({
		type: 'post',
		dataType: 'json',
		url:"/cases/viewFunctionUnitDetail/"+clickedPoint.id,
		data: JSON.stringify({
			from: startDate,
			to: endDate
		}),
		success:function(data){
			result = [];
			$.each(data, function(key, value){
				value.push(startDate);
				value.push(endDate);
				result.push(value);
			});

			setInfoList("#CaseDetailModal .modal-body", result, clickedPoint.x+' - '+clickedPoint.name, clickedPoint.id, 'subFunctionUnit');
			$("#CaseDetailModal .modal-body .footer .item").click();
/*
			requestResults.push(result);
			appenders.push($('#CaseDetailModal .modal-body .modal-body-chart')[0]);
			names.push(functionUnitName+' Case Detail');

			drawDetailCharts(requestResults, appenders, names);*/
			centerModal($('#CaseDetailModal'));
			//$('#CaseChartModal').modal('show');
		},
		complete:function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend:function(){
			showModal($('#CaseDetailModal'), true);
			//loadingNoty();
		}
	});
}

var caseTypeStatistics;
function callForCaseTypeBy(id, parentId, idType, ajaxOptions)
{
	requestResults=[];
	appenders=[];
	names=[];
	chartOptions = [];
	sizeAdjusts=[];

	requestURL = "/cases/viewCaseTypeBy/"+id+"/"+idType;
	if(parentId!=null&&parentId>0)
	{
		requestURL="/cases/viewCaseTypeBy/"+parentId+"/"+(idType-1);
	}

	var caseTypes={};
	var chartContainer = $('#CaseDetailModal .modal-body .chart');
	var tableContainer = $('#CaseDetailModal .modal-body .table');
	var option = {
		type: 'POST',
		dataType: 'JSON',
		url: requestURL,
		success: function(data){
			caseTypeStatistics = data;

			caseTypes = getCaseTypeFromSubFunctionUnit(caseTypeStatistics, null, tableContainer, true);

			requestResults.push($.map(caseTypes, function(value, key){return [[key, value[0], value[1]]]}));
			appenders.push(chartContainer[0]);
			names.push('Detail');
			sizeAdjusts.push(0.6);

			drawDetailCharts(requestResults, appenders, names, sizeAdjusts, {exporting:{sourceWidth: 276, sourceHeight: 193}});
		},
		complete: function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend: function(){
			chartContainer.html('');
			tableContainer.html('<div id="loading" class="center"><div class="center"></div></div>');
			//loadingNoty();
		}
	};
	$.extend(option, ajaxOptions);
	$.ajax(option);
}

function callForFunctionUnitBy(id, parentId, idType)
{
	requestResults=[];
	appenders=[];
	names=[];
	chartOptions = [];
	sizeAdjusts=[];
	year = selectedDate.getFullYear();
	month = selectedDate.getMonth()+1;

	requestURL = "/cases/viewFunctionUnitBy/"+id+"/"+year+"/"+month+"/"+idType;
	if(parentId!=null&&parentId>0)
	{
		requestURL="/cases/viewFunctionUnitBy/"+parentId+"/"+year+"/"+month+"/"+(idType-1);
	}

	var caseTypes={};
	var chartContainer = $('#CaseDetailModal .modal-body .chart');
	var tableContainer = $('#CaseDetailModal .modal-body .table');
	$.ajax({
		url: requestURL,
		success: function(data){
			caseTypeStatistics = (JSON.parse(data)).concat();

			caseTypes = getCaseTypeFromSubFunctionUnit(caseTypeStatistics, null, tableContainer, true);

			requestResults.push($.map(caseTypes, function(value, key){return [[key, value[0], value[1]]]}));
			appenders.push(chartContainer[0]);
			names.push('Detail');
			sizeAdjusts.push(0.6);

			drawDetailCharts(requestResults, appenders, names, sizeAdjusts);
		},
		complete: function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend: function(){
			chartContainer.html('');
			tableContainer.html('<div id="loading" class="center"><div class="center"></div></div>');
			//loadingNoty();
		}
	});
}

function callForCaseType(year, month)
{
	$.ajax({
		url:"/cases/viewCaseType/"+year+"/"+month,
		success:function(data){
			result = (JSON.parse(data)).concat();
			requestResults.push(result);
			appenders.push('CaseType');
			names.push('Case Type');
			chartOption = {
				chart: {
					type: 'bar',
					animation: {
						duration: 500
					}
				},
				xAxis: {
					categories: [],
					title: {
						text: null
					},
					labels: {
						useHTML: true,
						align: 'left',
						x: 10,
						y: 4,
						style: {
							font: 'normal 13px "Open Sans"',
							color: '#0d232a',
							width: this.width
						}
					}
				},
				yAxis: {
					min: 0,
					title: {
						text: null
					}
				},
				plotOptions: {
					bar: {
						dataLabels: {
							enabled: true
						},
						pointPadding: -0.25,
						color: '#1aadce',
						formatter: function() {
							return this.point.y;
						}
					}
				},
				legend: {
					enabled: false
				},
				dataLabels: {
					enabled: true
				}
			};
			chartOptions.push(chartOption);
			drawDetailCharts(requestResults, appenders, names, undefined, chartOptions);
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		}
	});
}

function callForCaseTypeDetail(clickedPoint, year, month)
{
	requestResults=[];
	appenders=[];
	names=[];
	chartOptions = [];
	year = selectedDate.getFullYear();
	month = selectedDate.getMonth()+1;
	startDate = moment(year+'-'+month).startOf('month').utc().format('YYYY-MM-DD HH:mm');
	endDate = moment(year+'-'+month).endOf('month').utc().format('YYYY-MM-DD HH:mm');

	$.ajax({
		url:"/cases/viewCaseTypeDetail/"+clickedPoint.id+"/"+year+"/"+month,
		success:function(data){
			data = (JSON.parse(data)).concat();
			result = [];
			$.each(data, function(key, value){
				value.push(startDate);
				value.push(endDate);
				result.push(value);
			});
			setInfoList("#CaseDetailModal .modal-body", result, clickedPoint.name, clickedPoint.id, 'caseType');
			$("#CaseDetailModal .modal-body .footer .item").click();

			/*requestResults.push(result);
			appenders.push($('#CaseDetailModal .modal-body')[0]);
			names.push(casetypeName+' Detail');

			drawDetailCharts(requestResults, appenders, names);*/
			centerModal($('#CaseDetailModal'));
			//$('#CaseChartModal').modal('show');
		},
		complete:function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend:function(){
			showModal($('#CaseDetailModal'), true);
			//loadingNoty();
		}
	});
}

function getCaseTypeListDetail(clickedPoint)
{
	var tableContainer = $('#CaseDetailModal .modal-body .table');
	getCaseTypeFromSubFunctionUnit(caseTypeStatistics, clickedPoint.id, tableContainer);
}

function callForCaseOverview(year, month, appender)
{
	var result = [];
	var syncStatus = 0;

	if(appender.length)
	{
		$.ajax({
			url:"/cases/viewCaseOverview/"+year+"/"+month,
			success:function(data){
				result = (JSON.parse(data)).concat();
				setDataTable(appender, result);
			},
			complete:function(){
				finishRefreshTable();
				centerModal($('#CaseDataModal'));
				//closeLoadingNoty();
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			},
			beforeSend:function(){
    			setDateSelector();
				resetDataTable(appender);
				startRefreshTable();
				showModal($('#CaseDataModal'));
				//$('#CaseDataModal').modal('show');
				//loadingNoty();
			}
		});
	}
}

function callForCaseListOverview(year, month, appender, startFromSync)
{
	var result = [];
	if(appender.length)
	{
		$.ajax({
			url:"/cases/viewCaseList/"+year+"/"+month,
			async: true,
			success:function(data){
				result = (JSON.parse(data)).concat();
				setSFDataTable(appender, result);
				loadCaseByAnchor();
			},
			complete: function(){
				if(startFromSync)
				{
					syncStatus = 1;
					n.setText('Done.');
					n.setTimeout(500);
				}
				finishRefreshTable();
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			}
		});
	}
}

function callForOpenCaseListOverview(appender)
{
	var result = [];
	if($(appender).length)
	{
		$.ajax({
			url:"/cases/viewOpenCase",
			success:function(data){
				result = (JSON.parse(data)).concat();
				setSFDataTable(appender, result);
				loadCaseByAnchor();
			},
			complete: function(){
				finishRefreshTable();
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			},
			beforeSend: function(){
				startRefreshTable();
			}
		});
	}
}

function callForCaseAmountOverview()
{
	$.ajax({
		url:"/cases/viewDailySystemUnitAmountOverview",
		success:function(data){
			cases = (JSON.parse(data));
			var callback = function(data){
				DrawWeeklyCaseChart(cases, JSON.parse(data), '#CaseChartModal .modal-body');
				centerModal($('#CaseChartModal'));
			};
			fetchEmergencyCase(selectedDate.getFullYear(), '', callback);
			
			//$('#CaseChartModal').modal('show');
		},
		complete:function(){
			//closeLoadingNoty();
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		beforeSend:function(){
			showModal($('#CaseChartModal'), true)
			//loadingNoty();
		}
	});
}

function callForCaseFilterData(forceCall)
{
	if(forceCall||!$('#selectSystemUnit option').length||caseFilterData==null)
	{
		$.ajax({
			url:"/configure/getCaseFilter",
			async: false,
			success:function(data){
				caseFilterData = (JSON.parse(data));

				systemUnit = caseFilterData['SystemUnit'];
				functionUnit = caseFilterData['FunctionUnit'];
				subFunctionUnit = caseFilterData['SubFunctionUnit'];
				caseType = caseFilterData['CaseType'];
				subCaseType = caseFilterData['SubCaseType'];
			},
			complete:function(){
				//closeLoadingNoty();
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			},
			beforeSend:function(){
				//$('#CaseAddModal').modal('show');
				//loadingNoty();
			}
		});
	}
}

var caseHandlingChart;
function callForCaseHandlingInfo(startDate, endDate){
	var responseEmpty=false;
	var viewtype;
	var dataViewType = $('.overview-case-handling-info .dataViewType');
	if(dataViewType.length>0)
		viewtype = dataViewType.val();
	viewtype = viewtype?viewtype:'hour';
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "/cases/viewCaseHandlingInfo/"+viewtype,
		data: JSON.stringify({
			from: startDate,
			to: endDate
		}),
		beforeSend: function(){
			if(caseHandlingChart!=undefined)
			{
				caseHandlingChart.showLoading("Loading");
			}
		},
		success: function(response){
			var seriesDataArray = [];
			$.each(response, function(categoryName, categoryData){
				seriesData = [];
				$.each(categoryData, function(index, info){
					seriesData.push({
						x: info[0],
						y: info[1]
					});
				});
				responseEmpty|=(categoryData.length==0);
				if(categoryName=='CaseAge')
				{
					seriesDataArray.push({
						type: 'spline',
						marker: {
							enabled: true,
							symbol: 'circle',
							radius: 3
						},
						yAxis: 1,
						name: categoryName,
						data: seriesData,
						color: '#AA4643',
						tooltip: {
							valueSuffix: ' h'
						}
					});
				}
				else if(categoryName=='CaseAgeDistribution')
				{
					seriesData = [];
					$.each(categoryData, function(index, info){
						seriesData.push({
							name: info['Criteria'],
							y: parseInt(info['Amount'])
						});
					});

					seriesDataArray.push({
						type: 'pie',
						size: '40%',
						name: categoryName,
						data: seriesData,
						center: [300, 50],
						colors: [
									'#4572A7', 
									'#80699B', 
									'#3D96AE', 
									'#89A54E', 
									'#DB843D', 
									'#AA4643', 
									'#92A8CD', 
									'#A47D7C', 
									'#B5CA92'
								],
						dataLabels: {
							distance: 10,
							formatter: function() {
								reStr = this.point.name;
								reStr +=': '+ Highcharts.numberFormat(this.point.percentage,1) +'%';
								reStr = this.percentage > 1.5 ? reStr : null;
								return reStr;
							}
						}
					});
				}
				else
				{
					seriesDataArray.push({
						yAxis: 0,
						name: categoryName,
						data: seriesData
					});
				}
			});
			caseHandlingChart = undefined
			if(caseHandlingChart==undefined)
			{
				caseHandlingChart = new Highcharts.Chart({
					chart: {
						type: 'column',
						renderTo: 'case-handling-content',
						animation: {
							duration: 500
						}
					},
					colors: [
						'#1aadce','#624289'
					],
					title: {
						text: 'case created/closed statistics'
					},
					tooltip: {
						shared: true,
						formatter: function() {
							if(this.point!=null&&this.point.series.type=='pie')
								return this.point.name+'<br>Amount: <b>'+this.point.y+'</b><br>percentage: <b>'+Highcharts.numberFormat(this.point.percentage,1) +'%</b>';
							else{
								reStr = 'During '+this.x+'<br><br>';
								$.each(this.points, function(i, point){
									reStr+='<span style="color:'+point.series.color+';">'+point.series.name+'</span>: <b>'+point.y;
									if(point.series.name=='CaseAge')
										reStr+='h';
									reStr+='</b><br>';
								});
								return reStr;
							}
						}
					},
					xAxis: {
						tickInterval: 1,
						categories: (viewtype=='hour'?Hours:WeekDays)
					},
					yAxis: [{
						min: 0,
						title: {
							text: '#'
						}
					},
					{
						min: 0,
						max: 72,
						title: {
							text: 'hour'
						},
						opposite: true
					}],
					plotOptions: {
					},
					legend: {
						enabled: true
					},
					series: seriesDataArray
				});
				if(responseEmpty)
				{
					caseHandlingChart.hideLoading();
					caseHandlingChart.showLoading("No Data Available");
				}
			}
			else
			{
				caseHandlingChart.hideLoading();

				while(caseHandlingChart.series.length > 0)
					caseHandlingChart.series[0].remove(true);

				$.each(seriesDataArray, function(index, seriesResult){
					caseHandlingChart.addSeries(seriesResult);
					caseHandlingChart.xAxis[0].setCategories((viewtype=='hour'?Hours:WeekDays));
				});

				caseHandlingChart.redraw();
				if(responseEmpty)
				{
					caseHandlingChart.hideLoading();
					caseHandlingChart.showLoading("No Data Available");
				}
			}
		}
	});
}

function initialCaseFilter(forceCall)
{
	callForCaseFilterData(forceCall);
	setCaseFilter(caseFilterData);
	centerModal($('#CaseModal'));
}

function callForOpenCaseAmount(selector)
{
	if($(selector).length)
	{
		$.ajax({
			url:"/cases/getTotalAmount",
			success:function(data){
				result = (JSON.parse(data));
				updateTabNotification(selector, result);
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			}
		});
	}
}

function callForUpsertCase(oCaseData, needClose, type)
{
	var postUrl = "/cases/UpsertCase";
	var data = {};

	if(oCaseData==null||oCaseData['Subject']==null||oCaseData['Subject']!=$('#caseSubject').val())
		data['Subject'] = $('#caseSubject').val();
	if(oCaseData==null||oCaseData['Description']==null||oCaseData['Description']!=$('#caseDescription').html())
		data['Description'] = $('#caseDescription').html();
	if(oCaseData==null||oCaseData['CaseDate']==null||oCaseData['CaseDate']!=$('#caseDatetime').val())
		data['CaseDate'] = $('#caseDatetime').val();
	if(oCaseData==null||oCaseData['ResponseDate']==null||oCaseData['ResponseDate']!=$('#responseDatetime').val())
		data['ResponseDate'] = $('#responseDatetime').val();
	if(oCaseData==null||oCaseData['SubCaseType']==null||oCaseData['SubCaseType']!=$('#selectSubCaseType').val())
		data['SubCaseType'] = $('#selectSubCaseType').val();
	if(oCaseData==null||oCaseData['SubFunctionUnit']==null||oCaseData['SubFunctionUnit']!=$('#selectSubFunctionUnit').val())
		data['SubFunctionUnit'] = $('#selectSubFunctionUnit').val();
	if(oCaseData==null||oCaseData['CaseAge']==null||oCaseData['CaseAge']!=$('#caseAge').val())
		data['CaseAge'] = $('#caseAge').val();
	if(oCaseData==null||oCaseData['Amount']==null||oCaseData['Amount']!=$('#caseAmount').val())
		data['Amount'] = $('#caseAmount').val();

	if(type!=null&&type.toLowerCase()=='update')
	{
		if(Object.keys(data).length>0)
			data['CaseId'] = oCaseData['CaseId'];
		postUrl += "/update";
	}

	if(Object.keys(data).length>0)
	{
		$.ajax({
			type: "POST",
			url: postUrl,
			data: JSON.stringify(data),
			dataType: "json",
			async: false,
			contentType: "application/json",
			beforeSend: function(){
				$('#saveCase').button('loading');
				$('#savenewCase').button('loading');
			},
			success: function(response){
				$('#caseSubject').val("");
				$('#caseDescription').html("");
				if(type!=null&&type.toLowerCase()=='update')
				{
					oCaseData['Row'].find('.edit').attr('data-functionunitdetailid', $('#selectSubFunctionUnit').val());
					oCaseData['Row'].find('.edit').attr('data-casetypedetailid', $('#selectSubCaseType').val());
					if(data['CaseAge']!=null)
					{
						oCaseData['Row'].find('.case-age').html(data['CaseAge']);
						oCaseData['Row'].find('.caseAge').html(data['CaseAge']);
					}
					if(data['ResponseDate']!=null)
					{
						oCaseData['Row'].find('.response-time').html(moment(data['ResponseDate']).diff(moment(oCaseData['Row'].find('.createDate').html()), 'minutes'));
						oCaseData['Row'].find('.responseDate').html(data['ResponseDate']);
					}
					if(data['Subject']!=null)
					{
						oCaseData['Row'].find('.data').html(data['Subject']);
						oCaseData['Row'].find('.data').attr('data-original-title', data['Subject']);
					}
					if(data['Description']!=null)
						oCaseData['Row'].find('.data').attr('data-content', data['Description']);
					if(data['SubFunctionUnit']!=null)
						oCaseData['Row'].find('.functionUnit').html($('#selectSubFunctionUnit option:selected').text());
					if(data['SubCaseType']!=null)
						oCaseData['Row'].find('.caseType').html($('#selectSubCaseType option:selected').text());
					$('[rel="popover"],[data-rel="popover"]').popover({placement: 'bottom'});
				}
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			},
			complete: function(){
				$('#saveCase').button('reset');
				$('#savenewCase').button('reset');
			}
		});
	}
	
	if(needClose)
	{
		$('#CaseModal').modal('hide');
	}
}

function callForUpsertCaseReport(needClose)
{
	var d = $('#casedate').val();
	var ctid = $('#selectCaseType').val();
	var sctid = $('#selectSubCaseType').val();
	var fuid = $('#selectFunctionUnit').val();
	var sfuid = $('#selectSubFunctionUnit').val();
	var a = $('#caseAmount').val();

	$.ajax({
		url:"/cases/AddCaseReport/"+d+"/"+ctid+"/"+fuid+"/"+a,
		async: false,
		success:function(data){
			if(data)
			{
				$('#saveCase').button('reset');
				$('#savenewCase').button('reset');
				if(needClose)
				{
					$('#CaseModal').modal('hide');
				}
				finishCaseAdd();
			}
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		}
	});
}

function callForAddConfig(dataTable, data, oType, needClose)
{
	var alias = '';

	if(oType == 'SystemUnit')
		alias = 'SU';
	else if(oType == 'FunctionUnit')
		alias = 'FU';
	else if(oType == 'SubFunctionUnit')
		alias = 'SFU';
	else if(oType == 'CaseType')
		alias = 'CT';
	else if(oType == 'SubCaseType')
		alias = 'SCT';

	$.ajax({
		type: "POST",
		url: "/configure/updateCaseFilter",
		data: data,
		async: false,
		dataType: "json",
		contentType: "application/json",
		beforeSend: function(){
			if(oType)
			{
				$('#save'+alias+'Add').button('loading');
				$('#savenew'+alias+'Add').button('loading');
			}
		},
		success: function(response){
			if(dataTable.fnReloadAjax)
				dataTable.fnReloadAjax();
			else
			{
				initialCaseFilter(true);
			}
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		complete: function(){
			if(oType)
			{
				$('#save'+alias+'Add').button('reset');
				$('#savenew'+alias+'Add').button('reset');
			}
			if(needClose)
			{
				popupAddHide('#add'+oType+'Modal');
			}
		}
	});
}

function popupAddShow(selector)
{
	$(selector).modal('show');
	var historyRow = $(selector).find('tbody tr:last');
	if(historyRow.find('td:first').text()==addLogHeader)
	{
		historyRow.remove();
	}
}
function popupAddHide(selector)
{
	$(selector).modal('hide');
}

var n;
var syncStatus=-1;
function callForSyncSFCase(year, month)
{
	$.ajax({
		url:"/cases/SyncCaseFromSalesforce/"+year+"/"+month,
		beforeSend:function(){
			startRefreshTable();

			syncStatus=-1;
			$.noty.closeAll();
			n = $('#chartReminder').noty({
				layout: 'inline',
				type: 'information',
				template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
				timeout: false,
				text: "Syncing data from Salesforce...", 
				callback: {
				    onClose: function() {
				    	if(syncStatus<=0)
				    		return false;
				    }
				}
			});
		},
		success:function(data){
			if(data)
			{
				syncStatus = 0;
				refreshSFDataTable('#SFCaseDataTable', true);
				callForOpenCaseAmount('.notification-count');
			}
		},
		error:function(){
			syncStatus = 1;
			$.noty.closeAll();
			n = $('#chartReminder').noty({
				layout: 'inline',
				type: 'error',
				template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
				timeout: 1000,
				text: "Error while fetching data from Salesforce..."
			});
		}
	});
}

function callForCaseSourceDistributionByCaseCategory(caseCategoryTypeId, Id, from, to, caseCategoryName){
	$.ajax({
		type: 'post',
		dataType: 'json',
		url: '/cases/viewCaseSourceBy/'+Id+'/'+caseCategoryTypeId,
		data: JSON.stringify({
			from: from,
			to: to
		}),
		beforeSend: function(){
			showModal($('#CaseDetailModal'), true);
		},
		success: function(response){
			drawCaseSourceChartInModal('#CaseDetailModal .modal-body', response, 'Case Distribution of '+caseCategoryName);
			centerModal($('#CaseDetailModal'));
		}
	});
}

function getCaseTypeFromSubFunctionUnit(sourceData, caseTypeId, appender, needCount)
{
	var caseTypes,
		detailRow = '';
	if(needCount)
		caseTypes = {};
	$.each(sourceData, function(key, value){
		if(caseTypeId==null||caseTypeId==value[1])
			detailRow+='<tr><td class="first">'+value[0]+'</td><td>'+value[2]+'</td><td>'+value[4]+'</td></tr>';
		if(caseTypes!==undefined)
		{
			if(caseTypes[value[0]]===undefined)
			{
				caseTypes[value[0]] = [];
				caseTypes[value[0]][0] = 0;
				if(caseTypes[value[0]][1]===undefined)
					caseTypes[value[0]][1] = value[1];
			}
			caseTypes[value[0]][0]+=value[4];
		}
	});

	appender.html('');
	//caseTypeList = '<table class="table table-condensed"><thead><tr><th>Case Type</th><th>Detail</th><th>Amount</th></tr></thead><tbody>'+detailRow+'</tbody></table>';
	appender.append('<div class="body"><table class="table table-condensed"><thead><tr><th>Case Type</th><th>Detail</th><th>Amount</th></tr></thead><tbody>'+detailRow+'</tbody></table></div>');
	appender.find('tbody tr').each(function(){
		var rowSpan = 1;
		while($(this).nextAll('tr').find('.first').length>0&&$(this).find('.first').text()==$(this).nextAll('tr').find('.first')[0].innerText)
		{
			$($(this).nextAll('tr').find('.first')[0]).remove();
			rowSpan++;
		}
		$(this).find('.first').attr('rowSpan', rowSpan);
	});

	if(caseTypes!==undefined)
		return caseTypes;
}

function resetDataTable(selector)
{
	datatable = $(selector).dataTable();
	datatable.fnClearTable();
}

function setDataTable(selector, data)
{
	datatable = $(selector).dataTable();
	datatable.fnClearTable();
    datatable.fnAddData(data);
	datatable.fnSortNeutral();
    setDateSelector();
    
	//$('#CaseDataModal').modal('show');
}

function setSFDataTable(selector, data)
{
	caseTable = $(selector).dataTable();
	caseTable.fnClearTable();
    caseTable.fnAddData(data);
	caseTable.fnSortNeutral();

    setDateSelector();
}

function updateTabNotification(selector, data)
{
	/*
	$(selector+' .value').html(data[0]);
	$(selector+' .icon32').attr('title', 'last sync at: '+data[1]);
	$(selector+' .icon').attr('title', 'last sync at: '+data[1]);
	$(selector+' .icon-refreshing').attr('title', 'last sync at: '+data[1]);
	$(selector+' span.notification').html(data[2]);
	$(selector).attr('data-original-title', data[2]+' unclosed cases.');*/
	if(data[2]>0)
	{
		$('#newNotificationCount').html('1');
		$(selector).html('1');
		$(selector).css('top',-50).animate({top: "0px"});
		clearNewNotificationItem();
		addNewNotificationItem('<a href="/cases/lists#open=true" class="item">New Salesforce Case <span class="detail">'+data[2]+' in total</span></a>');
	}
}

function clearNewNotificationItem(){
	$('.pop-dialog .notifications a.item').remove();
}
function addNewNotificationItem(content){
	$('.pop-dialog .notifications h3').after(content);
}

function drawDetailCharts(dataList, appenderList, nameList, sizeAdjustList, seriesOptionList)
{
	var chart;
	var seriesOption;
	var options = [];
	var chartSize = 265;
	var innerSize = 0;
	var height = 360;
	var showLegend = true;
	var labelDistance = 30;
	var labelColor = '#000000';
	//var colors = ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];
	var colors = Highcharts.getOptions().colors;
	var customData = [];
	var systemUnitQueue = [];
	var showTitle = true;
	var slicedOffset = 0;

	if($('#CaseTypeDetail').highcharts()) $('#CaseTypeDetail').highcharts().destroy();
	if($('#SystemUnitDetail').highcharts()) $('#SystemUnitDetail').highcharts().destroy();
	if($('.chart').highcharts()) $('.chart').highcharts().destroy();

	for(i in dataList)
	{
		if(nameList[i].indexOf('System Unit')==0){
			chartSize = 190;
			innerSize = 0;
			showLegend = true;
			labelDistance = -45;
			labelColor = '#ffffff';
			showTitle = true;
			slicedOffset = 0;
			for(j in dataList[i])
			{
				systemUnitQueue.push(dataList[i][j][0]);
				customData.push({
					name: dataList[i][j][0],
					y: dataList[i][j][1],
					id: dataList[i][j][2],
					color: colors[parseInt(j)]
				});
			}
		}
		else if(nameList[i].indexOf('Function Unit')==0){
			chartSize = 245;
			innerSize = 190;
			showLegend = false;
			labelDistance = 15;
			labelColor = '#000000';
			showTitle = true;
			slicedOffset = 0;
			for(j in systemUnitQueue)
			{
				for(k in dataList[i])
					if(dataList[i][k][0]==systemUnitQueue[j])
						customData.push({
							x: dataList[i][k][0],
							name: dataList[i][k][1],
							y: dataList[i][k][2],
							id: dataList[i][k][3],
							color: colors[parseInt(j)]
						});
			}
		}
		else if(nameList[i].indexOf('Detail')==0){
			showLegend = false;
			labelDistance = 10;
			showTitle = false;
			slicedOffset = 5;
			for(j in dataList[i])
			{
				customData.push({
					name: dataList[i][j][0],
					y: dataList[i][j][1],
					id: dataList[i][j][2]
				});
			}
		}
		else{
			chartSize = 245;
			innerSize = 0;
			showLegend = true;
			labelDistance = 15;
			labelColor = '#000000';
			showTitle = true;
			slicedOffset = 0;
			for(j in dataList[i])
			{
				customData.push({
					name: dataList[i][j][0],
					y: dataList[i][j][1],
					id: dataList[i][j][2]
				});
			}
		}
		
		if(sizeAdjustList!== undefined && sizeAdjustList[i]!== undefined)
		{
			chartSize*=sizeAdjustList[i];
			innerSize*=sizeAdjustList[i];
			height*=sizeAdjustList[i];
		}

		seriesOption = {
			id: nameList[i],
        	showInLegend: showLegend,
        	size: chartSize,
        	innerSize: innerSize,
        	data: customData,
        	name: nameList[i],
            cursor: 'pointer',
            allowPointSelect: true,
            slicedOffset: slicedOffset,
			animation: {
	            duration: 500
        	},
        	states: {
                select: {
                    color: 'rgba(38, 60, 83, 0.7)',
                    borderColor: '#fff'
                }
            },
            point:{
    			events: {
    				legendItemClick: function(e){
    					return false;
    				},
    				click: function(e){
    					if(e.point.series.name=='System Unit')
    					{
    						if(selectedDate.getFullYear()<=2013&&selectedDate.getMonth()<=6)
    							loadingNoty('Detailed data is not available before<br><b>August, 2013</b>.', true,'warning');
    						else
    							callForCaseSystemUnitDetail(e.point);
    					}
    					else if(e.point.series.name=='System Unit By Time Range')
						{
							callForCaseSystemUnitDetailByTimeRange(e.point);
						}
    					else if(e.point.series.name=='Function Unit')
    					{
    						if(selectedDate.getFullYear()<=2013&&selectedDate.getMonth()<=6)
    							loadingNoty('Detailed data is not available before<br><b>August, 2013</b>.', true,'warning');
    						else
    							callForCaseFunctionUnitDetail(e.point);
    					}
    					else if(e.point.series.name=='Function Unit By Time Range')
    					{
    						callForCaseFunctionUnitDetailByTimeRange(e.point);
    					}
    					else if(e.point.series.name=='Case Type')
    					{
    						if(selectedDate.getFullYear()<=2013&&selectedDate.getMonth()<=6)
    							loadingNoty('Detailed data is not available before<br><b>August, 2013</b>.', true,'warning');
    						else
    							callForCaseTypeDetail(e.point);
    					}
    					else if(e.point.series.name=='Detail')
    					{
    						if(selectedDate.getFullYear()<=2013&&selectedDate.getMonth()<=6)
    							loadingNoty('Detailed data is not available before<br><b>August, 2013</b>.', true,'warning');
    						else
    							getCaseTypeListDetail(e.point);
    					}
    				}
    			}
    		},
			dataLabels: {
				enabled: true,
				distance: labelDistance,
				connectorColor: '#000000',
				style: {color: labelColor},
				formatter: function() {
					reStr = this.point.y;
					if(this.series.type=='pie')
					{
						reStr = ''+ this.point.name +'';
						if(this.series.name != 'System Unit')
							reStr +=': '+ Highcharts.numberFormat(this.point.percentage,1) +'%';
						else
							reStr += '<br>'+Highcharts.numberFormat(this.point.percentage,1) +'%';
						reStr = this.percentage > 1.5 ? reStr : null;
					}
					return reStr;
				}
			}
    	};

    	options.push(seriesOption);
		customData = [];
    	if(nameList[i].indexOf('System Unit')==0)
    		continue;

    	chartOption = {
	        chart: {
	            type: 'pie',
	            height: height,
				renderTo: appenderList[i]
	        },
	        credits: {
	        	enabled: false
	        },
	        legend: {
	            layout: 'vertical',
	            itemStyle: {
			        fontSize: '10px'
			    },
			    y: 30,
	            align: 'right',
	            verticalAlign: 'top',
	            floating: true,
	            labelFormatter: function() {
                	return this.name +': '+this.y;
            	}
	        },
	        loading: {
	            labelStyle: {
	                color: 'black'
	            },
	            showDuration: 500,
	            hideDuration: 500
        	},
	        title: {
	            text: showTitle?'<h4>'+nameList[i]+'</h4>':''
	        },
	        subtitle:{
	        	text: showTitle?Months[selectedDate.getMonth()]+', '+selectedDate.getFullYear():'',
	        	style: {
	        		fontSize: '10px'
	        	}
	        },
	        tooltip: {
	            //xDateFormat: '%Y-%m-%d'
	            formatter: function(){
	            	if(this.point.series.type=='pie')
	        			return this.point.name+' #: <b>'+this.point.y+'</b><br>Percentage: <b>'+Highcharts.numberFormat(this.point.percentage,1)+'%</b>';
	        		else
	        			return this.point.name+' #: <b>'+this.point.y+'</b>';
	        	}
	        },
	        series: options
	    };

	    if(seriesOptionList!==undefined&&seriesOptionList[i]!==undefined)
	    	$.extend(true, chartOption, seriesOptionList[i]);

		chart = new Highcharts.Chart(chartOption);

		//if no data
		if(dataList[i].length==0)
		{
			chart.hideLoading();
			chart.showLoading("No Data Available");
			chart.setSize(chart.width, 100);
		}

		//chart.render();
		options = [];
    	//$("html, body").animate({scrollTop: $('#HCmonthlyReport').offset().top}, 100);
		//data = {};
	}
	
	/*
	chart = new Highcharts.Chart({
        chart: {
            type: 'pie',
            height: 420,
			renderTo: 'SystemUnit',
			animation: {
	            duration: 1000
        	}
        },
        title: {
            text: '<b>'+selectedDate.getFullYear()+', '+Months[selectedDate.getMonth()]+' - System Unit</b>'
        },
        tooltip: {
            //xDateFormat: '%Y-%m-%d'
            formatter: function(){
        		return this.point.name+': <b>'+this.point.y+'</b>';
        	}
        },
        series: [{
        	size: '80%',
        	data: data[0],
        	name: 'system unit',
            cursor: 'pointer',
            allowPointSelect: true,
            dataLabels: {
                        enabled: true,
                        connectorColor: '#000000',
                        style: {color: '#000000'},
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(1) +' %';
                        }
            }
    	}]
    });
	chart.render();

    chart = new Highcharts.Chart({
        chart: {
            type: 'pie',
            height: 420,
			renderTo: 'CaseCategory',
			animation: {
	            duration: 1000
        	}
        },
        title: {
            text: '<b>'+selectedDate.getFullYear()+', '+Months[selectedDate.getMonth()]+' - Case Category</b>'
        },
        tooltip: {
            //xDateFormat: '%Y-%m-%d'
            formatter: function(){
        		return this.point.name+': <b>'+this.point.y+'</b>';
        	}
        },
        series: [{
        	size: '80%',
        	data: data[1],
        	name: 'CaseCategory',
            cursor: 'pointer',
            allowPointSelect: true,
            dataLabels: {
                        enabled: true,
                        connectorColor: '#000000',
                        style: {color: '#000000'},
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(1) +' %';
                        }
            }
    	}]
    });
	chart.render();
    
	chart.addSeries({
		data: data[0], 
		type: 'pie', 
		name: 'system unit',
		center: ['30%','45%']
	});

	chart.addSeries({
		data: data[1],
		type: 'pie', 
		name: 'case category', 
		center: ['70%','45%']
    });*/

}

function drawTopListChart(topListChart, renderTo, categories, data){
	if(topListChart==undefined||topListChart==null)
	{
		topListChart = new Highcharts.Chart({
			chart: {
				type: 'bar',
				renderTo: renderTo,
				height: 350,
				animation: {
					duration: 500
				}
			},
			title: {
				text: 'Top 10 Case Category'
			},
			xAxis: {
				categories: [],
				title: {
					text: null
				},
				labels: {
					useHTML: true,
					align: 'left',
					x: 10,
					y: 4,
					style: {
						font: 'normal 13px "Open Sans"',
						color: '#0d232a',
						width: this.width
					}
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: null
				},
				labels: {
					enabled: true
				},
				gridLineWidth: 1
			},
			plotOptions: {
				series: {
					allowPointSelect: true,
					cursor: 'pointer',
					states: {
						select: {
							color: 'rgba(38, 60, 83, 0.7)',
							borderColor: '#fff'
						}
					}
				},
				bar: {
					dataLabels: {
						enabled: true
					},
					pointPadding: -0.25,
					color: '#1aadce',
					events: {
						click: function(e){
							if(typeof e.point.ctdid !="undefined" && e.point.ctdid!='')
								callForCaseSourceDistributionByCaseCategory(2, e.point.ctdid, data.extraData.from, data.extraData.to, e.point.category);
							else if(typeof e.point.ctid !="undefined" && e.point.ctid!='')
								callForCaseSourceDistributionByCaseCategory(1, e.point.ctid, data.extraData.from, data.extraData.to, e.point.category);
							else
								return;
						}
					}
				}
			},
			legend: {
				enabled: false
			}
		});
	}

	topListChart.hideLoading();
	topListChart.xAxis[0].setCategories(categories);
	if(topListChart.series[0])
		topListChart.series[0].remove();
	topListChart.addSeries({data:data.chartData,name:'Amount'});
	
	if(data.chartData.length==0)
	{
		topListChart.hideLoading();
		topListChart.showLoading("No Data Available");
	}
}

function setSelectedDate(year, month, day)
{
	if(!day)
		day = 1;
	return new Date(year+'/'+month+'/'+day);
}

function setDateSelector()
{
	if($('#CaseDataTable').length)
	{
		if(!$('#CaseDataModal .span4:eq(1)').html())
		{
			$('#CaseDataModal .span4:eq(1)').html('<div class="btn-group" style="text-align:center;"><a class="btn" href="#" id="monthSelector" ><i class="icon-calendar"></i>'+Months[selectedDate.getMonth()]+', '+selectedDate.getFullYear()+'</a><a class="btn" href="#" id="iconRefresh" title="Reload"><i class="icon-repeat"></i></a></div>');
		}
    		
	}
	else if($('#SFCaseDataTable').length)
	{
		if(!$('#SFCaseData .span4:eq(1)').html())
    		$('#SFCaseData .span4:eq(1)').html('<div class="btn-group" style="text-align:center;"><a class="btn" href="#" id="monthSelector" ><i class="icon-calendar"></i>'+Months[selectedDate.getMonth()]+', '+selectedDate.getFullYear()+'</a><a class="btn" href="#" id="iconViewOpenCase" title="Open Case"><i class="icon-eye-open"></i></a><a class="btn" href="#" id="iconSFSync" title="Sync"><i class="icon-refresh"></i></a><a class="btn" href="#" id="iconSFRefresh" title="Reload"><i class="icon-repeat"></i></a></div>');
    
	}
	$('#monthSelector').html('<i class="icon-calendar"></i>'+Months[selectedDate.getMonth()]+', '+selectedDate.getFullYear());
    $('#monthlyReportYearPicker span').html(selectedDate.getFullYear());
}

function setCaseFilter(data)
{
	setChildSelectValue('#selectSystemUnit', data['SystemUnit']);

	setChildSelectValue('#selectFunctionUnit', data['FunctionUnit'], $('#selectSystemUnit').val());
	setChildSelectValue('#selectSubFunctionUnit', data['SubFunctionUnit'], $('#selectFunctionUnit').val());

	setChildSelectValue('#selectCaseType', data['CaseType']);
	setChildSelectValue('#selectSubCaseType', data['SubCaseType'], $('#selectCaseType').val());
}

function setChildSelectValue(selector, mapper, parentValue, childValue)
{
	if(selector.indexOf('#')==0)
	{
		$(selector+'_chzn').remove();
		$(selector).val('').change().removeClass('chzn-done').chosen();
	}
	$(selector).empty();
	if(parentValue==null)
	{
		$.each(mapper, function (key, value) {
			$(selector).append($('<option>', { 
		        value: value['k'],
		        text : value['v']
			}));
		});
	}
	else
	{
		$.each(mapper, function (key, value) {
			if(value['pk']==parentValue)
			{
				$(selector).append($('<option>', { 
			        value: value['k'],
		        	text : value['v']
				}));
			}
		});
	}
	if($(selector+' option').length>0)
	{
		if(childValue!=null)
			$(selector).val(childValue);
		else
			$(selector+' option')[0].selected = true;
	}
	$(selector).trigger("liszt:updated");
}

function setFunctionUnitFilter(subFunctionUnitId, globalMapper)
{
	var functionUnitId, systemUnitId;
	if(subFunctionUnitId!=null&&subFunctionUnitId>0)
	{
		functionUnitId = $.grep(globalMapper['SubFunctionUnit'], function(value){
			return value.k==subFunctionUnitId;
		})[0].pk;
		systemUnitId = $.grep(globalMapper['FunctionUnit'], function(value){
			return value.k==functionUnitId;
		})[0].pk;
	}

	setChildSelectValue('#selectSystemUnit', globalMapper['SystemUnit'], null, systemUnitId);
	setChildSelectValue('#selectFunctionUnit', globalMapper['FunctionUnit'], $('#selectSystemUnit').val(), functionUnitId);
	setChildSelectValue('#selectSubFunctionUnit', globalMapper['SubFunctionUnit'], $('#selectFunctionUnit').val(), subFunctionUnitId);
}

function setCaseTypeFilter(subCaseTypeId, globalMapper)
{
	var caseTypeId;
	if(subCaseTypeId!=null&&subCaseTypeId>0)
	{
		caseTypeId = $.grep(globalMapper['SubCaseType'], function(value){
			return value.k==subCaseTypeId;
		})[0].pk;
	}

	setChildSelectValue('#selectCaseType', globalMapper['CaseType'], null, caseTypeId);
	setChildSelectValue('#selectSubCaseType', globalMapper['SubCaseType'], $('#selectCaseType').val(), subCaseTypeId);
}

function refreshDataTable(table)
{
	startRefreshTable();
	callForCaseOverview(selectedDate.getFullYear(), (selectedDate.getMonth()+1), table);
}

function refreshSFDataTable(table, startFromSync)
{
	if($(table).length)
	{
		if(startFromSync)
		{
			n.setText('Reloading Data...');
		}
		else
		{
			startRefreshTable();
		}
		callForCaseListOverview(selectedDate.getFullYear(), (selectedDate.getMonth()+1), table, startFromSync);
	}
}

function changeToRefreshing(iconSelector)
{
	/*$(datePickerSelector).attr({
		'disabled': 'disabled'
	});*/

	$(iconSelector).attr({
		'disabled': 'disabled'
	});

	$(iconSelector+' i').attr({
		'class': 'icon-refreshing'
	});
}

function setClassTo(iconSelector, iconClass)
{
	//$(datePickerSelector).removeAttr('disabled');

	$(iconSelector).removeAttr('disabled');

	$(iconSelector+' i').attr({
		'class': iconClass
	});
}

function setInfoList(selector, dataList, headerContent, parentId, dataType)
{
	var total = 0;
	if(typeof dataType!= 'undefined')
		dataType = dataType.replace(' ', '');
	else
		dataType = '';

	$(selector).html('');

	if(headerContent==null)
		headerContent='Detail';
	$(selector).parents('.modal').find('.modal-header h3').html(headerContent);

	if(dataType.toLowerCase()=="casetype")
	{
		var list = $(selector).append('<div class="span12 list"></div>').find('.list');
		list.append('<div class="floatBtn"><a href="#"><i class="icon-zoom-in"></i>View Case List</a></div>')
	}
	else
	{
		var list = $(selector).append('<div class="span4 list"></div><div class="span8 list-detail arrow_box"><div class="chart"></div><div class="table"></div></div>').find('.list');
	}
	var body = list.append('<div class="body"></div>').find('.body');

	$.each(dataList, function(key, value){
		total+=value[1];
		name = '<span class="name">'+value[0]+'</span>';
		amount = '<span class="value">'+value[1]+'</span>';
		id = value[2];
		from = value[3];
		to = value[4];

		if(dataType.toLowerCase()=="casetype")
			body.append('<a href="#" data-link = "/cases/lists#date='+selectedDate.getFullYear()+'-'+(selectedDate.getMonth()+1)+'&ctd='+encodeURIComponent(value[0]).replace(/%/g,"!")+'" class="item '+dataType+'" data-from="'+from+'" data-to="'+to+'" data-id="'+id+'">'+name+amount+'</a>');
		else
			body.append('<a href="#" class="item '+dataType+'" data-from="'+from+'" data-to="'+to+'" data-id="'+id+'">'+name+amount+'</a>');
	});

	name = '<span class="name">Overall</span>';
	amount = '<span class="value">'+total+'</span>';
	list.append('<div class="footer"><a href="#" class="item '+dataType+'" data-from="'+from+'" data-to="'+to+'" data-id="-1" data-parent-id="'+parentId+'">'+name+amount+'</a></div>');
}

function drawCaseSourceChartInModal(selector, dataList, headerContent){
	$(selector).html('');

	if(headerContent==null)
		headerContent='Detail';
	$(selector).parents('.modal').find('.modal-header h3').html(headerContent);

	var body = $(selector).append('<div class="span12" id="caseSourceChart"></div>');

	requestResults = [];
	appenders = [];
	names = [];
	chartOptions = [];
	$.each(dataList, function(key, value){
		requestResults.push(value);
		appenders.push('caseSourceChart');
		if(key.toLowerCase()=='systemunitdata'){
			names.push('System Unit Chart');
		}
		else if(key.toLowerCase()=='functionunitdata'){
			names.push('Function Unit Chart');
		}
		chartOptions.push(null);
	})
	drawDetailCharts(requestResults, appenders, names, undefined, chartOptions);
}

function startRefreshTable(){
	changeToRefreshing('#iconSFSync');
	changeToRefreshing('#iconSFRefresh');
	changeToRefreshing('#iconRefresh');
	changeToRefreshing('#iconViewOpenCase');
	changeToRefreshing('#applyAll');
	changeToRefreshing('#cancelAll');
}

function finishRefreshTable(){
	setClassTo('#iconSFSync', 'icon-refresh');
	setClassTo('#iconSFRefresh', 'icon-repeat');
	setClassTo('#iconRefresh', 'icon-repeat');
	setClassTo('#iconViewOpenCase', 'icon-eye-open');
	setClassTo('#applyAll', 'icon-ok');
	setClassTo('#cancelAll', 'icon-remove');
}

function finishCaseAdd()
{
	$.noty.closeAll();
	$('#chartReminder').noty({
		layout: 'inline',
		type: 'information',
		template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div><button type="button" class="close" data-dismiss="alert" style="margin-left:5px;">×</button></div>',
		timeout: false,
		text: "You've made changes, please <a id='alertRefresh' href='#' onClick='location.reload();return false;'>Refresh</a> to view the latest report."
	});
}

function showModal(element, needLoadingInfo)
{
	if(needLoadingInfo)
	{
		loadingModal(element);
	}
	element.modal('show');
}

function centerModal(element)
{
	element.css({
		'margin-top':($(window).height()-element.height())/2, 
		'margin-left':($(window).width()-element.width())/2,
		'top':'0', 
		'left':'0'
	});
}

function loadingModal(element)
{
	element.find('.modal-body').html('<div id="loading" class="center"><div class="center"></div></div>');
}

var newData;
function adjustChartData(data)
{
	newData= [];
	var dayInWeek;
	$.each(data, function(index, value){
		$date = moment(value[0]);
		if(dayoff[$date.format('YYYYMM')]&&dayoff[$date.format('YYYYMM')][$date.format('DD')])
		{
			var nData={
				x: value[0],
				y: value[1],
	        	color: 'rgb(47,126,116)',
				marker: {
					enabled: true,
					symbol: 'circle',
					radius: 2,
					fillColor: "rgb(47,126,116)"
				}
			}
			newData.push(nData);
		}
		else
			newData.push(value);

	});
}

var loadingState = 0;
function loadingNoty(text, normalNoty, notyType)
{
	var state, modal=true, timeout=false,notyText = text?text:"Loading...",type=notyType?notyType:"information";

	if(normalNoty)
	{
		state = 1;
		modal = false;
		timeout = 2000;
	}
	else
	{
		state = loadingState;
		modal = true;
		timeout = false;
	}

	$.noty.closeAll();
	noty({
		layout: 'center',
		type: type, 
		animation: {
		    open: {height: 'toggle'},
		    close: {height: 'toggle'},
		    easing: 'swing',
		    speed: 150
		},
		modal: modal,
		timeout: timeout,
		text: notyText,
		callback: {
		    onClose: function() {
		    	if(state<=0)
		    		return false;
		    }
		}
	});
}

function closeLoadingNoty(state)
{
	loadingState = 1;
	$.noty.closeAll();
}

function createNewCaseFilter(newData)
{
	addedObj = {};
	currentSelect = this.container.prevAll('select').eq(0);
	parentSelect = this.container.prevAll('select').eq(1);

	if(parentSelect.length>0)
	{
		if(parentSelect.attr('id').indexOf('selectFunctionUnit')==0)
			addedObj['DataType'] = 'data-subfunctionunit';
		else if(parentSelect.attr('id').indexOf('selectSystemUnit')==0)
			addedObj['DataType'] = 'data-functionunit';
		else if(parentSelect.attr('id').indexOf('selectCaseType')==0)
			addedObj['DataType'] = 'data-subcasetype';
		addedObj['Index'] = 'p|'+parentSelect.val();
	}
	else
	{
		if(currentSelect.attr('id').indexOf('selectSystemUnit')==0)
			addedObj['DataType'] = 'data-systemunit';
		else if(currentSelect.attr('id').indexOf('selectCaseType')==0)
			addedObj['DataType'] = 'data-casetype';
		addedObj['Index'] = 'p|';
	}
	addedObj['Value'] = newData;
	//callForAddConfig($(this), JSON.stringify(new Array(addedObj)));
	$.ajax({
		type: "POST",
		url: "/configure/updateCaseFilter/true",
		data: JSON.stringify(new Array(addedObj)),
		dataType: "json",
		contentType: "application/json",
		beforeSend: function(jqXHR, data){
		},
		success: function(data){
			currentSelect.append('<option value = "'+data+'" selected="selected">'+newData+'</option>');
			currentSelect.trigger('liszt:updated');
		},
		error: function(requestObject, error, errorThrown){
			alert('Error occurred: '+error);
		},
		complete: function(){
		}
	});
}

function loadCaseInfoByRow(row)
{
	$('#caseDatetime').val(row.find('.createDate').html());
	$('#responseDatetime').val(row.find('.responseDate').html());
	$('#caseSubject').val(row.find('.data').text());
	$('#caseDescription').html(row.find('.data').attr('data-content'));
	$('#caseAge').val(row.find('.caseAge').html());
	setFunctionUnitFilter(row.find('.edit').attr('data-functionunitdetailid'), caseFilterData);
	setCaseTypeFilter(row.find('.edit').attr('data-casetypedetailid'), caseFilterData);

	oCaseInfo['CaseId'] = row.find('.edit').attr('data-caseid');
	oCaseInfo['Subject'] = $('#caseSubject').val();
	oCaseInfo['Description'] = $('#caseDescription').html();
	oCaseInfo['CaseDate'] = $('#caseDatetime').val();
	oCaseInfo['ResponseDate'] = $('#responseDatetime').val();
	oCaseInfo['CaseAge'] = $('#caseAge').val();
	oCaseInfo['SubCaseType'] = row.find('.edit').attr('data-casetypedetailid');
	oCaseInfo['SubFunctionUnit'] = row.find('.edit').attr('data-functionunitdetailid');
	oCaseInfo['Amount'] = $('#caseAmount').val();
	oCaseInfo['Row'] = row;
}

function initializeSystemUnitTrends(){
	if(systemUnit!=null&&systemUnit!==undefined)
	{
		$systemUnitContainer = $('#SystemUnitContainer');
		if($systemUnitContainer!=null)
		{
			$systemUnitContainer.html('');
			$.each(systemUnit, function(index, value){
				$systemUnitContainer.append('<a href="#" class="btn btn-default systemUnitPicker" data-id="'+value['k']+'">'+value['v']+'</a>');
			})
			//$('.systemUnitPicker:eq(0)').click();
		}
	}
}

function checkCaseInfo()
{
	if($.trim($('#caseSubject').val())==''||$.trim($('#caseDescription').html())==''
		||$.trim($('#selectSubFunctionUnit').val())==''||$.trim($('#selectSubCaseType').val())==''
		||$.trim($('#caseDatetime').val())==''||$.trim($('#responseDatetime').val())==''
		||$.trim($('#caseAge').val())==''||$('#caseAge').val()<=0
		||$.trim($('#caseAmount').val())==''||$('#caseAmount').val()<=0)
	{
		alert('Please check your input.');
		return false;
	}

	return true;
}

function getCaseFilter(sync)
{
	if(sync||caseFilterData==null)
	{
		$.ajax({
			url:"/configure/getCaseFilter",
			success:function(data){
				caseFilterData = (JSON.parse(data));

				systemUnit = caseFilterData['SystemUnit'];
				functionUnit = caseFilterData['FunctionUnit'];
				subFunctionUnit = caseFilterData['SubFunctionUnit'];
				caseType = caseFilterData['CaseType'];
				subCaseType = caseFilterData['SubCaseType'];
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			}
		});
	}
	else
	{
		setCaseFilter(caseFilterData);
		centerModal($('#CaseModal'));
	}
}

function getAnchorValue(hash, filter)
{
	var subStr = hash.match(filter+"=[^&]*");
	if(subStr!=null)
	{
		return decodeURIComponent(subStr[0].split("=")[1].replace(/!/g, "%"));
	}
	return null;
}

function loadCaseByAnchor()
{
	var identifier = window.location.hash; //gets everything after the hashtag i.e. #home
	var filter;
	if ((filter = getAnchorValue(identifier, "fud"))!=null) {
		$('.dataTables_filter input').val("@@2="+filter).keyup();
	}
	else if ((filter = getAnchorValue(identifier, "ctd"))!=null) {
		$('.dataTables_filter input').val("@@3="+filter).keyup();
	}
	else
	{
		$('.dataTables_filter input').val('').keyup();
		//caseTable.fnFilterClear();
	}
}

function transferJsonToTimelineHtmlNode(jsonData)
{
	var eventWrapper = $('#emergencyCaseTimeline .timeline-html-wrap');
	$.each(jsonData, function(key, val){
		var start = moment(val.start);
		var end = moment(val.end||val.to);
		var diffUnit = 'Hour';
		var duration = end.diff(start);
		if(duration<3600000)
		{
			duration = end.diff(start, 'minutes');
			if(duration<=1)
				diffUnit = "Minute";
			else
				diffUnit = "Minutes";
		}
		else
		{
			duration = end.diff(start, 'hours');
			if(duration<=1)
				diffUnit = "Hour"
			else
				diffUnit = "Hours";
		}
		var e = $('<div class="timeline-event"></div>').appendTo(eventWrapper);
		e.append('<div class="timeline-date">'+moment(val.start).format('YYYY-MM-DD HH:mm')+'</div>');
		e.append('<div class="timeline-title">'+val.title+'</div>');
		e.append('<div class="timeline-priority">'+val.incidence+'</div>');
		e.append('<div class="timeline-content"><span class="description">Subject:</span> '+val.title+'<br><span class="description">Reason:</span> '+val.reason+'<br><span class="description">Duration:</span> '+duration+' '+diffUnit+'<br><span class="description">Incidence:</span> '+emergencyDetailNotes[val.incidence-1]+'</div>');
	});
}

function callForHoliday(){
	if(!dayoff)
	{
		$.ajax({
			url:'/configure/getHoliday/2013',
			dataType: 'json',
			success:function(data){
				holiday = [];
				dayoff = data;
				$.each(dayoff, function(yearMonth, date){
					$.each(date, function(d, v){
						if(v==2)
							holiday.push(yearMonth+d);
					});
				});
			}
		});
	}
}

function DrawDailyCaseChart(dailyCase, emergencyCase, appenderSelector){
	var emergencyPriorityColor = ['#FFF', '#C2E4FF', '#72A7E2', '#7846E2', '#4600DD'];
	var series = [];
	$.each(dailyCase, function(systemUnit, dailyAmount){
		series.push({
			type: 'line',
			name: systemUnit,
			data: dailyAmount,
			id: systemUnit+'Series'
		});
	});
	var emergencyData = [];
	$.each(emergencyCase, function(key, value){
		emergencyData.push({
			x: moment(value['start']).valueOf(),
			y: 10,
			title: ' ',
			fillColor: emergencyPriorityColor[value['incidence']-1],
			text: '<br><strong>Emergency</strong>: '+value['title']+'<br><strong>Reason</strong>: '+value['reason']+'<br><strong>Impact</strong>: '+emergencyDetailNotes[value['incidence']-1]
		});
	});
	$.each(holiday, function(key, date){
		date = moment(date.substr(0,4)+'-'+date.substr(4,2)+'-'+date.substr(6,2)).valueOf();
		if(date<new Date())
		emergencyData.push({
			x: date,
			title: ' ',
			fillColor: '#00ff00',
			text: '<br><strong>Holiday</strong>'
		});
	});
	emergencyData.push({
		x: moment('2013-09-4').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-08-14').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-07-24').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-07-3').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-06-13').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-05-22').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-05-2').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	series.push({
		type: 'flags',
		name: 'Emergency',
		data: emergencyData,
		onSeries: 'TotalSeries',
		shape: 'circlepin',
		stackDistance: 10,
		height: 12,
		width: 12
	});
	//adjustChartData(result);
	$(appenderSelector).highcharts('StockChart', {
		chart: {
		},
		xAxis: {
			type: 'datetime',
			labels: {
				format: '{value:%W/%Y}',
				align: 'left',
				rotation: 30
			},
			tickInterval: 24*36e5
		},
		yAxis: {
			min: 0,
			title: {
				text: '# case'
			}
		},
		tooltip: {
			shared: true
		},
		credits: {
			enabled: false
		},
		title : {
			text : 'EC_Support Case# Overview'
		},
		series : series
	});
}

function DrawWeeklyCaseChart(dailyCase, emergencyCase, appenderSelector){
	var emergencyPriorityColor = ['#FFF', '#C2E4FF', '#72A7E2', '#7846E2', '#4600DD'];
	var series = [];
	var weeklyAmount, firstDay, weekData;
	$.each(dailyCase, function(systemUnit, dailyAmount){
		weekData = {};
		firstDay = [];
		$.each(dailyAmount, function(k,v){
			var weekYear = moment(v[0]).isoWeekYear()+''+moment(v[0]).isoWeek();
			if(!(weekYear in weekData))
			{
				weekData[weekYear] = 0;
				firstDay.push(moment(v[0]).weekday(1).valueOf());
			}
			weekData[weekYear]+=v[1];
		});
		weeklyAmount = [];
		var i=0;
		$.each(weekData, function(weekYear, amount){
			weeklyAmount.push([firstDay[i], amount]);
			i++;
		});
		series.push({
			type: 'line',
			shadow: true,
			marker : {
				enabled : true,
				radius : 3
			},
			name: systemUnit,
			data: weeklyAmount,
			id: systemUnit+'Series'
		});
	});
	var emergencyData = [];
	$.each(emergencyCase, function(key, value){
		emergencyData.push({
			x: moment(value['start']).valueOf(),
			y: 10,
			title: ' ',
			fillColor: emergencyPriorityColor[value['incidence']-1],
			text: '<br><strong>Emergency</strong>: '+value['title']+'<br><strong>Reason</strong>: '+value['reason']+'<br><strong>Impact</strong>: '+emergencyDetailNotes[value['incidence']-1]
		});
	});
	$.each(holiday, function(key, date){
		date = moment(date.substr(0,4)+'-'+date.substr(4,2)+'-'+date.substr(6,2)).valueOf();
		if(date<new Date())
		emergencyData.push({
			x: date,
			title: ' ',
			fillColor: '#00ff00',
			text: '<br><strong>Holiday</strong>'
		});
	});
	emergencyData.push({
		x: moment('2013-09-4').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-08-14').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-07-24').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-07-3').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-06-13').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-05-22').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	emergencyData.push({
		x: moment('2013-05-2').valueOf(),
		title: ' ',
		fillColor: '#FFFF00',
		text: '<br><strong>Release Date</strong>'
	});
	series.push({
		type: 'flags',
		name: 'Emergency',
		data: emergencyData,
		onSeries: 'TotalSeries',
		shape: 'circlepin',
		stackDistance: 10,
		height: 12,
		width: 12
	});
	//adjustChartData(result);
	$(appenderSelector).highcharts('StockChart', {
		chart: {
		},
		rangeSelector: {
			selected: 1
		},
		xAxis: {
			type: 'datetime',
			labels: {
				format: '{value:%W/%Y}',
				align: 'right',
				rotation: -30
			},
			tickInterval: 7*24*36e5
		},
		yAxis: {
			min: 0,
			title: {
				text: '# case'
			}
		},
		credits: {
			enabled: false
		},
		tooltip: {
			shared: true
		},
		title : {
			text : 'EC_Support Case# Overview'
		},
		series : series
	});
}

function DrawTrendsChart(trendsChart, dataList, appenderSelector){
	var series = [];
	var weeklyAmount, index=0, visible = true;
	$.each(dataList, function(functionUnit, data){
		visible = (index++<7);
		series.push({
			type: 'line',
			shadow: true,
			marker : {
				enabled : true,
				radius : 3
			},
			name: functionUnit,
			data: data,
			id: functionUnit+'Series',
			visible: visible
		});
	});
	if(trendsChart==null)
	{
		trendsChart = $(appenderSelector).highcharts({
			chart: {
				height: 360
			},
			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: {month: '%b \'%Y'},
				minTickInterval: 30*24*36e5
			},
			yAxis: {
				min: 0,
				minTickInterval: 5,
				title: {
					text: '# case'
				}
			},
			credits: {
				enabled: false
			},
			tooltip: {
				shared: false
			},
			title : {
				text : 'function unit in the past months'
			},
			series : series
		}).highcharts();
	}
	trendsChart.hideLoading();

	while(trendsChart.series.length > 0)
    	trendsChart.series[0].remove(true);

    $.each(series, function(index, seriesResult){
		trendsChart.addSeries(seriesResult);
    });

    trendsChart.redraw();
	
	if(dataList.length==0)
	{
		trendsChart.hideLoading();
		trendsChart.showLoading("No Data Available");
	}

	return trendsChart;
}