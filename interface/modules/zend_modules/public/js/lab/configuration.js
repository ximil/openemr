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
		//alert('add '+document.getElementById('action').value);
		document.getElementById('action').value = "add";
		//alert('add '+document.getElementById('action').value);
		$('#pg').propertygrid({						
			url: './configuration/getAddConfigDeatils',  
			showGroup: true,  
			columns: addcolumns,
			width:700,
			height:'auto'//500
		});
		
		$('#w').window('open');
		
		collapseChildGroups();			
	}
	
	function editItem() {
		document.getElementById('action').value = "edit";
		
		$('#tg').treegrid({
			
			onClickRow: function(row) {
				//alert('going 2 reload'+row);
				
				var parentNode = $('#tg').treegrid('getParent' , row.id);
				//alert("parent :"+parentNode)
				if(parentNode != null)
				{
					var parentNodeId = parentNode.id;
				}
				//alert("row id :"+row.id);
				$('#pg').propertygrid({
					
					url: './configuration/getConfigDeatils?type_id='+ row.id,  
					showGroup: true,  
					columns: mycolumns,
					width:700,
					height:'auto'//500
				});					
				
				$('#w').window('open');
				
				collapseChildGroups();
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
		if(document.getElementById('action').value == "add")
		{
			addItem();
		}
		else
		{
			var input_arr	= new Array();
			
			var column_arr	= {'Name': 'name','Description':'description','Sequence':'seq','Order From':'order_from',
						'Procedure Code':'procedure_code','Standard Code':'standard_code','Body Site':'body_site',
						'Specimen Type':'specimen','Administer Via':'route_admin','Laterality':'laterality',
						'Default Units':'units','Default Range':'range','Followup Services':'related_code'};
			
			//alert("in saveItem "+document.getElementById('action').value);
			var rows = $('#pg').propertygrid('getChanges');
			
			var curr_row	= "";
			
			for(var i=0; i<rows.length; i++){
								
				//alert(rows[i].id+' => ' + rows[i].name + ':' + rows[i].value);
				if(curr_row	!= rows[i].id)
				{
					input_arr[rows[i].id]	= new Array();
				}
				input_arr[rows[i].id][rows[i].name] = rows[i].value;
				curr_row	= rows[i].id;					
			}	
			
			for(var index in input_arr)
			{
				var params = "";
				//alert("index :"+index+" => "+input_arr[index]);
				params	= "?type_id="+index;
				var sub_arr	= new Array();
				sub_arr		= input_arr[index];
				for(var ind in sub_arr)
				{
					//alert("sub index :"+ind+" => "+sub_arr[ind]);
					params	+= "&"+column_arr[ind]+"="+sub_arr[ind];						
				}
				
				//alert("params :"+params);
				//alert("submit ID : "+index+" params : "+params);
				$.ajax({
					type: "GET",
					cache: false,
					dataType: "json",
					url: "./configuration/saveConfigDetails"+params,
					data: {							
						},
					success: function(data) {
						//alert("Ajax success");
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
		var input_arr	= new Array();
		
		var column_arr	= {'Name': 'name','Description':'description','Sequence':'seq','Order From':'order_from',
					'Procedure Code':'procedure_code','Standard Code':'standard_code','Body Site':'body_site',
					'Specimen Type':'specimen','Administer Via':'route_admin','Laterality':'laterality',
					'Default Units':'units','Default Range':'range','Followup Services':'related_code'};
		
		//alert("in addItem ");
		var rows 	= $('#pg').propertygrid('getChanges');
		
		//alert("myObject is " + rows.toSource());
		//return false;
		
		var parent_id	= 0;
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
			alert("submitting : "+index+" params : "+params);
			
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
	
	
	function reloadTreeGrid()
	{
		alert('in reload func');
	}
	
