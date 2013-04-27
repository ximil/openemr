/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = 'MyToolbar';
	config.shiftEnterMode = CKEDITOR.ENTER_P;
        //config.enterMode = CKEDITOR.ENTER_BR;
	config.enterMode = CKEDITOR.ENTER_P;
 
	config.toolbar_MyToolbar =
	[
		{ name: 'document', items : [ 'Print','SpellChecker','Scayt' ] },
		{ name: 'clipboard', items : [ 'Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace' ] },
		{ name: 'basicstyles', items : [ 'SelectAll','RemoveFormat' ] },
		{ name: 'basicstyles1', items : [ 'Bold','Italic','Underline','Strike' ] },
		{ name: 'basicstyles2', items : [ 'Subscript','Superscript' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
		{ name: 'paragraph1', items : [ 'Outdent','Indent','Blockquote' ] },
		'/',
		{ name: 'paragraph2', items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
		{ name: 'insert', items : [ 'Image','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] }	
	];
};

/*{ name: 'external', items : [ 'Flash','Iframe' ] },*/