	var mycolumns = [[
				{field:'id',title:'Type ID',width:50,sortable:true},  
				{field:'name',title:'Name',width:150,sortable:true},  
				{field:'value',title:'Value',width:400,resizable:false}  
	]];
	
	var addcolumns = [[
				{field:'name',title:'Name',width:150,sortable:true},  
				{field:'value',title:'Value',width:400,resizable:false}  
	]];

	// append some nodes to the selected row
	function append(){
		var node = $('#tg').treegrid('getSelected');
		$('#tg').treegrid('append',{
			parent: node.id,  // the node has a 'id' value that defined through 'idField' property
			data: [{
				id: '073',
				name: 'name73'
			}]
		});
	}
	// insert a new node before the selected node
	function insert(){
		//var node = $('#tg').treegrid('select', 12);alert(node.data);
		var node = $('#tg').treegrid('getSelected');
		if (node){
			$('#tg').treegrid('insert', {
				before: node.id,
				data: {
					id: 100,
					name: 'New Testing for Insert'
				}
			});
		} else {
			$('#tg').treegrid('insert', {
				after: node.id,
				data: {
					id: 100,
					name: 'New Test 100'
				}
			});
		}
	}
	function formatProgress(value){
	if (value){
		var s = '<div style="width:100%;border:1px solid #ccc">' +
				'<div style="width:' + value + '%;background:#cc0000;color:#fff">' + value + '%' + '</div>'
				'</div>';
		return s;
	} else {
		return '';
	}
	}
	var editingId;
	function edit(){
		if (editingId != undefined){
			$('#tg').treegrid('select', editingId);
			return;
		}
		var row = $('#tg').treegrid('getSelected');
		if (row){
			editingId = row.id
			$('#tg').treegrid('beginEdit', editingId);
		}
	}
	function save(){
		if (editingId != undefined){
			var t = $('#tg');
			t.treegrid('endEdit', editingId);
			editingId = undefined;
			var persons = 0;
			var rows = t.treegrid('getChildren');
			for(var i=0; i<rows.length; i++){
				var p = parseInt(rows[i].persons);
				if (!isNaN(p)){
					persons += p;
				}
			}
			var frow = t.treegrid('getFooterRows')[0];
			frow.persons = persons;
			t.treegrid('reloadFooter');
		}
	}
	function cancel(){
		if (editingId != undefined){
			$('#tg').treegrid('cancelEdit', editingId);
			editingId = undefined;
		}
	}
	
	var url;  
	function add(target){alert('test');
		var tr = $(target).closest('tr.datagrid-row');
		var rowIndex = parseInt(tr.attr('datagrid-row-index'));alert(rowIndex);
		$('#tg').datagrid('selectRow', rowIndex);
		var row = $('#tg').datagrid('getSelected');
		alert('row');
		$('#fm').form('load', row);
		url	= '';
	} 
	
	function collapse()
	{
		$('#pg').propertygrid('collapseGroup');
	}
	
	
	
	function addConfig()
	{
		clearAll();
		
		expandCategory('group');
		//alert("ADD ITEM");
		//alert('add '+document.getElementById('action').value);
		document.getElementById('action').value = "add";
		/*//alert('add '+document.getElementById('action').value);
		$('#pg').propertygrid({						
			url: './configuration/getConfigAddPageDeatils',  
			showGroup: true,  
			columns: addcolumns,
			width:700,
			height:'auto'//500
		});
		*/
		$('#w').window('open');
		
		//collapseChildGroups();			
	}
	
	function deleteItem()
	{
		$('#tg').treegrid({
			
			onClickRow: function(row) {
				//alert("DELETE ITEM");
				
				var parentNode = $('#tg').treegrid('getParent' , row.id);
				//alert("parent :"+parentNode)
				if(parentNode != null)
				{
					var parentNodeId = parentNode.id;
				}
				//alert("row id :"+row.id);
				$.ajax({
					type: "GET",
					cache: false,
					dataType: "json",
					url: "./configuration/deleteConfigDetails?type_id="+row.id,
					data: {							
						},
					success: function(data) {
						//alert("Ajax success");
					},
					error: function(data){
						alert("Ajax Fail");
					}
				});
				alert('Items Deleted Successfully');
				$('#tg').treegrid('reload'); 
			},			
		})	

	}
	
	
	/*function editItem() {
		
		document.getElementById('action').value = "edit";
		
		$('#tg').treegrid({
			
			onClickRow: function(row) {
				//alert("EDIT ITEM");
				//alert('going 2 reload'+row);
				
				var parentNode = $('#tg').treegrid('getParent' , row.id);
				//alert("parent :"+parentNode)
				if(parentNode != null)
				{
					var parentNodeId = parentNode.id;
				}
				//alert("row id :"+row.id);
				$('#pg').propertygrid({
					
					url: './configuration/getConfigEditDeatils?type_id='+ row.id,  
					showGroup: true,  
					columns: mycolumns,
					width:700,
					height:'auto'//500
				});					
				
				$('#w').window('open');
				
				collapseChildGroups();
			},			
		})	

	}*/
	
	
	function editItem() {
		
		var init_type;
		var count;
		
		
		
				
		
		//alert("edit");
		$('#tg').treegrid({
			
			onClickRow: function(row) {
				clearAll();
		
				showCategory('group');
				showCategory('order');	
				showCategory('result');
				showCategory('reccomendation');	
				
				document.getElementById('action').value = "edit";
				alert("EDIT ITEM");
				//alert('going 2 reload'+row);
				
				var parentNode = $('#tg').treegrid('getParent' , row.id);
				//alert("parent :"+parentNode)
				if(parentNode != null)
				{
					var parentNodeId = parentNode.id;
				}
				//alert("row id :"+row.id);
				/*$('#pg').propertygrid({
					
					url: './configuration/getConfigEditDeatils?type_id='+ row.id,  
					showGroup: true,  
					columns: mycolumns,
					width:700,
					height:'auto'//500
				});					
				*/
				
				$.ajax({
					type: "POST",
					cache: false,
					dataType: "json",
					url: "./configuration/getConfigEditDeatils?type_id="+ row.id,
					data: {							
						},
					success: function(data) {
						//alert("Ajax success"+data.toSource());
						
						count	= 0;
						
						$.each(data, function(jsonIndex, jsonValue){
							//alert("type id :"+jsonValue['type_id']);
							//parent_id = jsonValue['type_id'];
							if(count == 0)
							{
								init_type = jsonValue['group'];
							}
							
							for(var field in jsonValue)
							{
								//alert(field+" : = "+jsonValue[field]);
								if(field == "group")
								{
									continue;
								}
								document.getElementById(field).value 		= jsonValue[field];								
							}							
							/*
							if(jsonValue['group'] == "grp")
							{
								document.getElementById('group_div').style.display		= 'block';
								document.getElementById('group_indicator_value').value 		= "-";
								document.getElementById('group_indicator').innerHTML 		= "-";
							}
							else if(jsonValue['group'] == "ord")
							{
								document.getElementById('order_div').style.display		= 'block';
								document.getElementById('order_indicator_value').value 		= "-";
								document.getElementById('order_indicator').innerHTML 		= "-";
							}
							else if(jsonValue['group'] == "res")
							{
								document.getElementById('result_div').style.display		= 'block';
								document.getElementById('result_indicator_value').value 	= "-";
								document.getElementById('result_indicator').innerHTML 		= "-";
							}
							else if(jsonValue['group'] == "rec")
							{
								document.getElementById('reccomendation_div').style.display	= 'block';
								document.getElementById('reccomendation_indicator_value').value = "-";
								document.getElementById('reccomendation_indicator').innerHTML 	= "-";
							}*/
							count++;
						});
						
						if(init_type == "grp")
						{
							hideCategory('order');
							hideCategory('result');
							hideCategory('reccomendation');
							expandCategory('group');
						}
						
						if(init_type == "ord")
						{
							hideCategory('group');
							expandCategory('order');
						}
						
						if(init_type == "res")
						{
							hideCategory('group');
							hideCategory('order');
							expandCategory('result');
						}
						
						if(init_type == "rec")
						{
							hideCategory('group');
							hideCategory('order');	
							hideCategory('result');
							expandCategory('reccomendation');
						}
					},
					error: function(data){
						alert("Ajax Fail");
					}
				});
				
				
				
				
				$('#w').window('open');
				
				//collapseChildGroups();
			},			
		})	
		
		

	}
	
	
	function collapseChildGroups(rowid)
	{
		$('#pg').propertygrid({
				onLoadSuccess: function(data)
				{	$('#pg').propertygrid('collapseGroup');
					$('#pg').propertygrid('expandGroup', 0); 
				}
			});
					
	}
	
	function collapseOtherGroups()
	{
		$('#pg').propertygrid({
				onExpand: function(row)
				{
					alert("hihihihi");
				}
			});
					
	}
	
	$('#pg').propertygrid({
		onExpand: function(row){
			alert('hai ****');
		}
	});
			
	function aaaa(rowid)
	{
		$('#pg').propertygrid({  
			onClickRow:function(rowIndex){  
			   alert("onClickRow");
			}  
		    });  
	}		    
	
	
	function loadRemote(){
		$('#ff').form('load', '../treegrid/treegrid_data2.json');
	}
	
	
	function saveItem()
	{
		if((document.getElementById('action').value == "add")||(document.getElementById('action').value == "addExist"))
		{
			
			addItem();
		}
		else
		{
			var input_arr	= new Array();
		
			var grp_arr	= new Array('group_name', 'group_description');
			
			var ord_arr	= new Array('order_name', 'order_description', 'order_sequence', 'order_from', 'order_procedurecode',
						    'order_standardcode', 'order_bodysite','order_specimentype', 'order_administervia', 'order_laterality');
			
			var res_arr	= new Array('result_name', 'result_description', 'result_sequence', 'result_defaultunits', 'result_defaultrange', 'result_followupservices');
			
			var rec_arr	= new Array('reccomendation_name', 'reccomendation_description', 'reccomendation_sequence',
						    'reccomendation_defaultunits', 'reccomendation_defaultrange', 'reccomendation_followupservices');
			
			
			var column_arr	= {'group_name': 'name','group_description':'description',
						'order_name': 'name','order_description':'description','order_sequence':'seq','order_from':'lab_id',
						'order_procedurecode':'procedure_code','order_standardcode':'standard_code','order_bodysite':'body_site',
						'order_specimentype':'specimen','order_administervia':'route_admin','order_laterality':'laterality',
						'result_name': 'name','result_description':'description','result_sequence':'seq',
						'reccomendation_name': 'name','reccomendation_description':'description','reccomendation_sequence':'seq',
						'result_defaultunits':'units','result_defaultrange':'range','result_followupservices':'related_code',
						'reccomendation_defaultunits':'units','reccomendation_defaultrange':'range','reccomendation_followupservices':'related_code'};
			
			var params;
		
			if($('#group_name').val().trim() != "")
			{
				params = "";
				//alert("index :"+index+" => "+input_arr[index]);
				params	= "?procedure_type=grp&type_id="+$('#group_type_id').val();
				
				for(var ind in grp_arr)
				{
					if($('#'+grp_arr[ind]).val().trim() != "")
					{
						params += "&"+column_arr[grp_arr[ind]]+"="+$('#'+grp_arr[ind]).val();
					}
				}
				//alert("submitting : group params : "+params);
				//return false;
				
				$.ajax({
					type: "GET",
					cache: false,
					dataType: "json",
					async: false,
					url: "./configuration/saveConfigDetails"+params,
					data: {							
						},
					success: function(data) {
						//alert("Ajax success"+data);
						//alert("myObject is " + data.toSource());
						$.each(data, function(jsonIndex, jsonValue){
							//alert("new type id :"+jsonValue['type_id']);
							//parent_id = jsonValue['type_id'];
						});
					},
					error: function(data){
						alert("Ajax Fail");
					}
				});
				
				
			}
			
			if($('#order_name').val().trim() != "")
			{
				params = "";
				//alert("index :"+index+" => "+input_arr[index]);
				params	= "?procedure_type=grp&type_id="+$('#order_type_id').val();
				
				for(var ind in ord_arr)
				{
					if($('#'+ord_arr[ind]).val().trim() != "")
					{
						params += "&"+column_arr[ord_arr[ind]]+"="+$('#'+ord_arr[ind]).val();
					}
				}
				//alert("submitting : order params : "+params);
				//return false;
				$.ajax({
					type: "GET",
					cache: false,
					dataType: "json",
					async: false,
					url: "./configuration/saveConfigDetails"+params,
					data: {							
						},
					success: function(data) {
						//alert("Ajax success"+data);
						//alert("myObject is " + data.toSource());
						$.each(data, function(jsonIndex, jsonValue){
							//alert("new type id :"+jsonValue['type_id']);
							//parent_id = jsonValue['type_id'];
						});
					},
					error: function(data){
						alert("Ajax Fail");
					}
				});
			}
			
			if($('#result_name').val().trim() != "")
			{
				params = "";
				//alert("index :"+index+" => "+input_arr[index]);
				params	= "?procedure_type=grp&type_id="+$('#result_type_id').val();
				
				for(var ind in res_arr)
				{
					if($('#'+res_arr[ind]).val().trim() != "")
					{
						params += "&"+column_arr[res_arr[ind]]+"="+$('#'+res_arr[ind]).val();
					}
				}
				//alert("submitting : result params : "+params);
				//return false;
				$.ajax({
					type: "GET",
					cache: false,
					dataType: "json",
					async: false,
					url: "./configuration/saveConfigDetails"+params,
					data: {							
						},
					success: function(data) {
						//alert("Ajax success"+data);
						//alert("myObject is " + data.toSource());
						$.each(data, function(jsonIndex, jsonValue){
							//alert("new type id :"+jsonValue['type_id']);
							//parent_id = jsonValue['type_id'];
						});
					},
					error: function(data){
						alert("Ajax Fail");
					}
				});
			}
			
			if($('#reccomendation_name').val().trim() != "")
			{
				params = "";
				//alert("index :"+index+" => "+input_arr[index]);
				params	= "?procedure_type=grp&type_id="+$('#reccomendation_type_id').val();
				
				for(var ind in rec_arr)
				{
					if($('#'+rec_arr[ind]).val().trim() != "")
					{
						params += "&"+column_arr[rec_arr[ind]]+"="+$('#'+rec_arr[ind]).val();
					}
				}
				//alert("submitting : reccomendation params : "+params);
				//return false;
				$.ajax({
					type: "GET",
					cache: false,
					dataType: "json",
					async: false,
					url: "./configuration/saveConfigDetails"+params,
					data: {							
						},
					success: function(data) {
						//alert("Ajax success"+data);
						//alert("myObject is " + data.toSource());
						$.each(data, function(jsonIndex, jsonValue){
							//alert("new type id :"+jsonValue['type_id']);
							//parent_id = jsonValue['type_id'];
						});
					},
					error: function(data){
						alert("Ajax Fail");
					}
				});
			}
						
			$('#w').window('close');
			
			alert('Changes Updated Successfully');
			$('#tg').treegrid('reload'); //$('#tt').treegrid('reload',rowID);				
		}
	}
	
	
	function cancelItem()
	{
		$('#w').window('close');
	}
	
	function addItem()
	{
		//alert('checking');
		var input_arr	= new Array();
		
		var grp_arr	= new Array('group_name', 'group_description');
		
		var ord_arr	= new Array('order_name', 'order_description', 'order_sequence', 'order_from', 'order_procedurecode',
					    'order_standardcode', 'order_bodysite','order_specimentype', 'order_administervia', 'order_laterality');
		
		var res_arr	= new Array('result_name', 'result_description', 'result_sequence', 'result_defaultunits', 'result_defaultrange', 'result_followupservices');
		
		var rec_arr	= new Array('reccomendation_name', 'reccomendation_description', 'reccomendation_sequence',
					    'reccomendation_defaultunits', 'reccomendation_defaultrange', 'reccomendation_followupservices');
		
		
		var column_arr	= {'group_name': 'name','group_description':'description',
					'order_name': 'name','order_description':'description','order_sequence':'seq','order_from':'lab_id',
					'order_procedurecode':'procedure_code','order_standardcode':'standard_code','order_bodysite':'body_site',
					'order_specimentype':'specimen','order_administervia':'route_admin','order_laterality':'laterality',
					'result_name': 'name','result_description':'description','result_sequence':'seq',
					'reccomendation_name': 'name','reccomendation_description':'description','reccomendation_sequence':'seq',
					'result_defaultunits':'units','result_defaultrange':'range','result_followupservices':'related_code',
					'reccomendation_defaultunits':'units','reccomendation_defaultrange':'range','reccomendation_followupservices':'related_code'};
					
		
		var parent_id;		
		parent_id	= (document.getElementById('exist_typeid').value != "") ? document.getElementById('exist_typeid').value : 0;		
		
		//alert('parent  :'+parent_id);
		
		var params;
		
		if($('#group_name').val().trim() != "")
		{
			params = "";
			//alert("index :"+index+" => "+input_arr[index]);
			params	= "?procedure_type=grp&parent="+parent_id;
			
			for(var ind in grp_arr)
			{
				if($('#'+grp_arr[ind]).val().trim() != "")
				{
					params += "&"+column_arr[grp_arr[ind]]+"="+$('#'+grp_arr[ind]).val();
				}
			}
			//alert("submitting : group params : "+params);
			//return false;
			$.ajax({
				type: "GET",
				cache: false,
				dataType: "json",
				async: false,
				url: "./configuration/addConfigDetails"+params,
				data: {							
					},
				success: function(data) {
					//alert("Ajax success"+data);
					//alert("myObject is " + data.toSource());
					$.each(data, function(jsonIndex, jsonValue){
						//alert("new type id :"+jsonValue['type_id']);
						parent_id = jsonValue['type_id'];
					});
				},
				error: function(data){
					alert("Ajax Fail");
				}
			});
			
			
		}
		
		if($('#order_name').val().trim() != "")
		{
			params = "";
			//alert("index :"+index+" => "+input_arr[index]);
			params	= "?procedure_type=ord&parent="+parent_id;
			
			for(var ind in ord_arr)
			{
				if($('#'+ord_arr[ind]).val().trim() != "")
				{
					params += "&"+column_arr[ord_arr[ind]]+"="+$('#'+ord_arr[ind]).val();
				}
			}
			//alert("submitting : order params : "+params);
			
			$.ajax({
				type: "GET",
				cache: false,
				dataType: "json",
				async: false,
				url: "./configuration/addConfigDetails"+params,
				data: {							
					},
				success: function(data) {
					//alert("Ajax success"+data);
					//alert("myObject is " + data.toSource());
					$.each(data, function(jsonIndex, jsonValue){
						//alert("new type id :"+jsonValue['type_id']);
						parent_id = jsonValue['type_id'];
					});
				},
				error: function(data){
					alert("Ajax Fail");
				}
			});
		}
		
		if($('#result_name').val().trim() != "")
		{
			params = "";
			//alert("index :"+index+" => "+input_arr[index]);
			params	= "?procedure_type=res&parent="+parent_id;
			
			for(var ind in res_arr)
			{
				if($('#'+res_arr[ind]).val().trim() != "")
				{
					params += "&"+column_arr[res_arr[ind]]+"="+$('#'+res_arr[ind]).val();
				}
			}
			//alert("submitting : result params : "+params);
			$.ajax({
				type: "GET",
				cache: false,
				dataType: "json",
				async: false,
				url: "./configuration/addConfigDetails"+params,
				data: {							
					},
				success: function(data) {
					//alert("Ajax success"+data);
					//alert("myObject is " + data.toSource());
					$.each(data, function(jsonIndex, jsonValue){
						//alert("new type id :"+jsonValue['type_id']);
						parent_id = jsonValue['type_id'];
					});
				},
				error: function(data){
					alert("Ajax Fail");
				}
			});
		}
		
		if($('#reccomendation_name').val().trim() != "")
		{
			params = "";
			//alert("index :"+index+" => "+input_arr[index]);
			params	= "?procedure_type=rec&parent="+parent_id;
			
			for(var ind in rec_arr)
			{
				if($('#'+rec_arr[ind]).val().trim() != "")
				{
					params += "&"+column_arr[rec_arr[ind]]+"="+$('#'+rec_arr[ind]).val();
				}
			}
			//alert("submitting : reccomendation params : "+params);
			$.ajax({
				type: "GET",
				cache: false,
				dataType: "json",
				async: false,
				url: "./configuration/addConfigDetails"+params,
				data: {							
					},
				success: function(data) {
					//alert("Ajax success"+data);
					//alert("myObject is " + data.toSource());
					$.each(data, function(jsonIndex, jsonValue){
						//alert("new type id :"+jsonValue['type_id']);
						parent_id = jsonValue['type_id'];
					});
				},
				error: function(data){
					alert("Ajax Fail");
				}
			});
		}
				
		$('#w').window('close');
		alert('Configuration Added Successfully');
		$('#tg').treegrid('reload');
	}
	
	
	
	
	function addExist()
	{
		document.getElementById('action').value = "addExist";	
		
		$('#tg').treegrid({
			
			onClickRow: function(row) {
				//alert("ADD EXIST ITEM");
				//alert('in addexist '+document.getElementById('action').value);
				var parentNode = $('#tg').treegrid('getParent' , row.id);
				//alert("parent :"+parentNode)
				if(parentNode != null)
				{
					var parentNodeId = parentNode.id;
				}
				//alert("row id :"+row.id);				
				
				clearAll();
		
				showCategory('group');
				showCategory('order');	
				showCategory('result');
				showCategory('reccomendation');	
				
				document.getElementById('exist_typeid').value = row.id;
				
				$('#w').window('open');	
			},			
		})	
	}
	
	/*function addExistItem()
	{
		var input_arr	= new Array();
		
		var column_arr	= {'Name': 'name','Description':'description','Sequence':'seq','Order From':'order_from',
					'Procedure Code':'procedure_code','Standard Code':'standard_code','Body Site':'body_site',
					'Specimen Type':'specimen','Administer Via':'route_admin','Laterality':'laterality',
					'Default Units':'units','Default Range':'range','Followup Services':'related_code'};
		
		alert("in addItem ");
		//var rows 	= $('#pg').propertygrid('getChanges');
		
		//alert("myObject is " + rows.toSource());
		//return false;
		
		var parent_id	= document.getElementById('exist_typeid').value;
		
		alert('parent  :'+parent_id);
		return false;
		var obj;			
		
		var curr_row	= "";
			
		for(var i=0; i<rows.length; i++){
							
			//alert(rows[i].id+' => ' + rows[i].name + ':' + rows[i].value);
			
			if(curr_row	!= rows[i].group)
			{
				input_arr[rows[i].group]	= new Array();
			}
			input_arr[rows[i].group][rows[i].name] = rows[i].value;
			curr_row	= rows[i].group;					
		}
			
		for(var index in input_arr)
		{
			var params = "";
			//alert("index :"+index+" => "+input_arr[index]);
			params	= "?procedure_type="+index+"&parent="+parent_id;
			var sub_arr	= new Array();
			sub_arr		= input_arr[index];
			for(var ind in sub_arr)
			{
				//alert("sub index :"+ind+" => "+sub_arr[ind]);
				params	+= "&"+column_arr[ind]+"="+sub_arr[ind];						
			}	
			//alert("submitting : "+index+" params : "+params);
			
			$.ajax({
				type: "GET",
				cache: false,
				dataType: "json",
				async: false,
				url: "./configuration/addConfigDetails"+params,
				data: {							
					},
				success: function(data) {
					//alert("Ajax success"+data);
					//alert("myObject is " + data.toSource());
					$.each(data, function(jsonIndex, jsonValue){
						//alert("new type id :"+jsonValue['type_id']);
						parent_id = jsonValue['type_id'];
					});
				},
				error: function(data){
					alert("Ajax Fail");
				}
			});
		}
		
		$('#w').window('close');
		alert('Configuration Added Successfully');
		$('#tg').treegrid('reload'); 
	}
	*/
	
	function statusChange(div_id)
	{
		var indicator;
		
		if(div_id == "group_indicator")
		{
			//alert(document.getElementById('group_indicator_value').value);
			indicator = document.getElementById('group_indicator_value').value;
			if(indicator == "+")
			{
				expandCategory('group');
			}
			else
			{
				collapseCategory('group');
			}
		}
		else if(div_id == "order_indicator")
		{
			//alert(document.getElementById('order_indicator_value').value);
			indicator = document.getElementById('order_indicator_value').value;
			if(indicator == "+")
			{
				expandCategory('order');
			}
			else
			{
				collapseCategory('order');
			}
		}
		else if(div_id == "result_indicator")
		{
			//alert(document.getElementById('result_indicator_value').value);
			indicator = document.getElementById('result_indicator_value').value;
			if(indicator == "+")
			{
				expandCategory('result');
			}
			else
			{
				collapseCategory('result');
			}
		}
		else if(div_id == "reccomendation_indicator")
		{
			//alert(document.getElementById('reccomendation_indicator_value').value);
			indicator = document.getElementById('reccomendation_indicator_value').value;
			if(indicator == "+")
			{
				expandCategory('reccomendation');
			}
			else
			{
				collapseCategory('reccomendation');	
			}
		}
	}
	function clearAll()
	{
		document.getElementById('config_form').reset();
		
		/*COLLAPSE ALL TYPE CATEGORIES*/
		collapseCategory('group');
		collapseCategory('order');
		collapseCategory('result');
		collapseCategory('reccomendation');		
	}
	
	
	function collapseCategory(div)
	{
		document.getElementById(div+'_div').style.display	= 'none';
		document.getElementById(div+'_indicator_value').value 	= "+";
		document.getElementById(div+'_indicator').innerHTML 	= "+";
	}
	
	function expandCategory(div)
	{
		document.getElementById(div+'_div').style.display	= 'block';
		document.getElementById(div+'_indicator_value').value 	= "-";
		document.getElementById(div+'_indicator').innerHTML 	= "-";
	}
	
	function hideCategory(div)
	{
		document.getElementById(div+'_title').style.display	= 'none';		
	}
	
	function showCategory(div)
	{
		document.getElementById(div+'_title').style.display	= 'block';		
	}
	
