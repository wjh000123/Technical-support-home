$(document).ready(function(){
	//highlight current / active link
	highlightMenu();
	
	//establish history variables
	var
		History = window.History, // Note: We are using a capital H instead of a lower h
		State = History.getState(),
		$log = $('#log'),
		notSupportHtml5 = false;

	//bind to State Change
	History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
		var State = History.getState(); // Note: We are using History.getState() instead of event.state
		$.ajax({
			url:State.url,
			success:function(msg){
				$('#content').html($(msg).find('#content').html());
				$('#loading').remove();
				$('#content').fadeIn();
				$('ul.nav li.active').removeClass('active');
				$('.subnav a.active').removeClass('active');
				highlightMenu();
				docReady();
				tsDocReady();
			}
		});
	});

	$(window).bind('hashchange',function(event){
		if(getAnchorValue(window.location.hash, 'open'))
			callForOpenCaseListOverview('#SFCaseDataTable');
		loadCaseByAnchor();
	});

	if($.browser.msie&&$.browser.version<10)
		notSupportHtml5 = true;
	
	//ajaxify menus
	$('body').on('click', 'a.ajax-link', function(e){
		if(notSupportHtml5||$(this).parent().hasClass('active')||$(this).hasClass('active')) {return;}
		e.preventDefault();
		if($('.btn-navbar').is(':visible'))
		{
			$('.btn-navbar').click();
		}
		$('#loading').remove();
		$('#content').html('<div id="loading"><div class="center"></div></div>');
		var $clink=$(this);
		if (typeof(History.pushState) != 'undefined') { 
			History.pushState(null, null, $clink.attr('href'));
		}
		//History.pushState(null, null, $clink.attr('href'));
		$('ul.nav li.active').removeClass('active');
		$('.subnav a.active').removeClass('active');
		$clink.parent('li').addClass('active');
		$clink.addClass('active');
	});

	//animating menus on hover
	$('ul.main-menu li:not(.nav-header)').hover(function(){
		$(this).animate({'margin-left':'+=5'},300);
	},
	function(){
		$(this).animate({'margin-left':'-=5'},300);
	});
	
	//other things to do on document ready, seperated for ajax calls
	docReady();
	tsDocReady();

	Highcharts.setOptions({
	    global: {
	        useUTC: false
	    }
	});

	var lastShownObj;
	$('body').on('mouseenter', '[rel="popover"],[data-rel="popover"]', function(e){
		if(lastShownObj!=null&&lastShownObj[0]!=$(this)[0])
		{
			clearTimeout(lastShownObj.data('timeout'));
			lastShownObj.popover('hide');
		}
		//forceClose = false;
		lastShownObj=$(this);
		lastShownObj.popover('show');
	});

	$('body').on('mouseleave', '[rel="popover"],[data-rel="popover"]', function(e){
		var timeout = setTimeout(function(){
			lastShownObj.popover('hide');
		}, 50);
		lastShownObj.data('timeout', timeout);
	});

	$('body').on('mouseenter', '.popover', function(e){
		clearTimeout(lastShownObj.data('timeout'));
	});

	$('body').on('mouseleave', '.popover', function(e){
		var timeout = setTimeout(function(){
			lastShownObj.popover('hide');
		}, 50);
		lastShownObj.data('timeout', timeout);
	});

	$('body').on('click', function(e){
		!(datepickerClicked||$(e.target).is('.popup')||(!$(e.target).is('.close-pop')&&$(e.target).parents('.popup').length)||$(e.target).parents('.fc-content').length||$(e.target).parents('.datepicker').length||$(e.target).parents('.datetimepicker').length)&&$('.popup').hide();
		datepickerClicked=false;
	});

	$('body').on('click', '#cancelAll', function(e){
		changedElement = [];
		fulTable.fnReloadAjax();
		ctlTable.fnReloadAjax();
		$('.span2.modify').html('');
	})

	$('body').on('click', '#applyAll', function(e){
		var json = transferToJson(changedElement);
		$.ajax({
			type: "POST",
			url: "/configure/updateCaseFilter",
			data: json,
			dataType: "json",
  			contentType: "application/json",
  			beforeSend: function(jqXHR, data){
  				startRefreshTable();
  			},
			success: function(data){
				fulTable.fnReloadAjax();
				ctlTable.fnReloadAjax();
    			$('.span2.modify').html('');
				//fulTable.fnClearTable();
			},
			error: function(requestObject, error, errorThrown){
				alert('Error occurred: '+error);
			},
			complete: function(){
				changedElement = [];
  				finishRefreshTable();
			}
		});
	})

	$('body').on('click', '#functionUnitTab th:eq(0) i', function(e){
		addedSystemUnit = [];
		popupAddShow('#addSystemUnitModal');
		setChildSelectValue('#selectSystemUnit_NSU', systemUnit);
	})

	$('body').on('click', '#functionUnitTab th:eq(1) i', function(e){
		addedFunctionUnit = [];
		popupAddShow('#addFunctionUnitModal');
		setChildSelectValue('#selectSystemUnit_NFU', systemUnit);
		setChildSelectValue('#selectFunctionUnit_NFU', functionUnit, $('#selectSystemUnit_NFU').val());
	})

	$('body').on('click', '#functionUnitTab th:eq(2) i', function(e){
		addedSubFunctionUnit = [];
		popupAddShow('#addSubFunctionUnitModal');
		setChildSelectValue('#selectSystemUnit_NSFU', systemUnit);
		setChildSelectValue('#selectFunctionUnit_NSFU', functionUnit, $('#selectSystemUnit_NSFU').val());
		setChildSelectValue('#selectSubFunctionUnit_NSFU', subFunctionUnit, $('#selectFunctionUnit_NSFU').val());
	})

	$('body').on('click', '#caseTypeTab th:eq(0) i', function(e){
		addedSubFunctionUnit = [];
		popupAddShow('#addCaseTypeModal');
		setChildSelectValue('#selectCaseType_NCT', caseType);
	})

	$('body').on('click', '#caseTypeTab th:eq(1) i', function(e){
		addedSubFunctionUnit = [];
		popupAddShow('#addSubCaseTypeModal');
		setChildSelectValue('#selectCaseType_NSCT', caseType);
		setChildSelectValue('#selectSubCaseType_NSCT', subCaseType, $('#selectCaseType_NSCT').val());
	})

	$('body').on('click', '#addSystemUnitModal button', function(e){
		if($('#inputSystemUnit').val()!='')
		{
			addedObj = {};
			addedObj['DataType'] = 'data-systemunit';
			addedObj['Index'] = 'p|';
			addedObj['Value'] = $('#inputSystemUnit').val();
			addedSystemUnit.push(addedObj);
			var historyRow = $(this).parents('tbody').find('tr:last');
			if(historyRow.find('td:first').text()!=addLogHeader)
			{
				$(this).parents('tbody').append('<tr><td style="text-align:right;vertical-align:middle;"><h4>'+addLogHeader+'</h4></td><td> + '+addedObj['Value']+'</td></tr>');
			}
			else
			{
				historyRow.find('td:last').html(historyRow.find('td:last').html()+'<br> + '+addedObj['Value']);
			}
			$('#inputSystemUnit').val('');
		}
	});

	$(".notification-dropdown").each(function (index, el) {
	    var $el = $(el);
	    var $dialog = $el.find(".pop-dialog");
	    var $trigger = $el.find(".trigger");
	    
	    $dialog.click(function (e) {
	        e.stopPropagation()
	    });
	    $dialog.find(".close-icon").click(function (e) {
			e.preventDefault();
			$dialog.removeClass("is-visible");
			$trigger.removeClass("active");
	    });
	    var timeout;
	    $trigger.mouseover(function (e) {
			e.preventDefault();
			e.stopPropagation();
	      	clearTimeout(timeout);

			$dialog.addClass("is-visible");
			$(this).addClass("active");
	    });
	    $trigger.mouseout(function (e) {
			e.preventDefault();
			e.stopPropagation();
	    	timeout = setTimeout(function(){
				$dialog.removeClass("is-visible");
				$trigger.removeClass("active");
	    	},
	    	50);
	    });
	    $dialog.mouseover(function (e) {
			e.preventDefault();
			e.stopPropagation();
	    	clearTimeout(timeout);
	    });

	    $dialog.mouseout(function (e) {
			e.preventDefault();
			e.stopPropagation();
	    	timeout = setTimeout(function(){
				$dialog.removeClass("is-visible");
				$trigger.removeClass("active");
	    	},
	    	50);
	    });
	});

	$('body').on('click', '#saveSUAdd', function(e){
		callForAddConfig(fulTable, JSON.stringify(addedSystemUnit), 'SystemUnit', true);
		
	});

	$('body').on('click', '#savenewSUAdd', function(e){
		callForAddConfig(fulTable, JSON.stringify(addedSystemUnit), 'SystemUnit', false);
	});

	$('body').on('click', '#addFunctionUnitModal button', function(e){
		if($('#inputFunctionUnit').val()!='')
		{
			addedObj = {};
			addedObj['DataType'] = 'data-functionunit';
			addedObj['Index'] = 'p|'+$('#selectSystemUnit_NFU').val();
			addedObj['Value'] = $('#inputFunctionUnit').val();
			addedFunctionUnit.push(addedObj);
			var historyRow = $(this).parents('tbody').find('tr:last');
			if(historyRow.find('td:first').text()!=addLogHeader)
			{
				$(this).parents('tbody').append('<tr><td style="text-align:right;vertical-align:middle;"><h4>'+addLogHeader+'</h4></td><td> + '+$('#selectSystemUnit_NFU option:selected').text()+' -> '+addedObj['Value']+'</td></tr>');
			}
			else
			{
				historyRow.find('td:last').html(historyRow.find('td:last').html()+'<br> + '+$('#selectSystemUnit_NFU option:selected').text()+' -> '+addedObj['Value']);
			}
			$('#inputFunctionUnit').val('');
		}
	});

	$('body').on('click', '#saveFUAdd', function(e){
		if(addedFunctionUnit.length>0)
			callForAddConfig(fulTable, JSON.stringify(addedFunctionUnit), 'FunctionUnit', true);
	});

	$('body').on('click', '#savenewFUAdd', function(e){
		if(addedFunctionUnit.length>0)
			callForAddConfig(fulTable, JSON.stringify(addedFunctionUnit), 'FunctionUnit', false);
	});

	$('body').on('click', '#addSubFunctionUnitModal button', function(e){
		if($('#inputSubFunctionUnit').val()!='')
		{
			addedObj = {};
			addedObj['DataType'] = 'data-subfunctionunit';
			addedObj['Index'] = 'p|'+$('#selectFunctionUnit_NSFU').val();
			addedObj['Value'] = $('#inputSubFunctionUnit').val();
			addedSubFunctionUnit.push(addedObj);
			var historyRow = $(this).parents('tbody').find('tr:last');
			if(historyRow.find('td:first').text()!=addLogHeader)
			{
				$(this).parents('tbody').append('<tr><td style="text-align:right;vertical-align:middle;"><h4>'+addLogHeader+'</h4></td><td> + '+$('#selectSystemUnit_NSFU option:selected').text()+' -> '+$('#selectFunctionUnit_NSFU option:selected').text()+' -> '+addedObj['Value']+'</td></tr>');
			}
			else
			{
				historyRow.find('td:last').html(historyRow.find('td:last').html()+'<br> + '+$('#selectSystemUnit_NSFU option:selected').text()+' -> '+$('#selectFunctionUnit_NSFU option:selected').text()+' -> '+addedObj['Value']);
			}
			$('#inputSubFunctionUnit').val('');
		}
	});

	$('body').on('click', '#saveSFUAdd', function(e){
		if(addedSubFunctionUnit.length>0)
			callForAddConfig(fulTable, JSON.stringify(addedSubFunctionUnit), 'SubFunctionUnit', true);
	});

	$('body').on('click', '#savenewSFUAdd', function(e){
		if(addedSubFunctionUnit.length>0)
			callForAddConfig(fulTable, JSON.stringify(addedSubFunctionUnit), 'SubFunctionUnit', false);
	});

	$('body').on('click', '#addCaseTypeModal button', function(e){
		if($('#inputCaseType').val()!='')
		{
			addedObj = {};
			addedObj['DataType'] = 'data-casetype';
			addedObj['Index'] = 'p|';
			addedObj['Value'] = $('#inputCaseType').val();
			addedCaseType.push(addedObj);
			var historyRow = $(this).parents('tbody').find('tr:last');
			if(historyRow.find('td:first').text()!=addLogHeader)
			{
				$(this).parents('tbody').append('<tr><td style="text-align:right;vertical-align:middle;"><h4>'+addLogHeader+'</h4></td><td> + '+addedObj['Value']+'</td></tr>');
			}
			else
			{
				historyRow.find('td:last').html(historyRow.find('td:last').html()+'<br> + '+addedObj['Value']);
			}
			$('#inputCaseType').val('');
		}
	});

	$('body').on('click', '#saveCTAdd', function(e){
		if(addedCaseType.length>0)
			callForAddConfig(ctlTable, JSON.stringify(addedCaseType), 'CaseType', true);
	});

	$('body').on('click', '#savenewCTAdd', function(e){
		if(addedCaseType.length>0)
			callForAddConfig(ctlTable, JSON.stringify(addedCaseType), 'CaseType', false);
	});

	$('body').on('click', '#addSubCaseTypeModal button', function(e){
		if($('#inputSubCaseType').val()!='')
		{
			addedObj = {};
			addedObj['DataType'] = 'data-subcasetype';
			addedObj['Index'] = 'p|'+$('#selectCaseType_NSCT').val();
			addedObj['Value'] = $('#inputSubCaseType').val();
			addedSubCaseType.push(addedObj);
			var historyRow = $(this).parents('tbody').find('tr:last');
			if(historyRow.find('td:first').text()!=addLogHeader)
			{
				$(this).parents('tbody').append('<tr><td style="text-align:right;vertical-align:middle;"><h4>'+addLogHeader+'</h4></td><td> + '+$('#selectCaseType_NSCT option:selected').text()+' -> '+addedObj['Value']+'</td></tr>');
			}
			else
			{
				historyRow.find('td:last').html(historyRow.find('td:last').html()+'<br> + '+$('#selectCaseType_NSCT option:selected').text()+' -> '+addedObj['Value']);
			}
			$('#inputCaseType').val('');
		}
	});

	$('body').on('click', '#saveSCTAdd', function(e){
		if(addedSubCaseType.length>0)
			callForAddConfig(ctlTable, JSON.stringify(addedSubCaseType), 'SubCaseType', true);
	});

	$('body').on('click', '#savenewSCTAdd', function(e){
		if(addedSubCaseType.length>0)
			callForAddConfig(ctlTable, JSON.stringify(addedSubCaseType), 'SubCaseType', false);
	});

	$('body').on('click', '.edit', function(e){
		e.preventDefault();
		showModal($('#CaseModal'));
		$('#saveCase').data('actionType','update');
		$('#CaseInfoTable tr:last').hide();
		$('#savenewCase').hide();

		loadCaseInfoByRow($(e.target).parents('tr:first'));
		
	});

	$('body').on('click', '#CaseList', function(e){
		e.preventDefault();
		callForCaseOverview(selectedDate.getFullYear(), selectedDate.getMonth()+1, '#CaseDataTable');
	});

	$('body').on('click', '#viewSFCaseChart', function(e){
		e.preventDefault();
		callForCaseAmountOverview();
	});

	$('body').on('click', '#CaseAdd', function(e){
		e.preventDefault();
		$('#saveCase').data('actionType','insert');
		showModal($('#CaseModal'));
		initialCaseFilter();
	});

	$('body').on('click', '#saveCase', function(e){
		e.preventDefault();
		if(!checkCaseInfo())
			return false;
		callForUpsertCase(oCaseInfo, true, $('#saveCase').data('actionType'));
	});

	$('body').on('click', '#savenewCase', function(e){
		e.preventDefault();
		if(!checkCaseInfo())
			return false;
		callForUpsertCase(oCaseInfo, false, $('#saveCase').data('actionType'));
	});

	$('body').on('change', '#selectSystemUnit', function(e){
		setChildSelectValue('#selectFunctionUnit', functionUnit, $('#selectSystemUnit').val());
		setChildSelectValue('#selectSubFunctionUnit', subFunctionUnit, $('#selectFunctionUnit').val());
	});

	$('body').on('change', '#selectFunctionUnit', function(e){
		setChildSelectValue('#selectSubFunctionUnit', subFunctionUnit, $('#selectFunctionUnit').val());
	});

	$('body').on('change', '#selectCaseType', function(e){
		setChildSelectValue('#selectSubCaseType', subCaseType, $('#selectCaseType').val());
	});

	$('body').on('change', '#selectSystemUnit_NFU', function(e){
		setChildSelectValue('#selectFunctionUnit_NFU', functionUnit, $('#selectSystemUnit_NFU').val());
	})

	$('body').on('change', '#selectSystemUnit_NSFU', function(e){
		setChildSelectValue('#selectFunctionUnit_NSFU', functionUnit, $('#selectSystemUnit_NSFU').val());
		setChildSelectValue('#selectSubFunctionUnit_NSFU', subFunctionUnit, $('#selectFunctionUnit_NSFU').val());
	})

	$('body').on('change', '#selectFunctionUnit_NSFU', function(e){
		setChildSelectValue('#selectSubFunctionUnit_NSFU', subFunctionUnit, $('#selectFunctionUnit_NSFU').val());
	})

	$('body').on('change', '#selectCaseType_NSCT', function(e){
		setChildSelectValue('#selectSubCaseType_NSCT', subCaseType, $('#selectCaseType_NSCT').val());
	})

	$('.modal').on('show', function () {
		centerModal($(this));
	})

	$('body').on('click', '#iconRefresh', function(e){
		e.preventDefault();
		if($(this).attr('disabled')=== undefined)
			refreshDataTable('#CaseDataTable');
	});

	$('body').on('click', '#iconSFRefresh', function(e){
		e.preventDefault();
		if($(this).attr('disabled')=== undefined)
		{
			window.location.hash = '';
			refreshSFDataTable('#SFCaseDataTable');
		}
	});

	$('body').on('click', '#iconViewOpenCase', function(e){
		e.preventDefault();
		if($(this).attr('disabled')=== undefined)
			callForOpenCaseListOverview('#SFCaseDataTable');
	});

	$('body').on('click', '#iconSFSync', function(e){
		e.preventDefault();
		if($(this).attr('disabled')=== undefined)
			callForSyncSFCase(selectedDate.getFullYear(), selectedDate.getMonth()+1);
	});

	$('body').on('click', '.refresh', function(e){
		e.preventDefault();
		datatable = $('.datatableSmall').dataTable();
		datatable.fnClearTable();
		$('.dataTables_empty').html('Loading case...');
		datatable.fnReloadAjax('/cases/viewTodayCaseList/sync');
	});

	$('body').on('mouseenter', 'span:not(:has([data-rel="popover"]))', function() {
		var t = $(this);
		var title = t.attr('title');
		if (!title){
			if (this.offsetWidth < this.scrollWidth)
				t.parent().attr('title', t.text());
			else if(this.offsetWidth>=t.parent().width())
				t.attr('title', t.text());
		}
		else {
			if (this.offsetWidth >= this.scrollWidth && title == t.text())
				t.parent().removeAttr('title');
			else if(this.offsetWidth>=t.parent().width() && title == t.text())
				t.removeAttr('title');
		}
	});

	$('body').on('click', '#CaseDetailModal .list .item.', function(){
		$('#CaseDetailModal .list .item.active').removeClass('active');
		$(this).addClass('active');
		var percentage = ($(this).offset().top-$(this).parents('.list').offset().top+$(this).height())/$(this).parents('.list').height();
		if(percentage<0.05)
			percentage = 0.05;
		else if(percentage>0.95)
			percentage = 0.95;
		$(this).parents('.modal-body').find('.arrow_box').addClass('dynamic').css('top', percentage.toFixed(2)*100+"%");
	});
	$('body').on('click', '#CaseDetailModal .list .item.subFunctionUnit', function(){
		callForCaseTypeBy($(this).data('id'), $(this).data('parent-id'), 3, {data: JSON.stringify({from: $(this).data('from'),to: $(this).data('to')})});
	});

	$('body').on('click', '#CaseDetailModal .list .item.functionUnit', function(){
		callForCaseTypeBy($(this).data('id'), $(this).data('parent-id'), 2, {data: JSON.stringify({from: $(this).data('from'),to: $(this).data('to')})});
	});

	$('body').on('mouseover', '#CaseDetailModal .list .item.caseType', function(){
		var top = $(this).offset().top-$(this).parent().offset().top+8;
		var floatBtn = $('.floatBtn');
		floatBtn.css('top', top);
		floatBtn.find('a').attr('href', $(this).data('link')).attr('target', '_blank');
		//callForFunctionUnitBy($(this).data('id'), $(this).data('parent-id'), 2);
	});

	$('body').on('mouseover', '.modal-backdrop', function(){
		$('.floatBtn').css('top', '-100px');
		//callForFunctionUnitBy($(this).data('id'), $(this).data('parent-id'), 2);
	});

	$('body').on('click', '#switcher', function(e){
		if($(this).data('emergency'))
		{
			$('#DailyCase').animate({'margin-left':'0px'}, 200, 'easeOutCirc');
			$(this).find('.toggle-wrapper').animate({'margin-left':'-33px'}, 300, function(){
				$(this).parents('.box-header').find('.header-title').html(' Daily Case');
			});
			$(this).data('emergency', false);
		}
		else
		{
			var firstWrapperWidth = $('#DailyCase').width();
			$('#DailyCase').animate({'margin-left':'-'+(firstWrapperWidth+20)+'px'}, 200, 'easeOutCirc');
			$(this).find('.toggle-wrapper').animate({'margin-left':'-9px'}, 300, function(){
				$(this).parents('.box-header').find('.header-title').html(' Emergency Case');
			});
			$(this).data('emergency', true);
		}
	});

	$('body').on('mouseover', '#emergencyIncidence a', function(){
		renderRating($(this).parent(), $(this).data('value'));
	});

	$('body').on('mouseout', '#emergencyIncidence a', function(){
		renderRating($(this).parent(), $(this).parent().data('value'), false, true);
	});

	$('body').on('click', '#emergencyIncidence a', function(){
		renderRating($(this).parent(), $(this).data('value'), true);
	});

	$('body').on('click', '#emergencySave', function(e){
		var caseInfo = {};
		caseInfo.title = $.trim($('#emergencySubject').val());
		caseInfo.reason = $.trim($('#emergencyReason').val());
		if(!(caseInfo.title.length>0&&caseInfo.reason.length>0))
		{
			alert('Check the input.');
			return false;
		}
		caseInfo.incidence = $('#emergencyIncidence').data('value');
		caseInfo.startDate = $('#emergencyStartDate').val()+'T'+$('#emergencyStartTime').val()+":00+0800";
		caseInfo.endDate = $('#emergencyEndDate').val()+'T'+$('#emergencyEndTime').val()+":00+0800";
		if($(this).data('actionType')=='insert')
		{
			$.ajax({
				type: "post",
				url:"/cases/upsertEmergency",
				async: true,
				data: JSON.stringify(caseInfo),
				success:function(data){
					caseInfo.id = JSON.parse(data);

					emergencyCalendar.fullCalendar(
						'renderEvent',
						{
							title: caseInfo.title,
							reason: caseInfo.reason,
							start: caseInfo.startDate,
							end: caseInfo.endDate,
							to: caseInfo.endDate,
							incidence: caseInfo.incidence,
							id:caseInfo.id,
							allDay: false
						},
						true
					);
				},
				error: function(requestObject, error, errorThrown){
					alert('Error: Case is not fully created. Please re-create.');
				}
			});
		}
		else if($(this).data('actionType')=='update')
		{
			if(lastUpdatedEvent!==null)
			{

				if(lastUpdatedEvent.title != caseInfo.title)
				{
					lastUpdatedEvent.title = caseInfo.title;
				}
				else
					delete caseInfo.title;

				if(lastUpdatedEvent.reason != caseInfo.reason)
				{
					lastUpdatedEvent.reason = caseInfo.reason;
				}
				else
					delete caseInfo.reason;

				if(new Date(lastUpdatedEvent.start)*1 != new Date(caseInfo.startDate)*1)
				{
					lastUpdatedEvent.start = caseInfo.startDate;
				}
				else
					delete caseInfo.startDate;

				if((lastUpdatedEvent.end&&new Date(lastUpdatedEvent.end)*1 != new Date(caseInfo.endDate)*1)||(lastUpdatedEvent.to&&new Date(lastUpdatedEvent.to)*1 != new Date(caseInfo.endDate)*1))
				{
					lastUpdatedEvent.end = caseInfo.endDate;
					lastUpdatedEvent.to = caseInfo.endDate;
				}
				else
					delete caseInfo.endDate;

				if(lastUpdatedEvent.incidence != caseInfo.incidence)
				{
					lastUpdatedEvent.incidence = caseInfo.incidence;
				}
				else
					delete caseInfo.incidence;

				var hasUpdatedField = false;
				$.each(caseInfo, function(key, val){
					if(val!=null)
					{
						hasUpdatedField = true;
						return false;
					}
				})

				if(hasUpdatedField)
				{
					caseInfo.id = lastUpdatedEvent.id;
					
					$.ajax({
						type: "post",
						url:"/cases/upsertEmergency/update",
						data: JSON.stringify(caseInfo),
						success:function(data){
							emergencyCalendar.fullCalendar( 'updateEvent', lastUpdatedEvent );
						},
						error: function(requestObject, error, errorThrown){
							alert('Error: Case is not fully created. Please re-create.');
						}
					});

				}
			}
		}
		$(".popup").hide();
	});

	
	//prevent # links from moving to top
	$('body').on('click', 'a[href="#"][data-top!=true]', function(e){
		e.preventDefault();
	});

	$('body').on('click', '.top-list-case-type .range-criteria .submit', function(e){
		e.stopPropagation();
		e.preventDefault();
		var selectedCaseTypeId = -1;
		if($('#CriteriaOfCaseType').length)
			selectedCaseTypeId = $('#CriteriaOfCaseType').val();
		$.ajax({
			type: 'POST',
			url: '/cases/viewTopCaseType/'+selectedCaseTypeId,
			dataType: 'json',
			data: JSON.stringify({
				from: toplistRangeStart,
				to: toplistRangeEnd
			}),
			success: function(response){
				var categories = [];
				var data = {};
				data.chartData = [];
				data.extraData = {};
				/*var dtable = $('.top-list .list-content').dataTable();
				dtable.fnClearTable();
				dtable.fnAddData(response);
				dtable.fnSortNeutral();*/
				$.each(response, function(key, value){
					categories.push(value['CaseTypeName']+' - '+value['CaseTypeDetailName']);
					data.chartData.push({
						y:value['Amount'],
						ctdid:value['CaseTypeDetailId'],
						ctid:value['CaseTypeId']
					});
				});
				data.extraData.from = toplistRangeStart;
				data.extraData.to = toplistRangeEnd;
				drawTopListChart(topListChart, 'top-list-content', categories, data);
			}
		});
	});

	$('body').on('click', '.overview-list-system-unit .range-criteria .submit', function(e){
		e.stopPropagation();
		e.preventDefault();
		callForCaseSystemUnitByTimeRange(overviewlistRangeStart, overviewlistRangeEnd);
	});

	$('body').on('click', '.overview-case-handling-info .range-criteria .submit', function(e){
		e.stopPropagation();
		e.preventDefault();
		callForCaseHandlingInfo(caseHandlingOverviewRangeStart, caseHandlingOverviewRangeEnd);
	});

	$('body').on('click', '.systemUnitPicker', function(e){
		e.stopPropagation();
		e.preventDefault();
		$(this).parent().children().removeClass('active');
		$(this).addClass('active');
		callForMonthlyFunctionUnitAmount($(this).data('id'));
	});

	$('body').on('click', '#caseTypeTrendsLoad', function(e){
		callForMonthlyCaseTypeAmount();
	})

});	

function docReady(){
	
	//bootstrap-datepicker
	$('.datepicker').val(moment().format('YYYY-MM-DD'));
	$('.datepicker').datepicker({
		weekStart: 1,
		todayBtn: "linked",
    	todayHighlight: true,
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on("hide", function(e){
		datepickerClicked = true;
    });

	var yearpicker = $('.yearPicker').datepicker({
		startDate: "2013-01-01",
		format: 'yyyy-mm-dd',
		autoclose: true,
		minViewMode: 'years'
	}).on('changeDate', function(ev){
		var date = new Date(ev.date.valueOf());
		selectedDate = setSelectedDate(date.getFullYear(), date.getMonth()+1, 1);
		setDateSelector();
		yearpicker.hide();
		callForCaseReportByYear();
	}).data('datepicker');

	$durationStartDate = $('.durationStartDate');
	$durationEndDate = $('.durationEndDate');
	if($durationStartDate.length)
	{
		var sDate = $durationStartDate.datepicker({
			weekStart: 1,
			todayBtn: "linked",
	    	todayHighlight: true,
			format: 'yyyy-mm-dd',
			autoclose: true
		}).on("show", function(e){
			eDate.setStartDate(e.date);
	    }).on("hide", function(e){
			datepickerClicked = true;
	    }).on("changeDate", function(e){
			datepickerClicked = true;
			eDate.setStartDate(e.date);
			if(e.date.valueOf()>new Date($('.durationEndDate').val()).valueOf())
			{
				eDate.setValue(e.date.valueOf());
				$('.durationEndDate').val(moment(e.date).format('YYYY-MM-DD'));
			}
	    }).data('datepicker');
	    var valAttr = $durationStartDate.attr('value');
		if(typeof valAttr !== 'undefined' && valAttr !==false)
			$durationStartDate.val(moment().format('YYYY-MM-DD'));
		else
			$durationStartDate.html(moment().format('YYYY-MM-DD'));
	}
	$('.input-daterange').datepicker({
		weekStart: 1,
		todayBtn: "linked",
		todayHighlight: true,
		format: 'yyyy-mm-dd',
		autoclose: true
	});
	if($durationEndDate.length)
	{
		var eDate = $durationEndDate.datepicker({
			weekStart: 1,
			todayBtn: "linked",
			todayHighlight: true,
			format: 'yyyy-mm-dd',
			autoclose: true
		}).on("show", function(e){
			eDate.setStartDate(sDate.date<e.date?sDate.date:e.date);
	    }).on("hide", function(e){
			datepickerClicked = true;
	    }).data('datepicker');
	    var valAttr = $durationEndDate.attr('value');
		if(typeof valAttr !== 'undefined' && valAttr !==false)
			$durationEndDate.val(moment().format('YYYY-MM-DD'));
		else
			$durationEndDate.html(moment().format('YYYY-MM-DD'));
	}

    if($('.datetimepicker').length)
    {
		$('.datetimepicker').val(moment().format('YYYY-MM-DD HH:mm:ss'));
		var timepicker = $('.datetimepicker').datetimepicker({
			weekStart: 1,
			todayBtn: "linked",
	    	todayHighlight: true,
			format: 'yyyy-mm-dd hh:ii:ss',
			//minView: 'day',
			autoclose: true
		}).on("hide", function(e){
			datepickerClicked = true;
	    }).on("show", function(e){
	    	$('input.datetimepicker').not(e.target).datetimepicker('hide');
	    });
    }

	//notifications
	$('.noty').click(function(e){
		e.preventDefault();
		var options = $.parseJSON($(this).attr('data-noty-options'));
		noty(options);
	});

	//chosen - improves select
	$('[data-rel="chosen"],[rel="chosen"]').chosen({
		disable_search_threshold: 8
	}).each(function(){
    	if($(this).parents('tbody').find('.chzn-with-drop').length>0)
    	{
        	$(this).parents(".modal").css("overflow", "visible");
    	}
    	else
    	{
        	$(this).parents(".modal").css("overflow", "hidden");
    	}

		$(this).on("liszt:showing_dropdown", function () {
	    	if($(this).parents('tbody').find('.chzn-with-drop').length>0)
	    	{
	        	$(this).parents(".modal").css("overflow", "visible");
	    	}
	    });
	    $(this).on("liszt:hiding_dropdown", function () {
	    	if($(this).parents('tbody').find('.chzn-with-drop').length==0)
	    	{
	        	$(this).parents(".modal").css("overflow", "hidden");
	    	}
	    });
	});

	//tabs
	$('#myTab a:first').tab('show');
	$('#myTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

	//tabs
	$('#configTab a:first').tab('show');
	$('#configTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

	//makes elements soratble, elements that sort need to have id attribute to save the result
	$('.sortable').sortable({
		revert:true,
		cancel:'.btn,.box-content,.nav-header',
		update:function(event,ui){
			//line below gives the ids of elements, you can make ajax call here to save it to the database
			//console.log($(this).sortable('toArray'));
		}
	});

	//slider
	$('.slider').slider({
		min: 1,
		max: 25,
		range: "min",
		animate: "fast",
		slide: function(event, ui){
        	$("#caseAmount").val(ui.value);
      	}
	});

	$("#caseAmount").val($('.slider').slider("value"));

	$('.selectpicker').selectpicker();

	//tooltip
	$('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

	//auto grow textarea
	$('textarea.autosize').autosize();

	//$("abbr.timeago").timeago();

	//datatable
	$('.datatable').dataTable({
		"sDom": "<'row-fluid'<'span4'l><'span4'><'span4'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
		"sPaginationType": "bootstrap",
		"iDisplayLength": 20,
		"aaSorting": [],
		"aLengthMenu": [[10, 20, 40, 80, -1], [10, 20, 40, 80, "All"]],
		"oLanguage": {
			"sProcessing": "Processing...",
			"sLoadingRecords": "Loading...",
			"sLengthMenu": "_MENU_ records per page"
		},
		"fnDrawCallback" : function() {
		    $('[rel="popover"],[data-rel="popover"]').popover();
		    $(".createDate").fromNow();
			//$("abbr.timeago").timeago();
		}
	} );

	$('.btn-close').click(function(e){
		e.preventDefault();
		$(this).parent().parent().parent().fadeOut();
	});

	$('.btn-minimize').click(function(e){
		e.preventDefault();
		var $target = $(this).parent().parent().next('.box-content');
		if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
		else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
		$target.slideToggle();
	});

	$('.btn-setting').click(function(e){
		e.preventDefault();
		$('#myModal').modal('show');
	});

	//initialize the external events for calender

	$('#external-events div.external-event').each(function() {

		// it doesn't need to have a start or end
		var eventObject = {
			title: $.trim($(this).text()) // use the element's text as the event title
		};
		
		// store the Event Object in the DOM element so we can get to it later
		$(this).data('eventObject', eventObject);
		
		// make the event draggable using jQuery UI
		$(this).draggable({
			zIndex: 999,
			revert: true,      // will cause the event to go back to its
			revertDuration: 0  //  original position after the drag
		});
		
	});

	if($('#calendar').length)
	{
		emergencyCalendar = $('#calendar').fullCalendar({
			timeFormat: 'HH:mm',
			height: 750,
			firstDay: 1,
			unselectAuto: false,
			selectable: true,
			selectHelper: true,
			ignoreTimezone: false,
			select: function(start, end, allDay, jsEvent) {
				$('#emergencySave').data('actionType', 'insert').html('Create');
				$("#emergencyStartTime, #emergencyEndTime").timepicker({
					minuteStep: 5,
					showInputs: false,
					showMeridian: false
			    });
				$('#emergencyStartDate').val(moment(start).format("YYYY-MM-DD"));
				$('#emergencyStartTime').val(moment().format("HH:mm"));
				$('#emergencyEndDate').val(moment(end).format("YYYY-MM-DD"));
				$('#emergencyEndTime').val(moment().format("HH:mm"));
				$('#emergencySubject').val('');
				$('#emergencyReason').val('');
				renderRating($('#emergencyIncidence'), null, null, true);

				var popup = $('.calendar-wrapper .new-event.popup');
				var target = $(jsEvent.target);
				var popupwrapper = popup.parents(".calendar-wrapper");
				var x = target.offset().left - popup.width()/2;
				var y = target.offset().top - popup.height() - 30;
				showEmergencyPopup(x, y, target, popup, popupwrapper);
			},
			eventClick: function(calEvent, jsEvent, view) {
				$('#emergencySave').data('actionType', 'update').html('Update');
				lastUpdatedEvent = calEvent;
				$("#emergencyStartTime, #emergencyEndTime").timepicker({
					minuteStep: 5,
					showInputs: false,
					showMeridian: false
			    });
				var popup = $('.calendar-wrapper .new-event.popup');
				var target = $(jsEvent.target);
				var x = target.offset().left - popup.width()/2;
				var y = target.offset().top - popup.height() - target.height() - 20;

				$('.calendar-wrapper .fc-event.active').removeClass('active');
				target.parents('.fc-event').addClass('active');
				$('#emergencyStartDate').val(moment(calEvent.start).format("YYYY-MM-DD"));
				$('#emergencyStartTime').val(moment(calEvent.start).format("HH:mm"));
				$('#emergencyEndDate').val(moment(calEvent.end||calEvent.to||calEvent.start).format("YYYY-MM-DD"));
				$('#emergencyEndTime').val(moment(calEvent.end||calEvent.to||calEvent.start).format("HH:mm"));
				$('#emergencySubject').val(calEvent.title);
				$('#emergencyReason').val(calEvent.reason);
				$('#emergencyIncidence').data('value', calEvent.incidence);
				showEmergencyPopup(x, y, target, popup);
				renderRating($('#emergencyIncidence'), $('#emergencyIncidence').data('value'), null, true);
			},
			eventRender: function(event, element) {
				//element.attr({"reason": event.reason, "incidence": event.incidence});
			},
			viewRender: function(view, element){
				emergencyCalendar&&emergencyCalendar.fullCalendar('removeEvents');
				fetchEmergencyCase(view.start.getFullYear(), (view.start.getMonth()+1), function(rawdata){
					emergencyCalendar.fullCalendar('addEventSource', JSON.parse(rawdata));
				});
			}
		});
	}

	$('.reportRange').daterangepicker(
	{
		minDate: '2013-01-01',
		showDropdowns: true,
		showWeekNumbers: true,
		timePicker: false,
		//timePickerIncrement: 1,
		//timePicker12Hour: true,
		ranges: {
			'Last 30 Days': [moment().subtract('days', 29), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
			'Last 3 Months': [moment().subtract('month', 3).startOf('month'), moment().subtract('month', 1).endOf('month')],
			'YTD': [moment().startOf('year'), moment()]
		},
		opens: 'left',
		buttonClasses: ['btn btn-default'],
		applyClass: 'btn-small btn-primary',
		cancelClass: 'btn-small',
		format: 'YYYY-MM-DD',
		separator: ' to ',
		locale: {
			applyLabel: 'Submit',
			fromLabel: 'From',
			toLabel: 'To',
			customRangeLabel: 'Custom Range',
			daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
			monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			firstDay: 1
		}
	},
	function(start, end) {
		itemId = this.element.attr('id');
		if(itemId=='overViewListRange')
		{
			overviewlistRangeStart = start;
			overviewlistRangeEnd = end;
		}
		else if(itemId=='topListRange')
		{
			toplistRangeStart = start;
			toplistRangeEnd = end;
		}
		else if(itemId=='caseHandlingOverviewRange')
		{
			caseHandlingOverviewRangeStart = start;
			caseHandlingOverviewRangeEnd = end;
		}

		$(this.element).find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	}
	);
	//Set the initial state of the picker label
	$('#overViewListRange span').html(moment().startOf('month').format('MMMM D, YYYY') + ' - ' + moment().endOf('month').format('MMMM D, YYYY'));
	overviewlistRangeStart = moment().startOf('month');
	overviewlistRangeEnd = moment().endOf('month');

	$('#topListRange span').html(moment().startOf('month').format('MMMM D, YYYY') + ' - ' + moment().endOf('month').format('MMMM D, YYYY'));
	toplistRangeStart = moment().startOf('month');
	toplistRangeEnd = moment().endOf('month');

	$('#caseHandlingOverviewRange span').html(moment().startOf('year').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
	caseHandlingOverviewRangeStart = moment().startOf('year');
	caseHandlingOverviewRangeEnd = moment();

	
/*
	if($('#top-list-content').length)
		var topListChart = new Highcharts.Chart({
			chart: {
				type: 'bar',
				renderTo: 'top-list-content',
				animation: {
					duration: 500
				}
			},
			title: {
				text: 'Top 10 Case Type'
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
					color: '#1aadce'
				}
			},
			legend: {
				enabled: false
			}
		});*/
}

function highlightMenu(){
	$('.navbar ul.navbar-nav li a').each(function(){
		if(String(window.location).indexOf($($(this))[0].href)>=0)
		{
			$(this).parent().addClass('active');
			return false;
		}
	});

	$('.subnav a').each(function(){
		if(String(window.location)==$($(this))[0].href)
			$(this).addClass('active');
	});

	if(!$('.navbar ul.navbar-nav li.active').length)
	{
		$('.navbar ul.navbar-nav li:eq(0)').addClass('active');
	}

	if(!$('.subnav a.active').length)
	{
		$('.subnav a').each(function(){
			if($($(this))[0].href.indexOf(window.location.pathname)>=0)
			{
				$(this).addClass('active');
				return false;
			}
		});
	}
}

function renderRating(container, value, setValue, showAsSelected)
{
	value==null&&(value=1);
	setValue&&container.data('value', value)&&container.data('title', emergencyBriefNotes[value-1]);
	container.find('span.note').html(emergencyBriefNotes[value-1]);

	$.each(container.children('a'), function(i, val){
		var ratingOption = $(val);
		if(ratingOption.data('value')<=value)
		{
			ratingOption.removeClass();
			if(showAsSelected)
			{
				ratingOption.addClass('rating_selected');
			}
			else
			{
				ratingOption.addClass('rating_on');
			}
		}
		else
		{
			ratingOption.removeClass();
			ratingOption.addClass('rating_off');
		}
	});
}

function showEmergencyPopup(x, y, target, popup)
{
	x=x<0?0:x;
	popup.hide();
	if(y < 0)
	{
		$('.calendar-wrapper .popup .pointer').css('top', '-22px');
		$('.calendar-wrapper .popup .pointer .arrow').css({'border-top-color':'transparent', 'border-bottom-color':'#fff'});
		$('.calendar-wrapper .popup .pointer .arrow_border').css({'border-top-color':'transparent', 'border-bottom-color':'#888', 'top':'-2px'});
		y = target.offset().top + target.height();
	}
	else
	{
		$('.calendar-wrapper .popup .pointer').css('top', '');
		$('.calendar-wrapper .popup .pointer .arrow').css({'border-top-color':'', 'border-bottom-color':''});
		$('.calendar-wrapper .popup .pointer .arrow_border').css({'border-top-color':'', 'border-bottom-color':'', 'top':''});
	}
	popup.css({'top':y, 'left':x});
	popup.fadeIn("fast");
}

//additional functions for data table
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings ){
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
}

$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
					'<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
				// remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
				}

				// add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
});


//datatable plugins
$.fn.dataTableExt.oApi.fnSortNeutral = function ( oSettings ){
    /* Remove any current sorting */
    oSettings.aaSorting = [];
      
    /* Sort display arrays so we get them in numerical order */
    oSettings.aiDisplay.sort( function (x,y) {
        return x-y;
    } );
    oSettings.aiDisplayMaster.sort( function (x,y) {
        return x-y;
    } );
      
    /* Redraw */
    oSettings.oApi._fnReDraw( oSettings );
};

$.fn.dataTableExt.oApi.fnFindCellRowNodes = function ( oSettings, sSearch, iColumn )
{
    var
        i,iLen, j, jLen,
        aOut = [], aData;
      
    for ( i=0, iLen=oSettings.aoData.length ; i<iLen ; i++ )
    {
        aData = oSettings.aoData[i]._aData;
          
        if ( typeof iColumn == 'undefined' )
        {
            for ( j=0, jLen=aData.length ; j<jLen ; j++ )
            {
                if ( aData[j] == sSearch )
                {
                    aOut.push( oSettings.aoData[i].nTr );
                }
            }
        }
        else if ( aData[iColumn] == sSearch )
        {
            aOut.push( oSettings.aoData[i].nTr );
        }
    }
      
    return aOut;
};

$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw ){
    if ( sNewSource !== undefined && sNewSource !== null ) {
        oSettings.sAjaxSource = sNewSource;
    }
 
    // Server-side processing should just call fnDraw
    if ( oSettings.oFeatures.bServerSide ) {
        this.fnDraw();
        return;
    }
 
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;
    var aData = [];
 
    this.oApi._fnServerParams( oSettings, aData );
 
    oSettings.fnServerData.call( oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );
 
        /* Got the data - add it to the table */
        var aData =  (oSettings.sAjaxDataProp !== "") ?
            that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;
 
        for ( var i=0 ; i<aData.length ; i++ )
        {
            that.oApi._fnAddData( oSettings, aData[i] );
        }
         
        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
 
        that.fnDraw();
 
        if ( bStandingRedraw === true )
        {
            oSettings._iDisplayStart = iStart;
            that.oApi._fnCalculateEnd( oSettings );
            that.fnDraw( false );
        }
 
        that.oApi._fnProcessingDisplay( oSettings, false );
 
        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' && fnCallback !== null )
        {
            fnCallback( oSettings );
        }
    }, oSettings );
};

$.fn.dataTableExt.oApi.fnFilterClear  = function ( oSettings )
{
    /* Remove global filter */
    oSettings.oPreviousSearch.sSearch = "";
      
    /* Remove the text of the global filter in the input boxes */
    if ( typeof oSettings.aanFeatures.f != 'undefined' )
    {
        var n = oSettings.aanFeatures.f;
        for ( var i=0, iLen=n.length ; i<iLen ; i++ )
        {
            $('input', n[i]).val( '' );
        }
    }
      
    /* Remove the search text for the column filters - NOTE - if you have input boxes for these
     * filters, these will need to be reset
     */
    for ( var i=0, iLen=oSettings.aoPreSearchCols.length ; i<iLen ; i++ )
    {
        oSettings.aoPreSearchCols[i].sSearch = "";
    }
      
    /* Redraw */
    oSettings.oApi._fnReDraw( oSettings );
};

Highcharts.dateFormats = {
        W: function (timestamp) {
			return moment(timestamp).isoWeek();
        }
    }

$.fn.fromNow = function() {
	$.each($(this), function(key, val){
		$val = $(val);
		if(!$val.prev().length)
		{
			var fn = moment($val.html()).format('YYYY-MM-DD');
			if((moment().month() - moment($val.html()).month())<1)
				fn = moment($val.html()).fromNow();
			$('<span style="color:#999;margin-left:0.5em;">'+fn+'</span>').insertBefore($val);
			$val.parent().attr({title: $val.html()});
		}
	})
};