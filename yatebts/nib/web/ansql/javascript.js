/**
 * Copyright (C) 2008-2014 Null Team
 *
 * This software is distributed under multiple licenses;
 * see the COPYING file in the main directory for licensing
 * information for this specific distribution.
 *
 * This use of this software may be subject to additional restrictions.
 * See the LEGAL file in the main directory for details.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */ 

/** 
 * Get Internet Explorer Version
 * @return the version of Internet Explorer or a -1 (indicating the use of another browser)
 */
function getInternetExplorerVersion()
{
	var rv = -1; // Return value assumes failure.
	if (navigator.appName == 'Microsoft Internet Explorer') {
		var ua = navigator.userAgent;
		var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null)
			rv = parseFloat( RegExp.$1 );
	}
	return rv;
}

//var ie = getInternetExplorerVersion();

/**
 * Show hidden element 
 * Function tries to set the right  style.display depending on the tag type
 * @param element_id String. Id of the element to be displayed
 */
function show(element_id)
{
	if (typeof ie == 'undefined' || ie==null)
		ie = getInternetExplorerVersion();

	var element = document.getElementById(element_id);
	if (element == null)
		return;

	switch (element.tagName.toLowerCase()) {
		case "tr":
		case "img":
		case "input":
		case "select":
		case "iframe":
			element.style.display = (ie > 1 && ie<=9) ? "block" : "table-row";
			break;
		case "td":
			element.style.display = (ie > 1 && ie<=9) ? "block" : "table-cell";
			break;
		case "table":
			element.style.display = (ie > 1 && ie<=9) ? "block" : "table";
			break;
		default:
			element.style.display = "block";
	}
}

/**
 * Hide element
 * Function sets style.display to 'none'.
 * @param element_id String. Id of the element to be hidden
 */ 
function hide(element_id)
{
	if (typeof ie == 'undefined' || ie==null)
		ie = getInternetExplorerVersion();

	var element = document.getElementById(element_id);
	if (element == null)
		return;
	element.style.display = "none";
}

/**
 * Show/hide advanced fields in a form and change src and title associated 
 * to the image clicked to perform this action
 * This function is used from editObject() from lib.php is used with fields marked as advanced.
 * @param identifier String. In case there are multiple forms in a single page, 
 * all elements from a form should start with this identified
 */
function advanced(identifier)
{
	var form = document.getElementById(identifier);

	var elems = (form!=null) ? form.elements : [];
	var elem_name;

	for (var i=0;i<elems.length;i++) {
		elem_name = elems[i].name;
		if (identifier.length>elem_name.length && elem_name.substr(0,identifier.length)!=identifier)
			continue;
		
		var elem = document.getElementById("tr_"+elem_name);
		if (elem == null || elem.style.display == null || elem.style.display == "")
			continue;

		show_hide("tr_"+elem_name);
	}

	var img = document.getElementById(identifier+"xadvanced");
	
	if (img!=null && img.tagName=="IMG") {
		var imgsrc= img.src;
		var imgarray = imgsrc.split("/");
		if (imgarray[imgarray.length-1] == "advanced.jpg") {
			imgarray[imgarray.length-1] = "basic.jpg";
			img.title = "Hide advanced fields";
		} else {
			imgarray[imgarray.length-1] = "advanced.jpg";
			img.title = "Show advanced fields";
		}

		img.src = imgarray.join("/");
	} else 
		console.log("advanced() was called, but img is null and tagName='"+img.tagName+"'");
}

/**
 * Check/Uncheck all checkboxes from form containing the element
 * Usually used from tableOfObjects() from lib.php
 * @param element. 'Select all' checkbox whose checked state dictates the state of the other checkboxes
 */
function toggle_column(element)
{
	var containing_form = parent_by_tag(element, "form");
	if (containing_form==null)
		return;

	for(var z=0; z<containing_form.length;z++) {
		if (containing_form[z].type != 'checkbox')
			continue;
		if (containing_form[z].disabled == true)
			continue;
		containing_form[z].checked = element.checked;
	}
}

/**
 * Retrieve containing tag of certain type where element resides.
 * @param element Object. Element whose containing element you need
 * Note! This is not the id of the element but the element itself
 * @param tagname String. Lowercase value of the tag type of the desired container
 * @return first parent element with specified tagType or null if not found
 */
function parent_by_tag(element, tagname)
{
	if (element==null)
		return;
        while(true) {
                parent_element = element.parentElement;
                if (parent_element==null)
                        return null;
		
		if (parent_element.tagName.toLowerCase()==tagname)
			return parent_element;

                element = parent_element;
        }
}

/*
 * Show/Hide tabs 
 * Used from generic_tabbed_settings() from lib.php
 * @param count_sections Integer. Total number of tabs
 * @param part_ids String. Particle identifying specific elements to be showed/hidden.
 * Defaults to ''
 */
function show_all_tabs(count_sections,part_ids)
{
	if (part_ids==null)
		part_ids = "";
	var img = document.getElementById("img_show_tabs"+part_ids);
	if (img.src.substr(-12)=="/sm_show.png")
		img.src = "images/sm_hide.png";
	else
		img.src = "images/sm_show.png";
	var i;
	for (i=1; i<count_sections; i++) {
		section_tab = document.getElementById("tab_"+part_ids+i);
		section_fill = document.getElementById("fill_"+part_ids+i);
		if (section_tab==null) {
			alert("Don't have section tab for "+part_ids+i);
			continue;
		}
		if (section_tab.style.display=="") {
			section_tab.style.display = "none";
			if (section_fill!=null)
				section_fill.style.display = "";
		} else {
			section_tab.style.display = "";
			if (section_fill!=null)
				section_fill.style.display = "none";
		}
	}
}

/**
 * Show a section when clicked - when tabs are used.
 * Function sets classname to  "section_selected basic "+custom_css+"_selected" for first section when selected
 * or "section_selected "+custom_css+"_selected" in case another one is selected
 * and to "section basic "+custom_css when first section is closed
 * and to "section "+custom_css when another section is closed
 * @param section_index Integer. Number of the section to be shown. 
 * @param count_sections Integer. Total number of tabs
 * @param part_ids String. Particle identifying specific elements to be showed/hidden.
 * Defaults to ''
 * @param custom_css String. Name of custom css class
 */
function show_section(section_index,count_sections,part_ids,custom_css)
{
	if (part_ids==null)
		part_ids="";
	if (custom_css==null)
		custom_css="";

	var i, section_div, section_tab;
	for (i=0; i<count_sections; i++) {
		section_tab = document.getElementById("tab_"+part_ids+i);
		section_div = document.getElementById("sect_"+part_ids+i);
		if (section_tab==null) {
			console.log("Don't have section tab for "+i);
			continue;
		}
		if (section_div==null) {
			console.log("Don't have section div for "+i);
			continue;
		}
		if (i==section_index) {
			if (i==0)
				section_tab.className = "section_selected basic "+custom_css+"_selected";
			else
				section_tab.className = "section_selected "+custom_css+"_selected";
			section_div.style.display = "";
		} else {
			cls = section_tab.className;
			if (cls.substr(0,16)=="section_selected") {
				if (i==0)
					section_tab.className = "section basic "+custom_css;
				else
					section_tab.className = "section "+custom_css;
				section_div.style.display = "none";
			}
		}
	}
}

/**
 * Show/hide comment associated to a field
 * @param id String. Id of the object whose comment should be shown
 */
function show_hide_comment(id)
{
	show_hide("comment_"+id);
}

/**
 * Show/hide element. Changes visibility of the element
 * @param element_id String. Id of the element to to shown/hidden
 */ 
function show_hide(element_id)
{
	var element = document.getElementById(element_id);
	if (element==null)
		return;

	if (element.style.display=="none")
		show(element_id);
	else
		hide(element_id);
}

/**
 * Make HTTP request to specified url.
 * encodeURI function is used on url before using @make_api_request function
 * @param url String. Where to make request to
 * @param cb Callback. If set, call it passing response from HTTP request as argument
 * @param aync Bool. If set, specifies if the request should be handled asynchronously or not. 
 * Default async is true (asynchronous) 
 */
function make_request(url, cb, async)
{
        url = encodeURI(url);
		if (typeof async === 'undefined')
			async = true;
        make_api_request(url, cb, async);
}

/**
 * Make HTTP request to specified url. 
 * If callback cb is defined, call with by passing response from HTTP request as parameter
 * Use @make_reques function to make sure url is encoded since this function assumes it already was encoded.
 * @param url String. Where to make request to
 * @param cb Callback. If set, call it passing response from HTTP request as argument
 * @param aync Bool. If set, specifies if the request should be handled asynchronously or not. 
 * Default async is true (asynchronous)
 */
function make_api_request(url, cb, async)
{
	xmlhttp = GetXmlHttpObject();
	if (xmlhttp == null) {
		alert("Your browser does not support XMLHTTP!");
		return;
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			var response = xmlhttp.responseText;
			if (typeof cb != 'undefined' && cb !== null) 
				call_function(cb,response);
		}
	}
	if (typeof async === 'undefined')
		async = true;
	xmlhttp.open("GET", url, async);
	xmlhttp.send(null);
}

/**
 * Make callback 
 * @param cb Callback. Name of the function to be called
 * The callback can be set as object:
 * cb={name:name_func, param:value}
 * @param param. Parameter to be passed when calling cb
 */
function call_function(cb, param)
{
	if (cb && typeof(cb) === "function") {
		// execute the callback, passing parameters as necessary
		cb(param);
	} else if (cb && typeof(cb) === "object" && typeof(cb.name) === 'function') {
		cb.name(param, cb.param);
	} else
		console.error("Trying to call invalid callback "+cb.toString()+", type for it is: "+typeof(cb));
}

/**
 * Retrieve object used to make HTTP request.
 * @returns object. Function returns new XMLHttpRequest or ActiveXObject("Microsoft.XMLHTTP") depending on browser or null if none of the two is available
 */
function GetXmlHttpObject()
{
        if (window.XMLHttpRequest)
        {
                /* code for IE7+, Firefox, Chrome, Opera, Safari*/
                return new XMLHttpRequest();
        }
        if (window.ActiveXObject)
        {
                /* code for IE6, IE5*/
                return new ActiveXObject("Microsoft.XMLHTTP");
        }
        return null;
}

/**
 * Check if required fields were set.
 * The required_fields global variable is checked
 * Ex: required_fields={"username":"username", "contact_info":"Contact information"}
 * "contact_info" is the actual field_name in the form while "Contact information" is what the user sees associated to that form element
 * @return Bool. True when required fields are set, false otherwise 
 * If required_fields is undefined then function returns true and a message is logged in console
 */
function check_required_fields()
{
	if (typeof(required_fields) === "undefined") {
		console.log("The required fields are not defined!");
		return false;
	}

	var err = "";
	// variable required_fields is a global array 
	// it is usually created from method requiredFieldsJs() from Wizard class

	var field_name, field_value, field, tr_field;
	for (field_name in required_fields) {
		tr_field = document.getElementById("tr_"+field_name);
		if ((tr_field.getAttribute("trigger") == "\\\"true\\\"" || tr_field.getAttribute("trigger")=="true") && tr_field.style.display=="none")
			continue;

		field_value = document.getElementById(field_name).value;
		if (field_value=="")
			err += "Please set "+required_fields[field_name]+"! ";
	}
	if (err!="") {
		error(err);
		return false;
	}
	return true;
}

/**
 * Display error. Function currently used alert.
 * @param error String. Error to be displayed to the user
 */
function error(error)
{
	alert(error);
}

/**
 * Verify if value is numeric. Used isNaN js function.
 * @param val String. Value to be checked
 * @return Bool
 */
function is_numeric(val)
{
	return !isNaN(val);  
}

/**
 * Delete element
 * @param id String. Id of the tag to remove
 */
function delete_element(id)
{
	var obj = document.getElementById(id);
	if (obj)
		obj.parentNode.removeChild(obj);
}

/**
 * Change object/tag id
 * @param id String. Current id of the tag
 * @param new_id String.
 */
function set_id_obj(id, new_id)
{
	var obj = document.getElementById(id);
	if (obj)
		obj.id = new_id;
}

/**
 * Set value of object/tag
 * @param id String. Id of the tag 
 * @param val String. Value to be set in tag with specified id
 */
function set_val_obj(id, val)
{
	var obj = document.getElementById(id);
	if (obj)
		obj.value = val;
}

/**
 * Submit form 
 * @param formid String. Id of the form to submit
 */
function submit_form(formid)
{
	var form_to_submit = document.getElementById(formid);
	if (form_to_submit)
		form_to_submit.submit();
}

/** 
 * Retrieve selected value from dropdown
 * @param id_name String. The id of the select tag 
 * @return String with selected option or null if tag with it is not found or no value is selected
 */
function get_selected(id_name)
{
	var selector_obj = document.getElementById(id_name);
	if (selector_obj==null)
		return null;
	var sel = selector_obj.options[selector_obj.selectedIndex].value || selector_obj.options[selector_obj.selectedIndex].text;
	return sel;
}

/** 
 * Sets innerHTML for specific tag
 * @param id String. Id of the tag to set content into
 * @param html String. The content to be set in the tag specified by id
 */
function set_html_obj(id, html)
{
        var obj = document.getElementById(id);
        if (obj)
                obj.innerHTML = (html == null) ? "" : html;
}

/**
 * Show fields after 'Add another ...' link is pressed. 
 * This fields must already exist and be hidden.
 * When made using display_pair() you must set triggered_by => "" for the fields
 * Function will show all fields ending in link_index from form 
 * and will hide clicked link and display the one with the next index
 * @param link_index Integer. The index that unites fields to be show as part of another object
 * @param link_name String. Name of the add link. Ex: add, add_contact
 * @param hidden_fields Array. Contains the name of the input fields of type 'hidden' 
 * @param level_fields Bool. If true, the fields in the FORM are on more levels
 * @param name_obj_title String. Common piece of the name of the objtitle field for this level of objects
 * @param border_elems Object. {"start":"", "end":""}. Name between which we start showing elements. Used when you have more types of objects so _index is not unique  
 * Ex: {"end":name_border} // between start of the form and name_border
 * Ex: {"start":name_border} // between name_border and end of form
 * Ex: {"start":border1, "end":border2} // between border1 and border2 
 */
function fields_another_obj(link_index, link_name, hidden_fields, level_fields, name_obj_title, border_elems, multiple_subtitle)
{
	console.log("Entered fields_another_obj() ", arguments);

	if (!is_numeric(link_index)) {
		console.error("Called fields_another_obj with non numeric param link_index: "+link_index);
		return;
	}

	if (name_obj_title==undefined)
		name_obj_title = "objtitle";

	var element_name;

	// this is the link that was clicked and should be hidden
	var current_link_id = link_name+(link_index-1);
	if (document.getElementById(current_link_id)==null)
		// maybe previous index is not with build by decresing 1 but by removing the last digit: _ or _1 are current_link_id for _2 or _12
		current_link_id = link_name+link_index.substr(0,link_index.length-1);

	hide(current_link_id);

	// retrieve all elements from same form as the clicked link
	var parentform = parent_by_tag(document.getElementById(current_link_id),"form");
	if (parentform==null) {
		console.error("Can't retrieve parent for for element with id" + current_link_id);
		return;
	}
	elems = parentform.elements;

	// see if there are advanced fields (check button -- it's displayed only when there are fields marked as advanced)
	var show_advanced = document.getElementById("advanced");
	if (show_advanced!=null) {
		console.log("innerHTML advanced button:'"+show_advanced.innerHTML+"'");
		show_advanced = (show_advanced.innerHTML=="Advanced") ? false : true;
	} else
		show_advanced = false;
	console.log("Show advanced: "+show_advanced);

	// show objtitle, if defined
	show("tr_" + name_obj_title + link_index);

	console.log("level_fields="+level_fields);
	if (level_fields!=undefined && level_fields==true)
		show("tr_"+name_obj_title+"_level_"+link_index);

	if (multiple_subtitle!=undefined && multiple_subtitle==true) {
		var id_to_show;
		for (var k=0; k<10; k++) {
			if (k==0)
				id_to_show = "tr_"+name_obj_title+"_level_"+link_index;
			else
				id_to_show = "tr_"+name_obj_title+"_level"+k+"_"+link_index;
			show(id_to_show);
			console.log("Tried to show:'"+id_to_show+"'");
		}
	}

	var id_tr_element, tr_element;
	var have_start = true;
	var have_end = false;

	if (border_elems!=undefined && border_elems.start!=undefined)
		have_start = false;

	for (var i=0; i<elems.length; i++) {

		element_name = elems[i].name;
		if (element_name.substr(-2)=="[]")
			element_name = element_name.substr(0,element_name.length-2);

		if (!have_start && elems[i].name==border_elems.start)
			have_start = true;

		if (border_elems!=undefined && border_elems.end!=undefined && border_elems.end==elems[i].name)
			have_end = true;

		if (!have_start || have_end) {
			//console.log("Skipping "+elems[i].name+": have_start="+have_start+" have_end="+have_end);
			continue;
		}

		// we assume that elements in form have the same "id" and "name"
		// the containing tr is built by concatenanting "tr_" + element_id
		id_tr_element = "tr_" + element_name;

		// if form fields are displayed on more levels
		// split the form elements name by '_' to get their id 
		// and skip the elements that don't have to be displayed
		// otherwise just display links ending in link_index
		if (level_fields!=undefined && level_fields==true) {
			var index_arr = id_tr_element.split("_");
			if (index_arr[index_arr.length-1] !=link_index) 
				continue;

		} else if (id_tr_element.substr(element_name.length+2, id_tr_element.length)!=link_index)  
			continue;

		tr_element = document.getElementById(id_tr_element);

		// this field is advanced -> display it only if user already requested to see advanced fields
		if (tr_element.getAttribute("advanced")=="true" && show_advanced==false)
			continue;

		show(id_tr_element);
	}

	// this is the next link that should be displayed
	show("tr_"+link_name+link_index);

	//if hidden_fields was set, append it to the other already set
	if (hidden_fields != undefined) {
		for (var name in hidden_fields) {
			var input = document.createElement("INPUT");
   			input.setAttribute("type", "hidden");
			input.setAttribute("name", ''+hidden_fields[name]+'');
			input.setAttribute("value", 'off');
			tr_element_hidden = document.getElementById("tr_hidden_fields");
        		tr_element_hidden.appendChild(input);
		}
	}
}

/**
 * Builds part of a link from the fields in a form
 * Values set in form field are escaped with 'encodeURIComponent' before concatenanting
 * @param form_name String. Name of the form where to take the elements from
 * @return String. Part of a link
 * Ex: ?name=Larry&phone_number=4034222222&zipcode=50505
 */ 
function link_from_fields(form_name) 
{
	var form_obj = document.forms[form_name];
	if (form_obj==null)
		return null;
	var qs = '';
	for (e=0;e<form_obj.elements.length;e++) {
		if (form_obj.elements[e].name!='') {
			if (form_obj.elements[e].type=="checkbox" && form_obj.elements[e].checked!=true)
				continue; 
			if (form_obj.elements[e].type!="select-multiple") {
				qs += (qs=='')?'?':'&';
				qs += form_obj.elements[e].name+'='+encodeURIComponent(form_obj.elements[e].value);
			} else {
				for (var i = 0; i < form_obj.elements[e].options.length; i++) {
					if (form_obj.elements[e].options[i].selected) {
						qs += (qs=='')?'?':'&';
						qs +=  form_obj.elements[e].name+"="+form_obj.elements[e].options[i].value;
					}
				}
			}
		}
	}
	console.log("Result link_from_fields: "+qs);
	return qs;
}

/**
 * Removed value from array
 * Note! Only the first found value is removed
 * @param val. Value to be removed
 * @param arr Array. Array to remove value from
 */
function removeArrayValue(val,arr)
{
	if (!val)
		return;
	for (var i=0; i<arr.length; i++) {
		if (arr[i]==val) {
			arr.splice(i, 1);
			break;
		}
	}
}

/**
 * Adds div element with specified id, content and css class in another element
 * Element is inserted at the beggining
 * @param parent_id String. Id of the parent element in which the new element will be added
 * @param element_id String. Id of the new element that will be added
 * @param html String. Content to be set inside new div
 * @param css_class String. CSS class(es) for the new div
 */
function add_element_with_class(parent_id, element_id, html, css_class)
{
	parent_elem = document.getElementById(parent_id);
	if (parent_elem==null)
		return;
	newDiv = document.createElement("div");
	newDiv.setAttribute("id", element_id);
	newDiv.innerHTML = html;
	if (css_class)
		newDiv.className = css_class;
	parent_elem.parentNode.insertBefore(newDiv, parent_elem);
}

/**
 * Adds div element with specified id, content and css class in another element
 * Element is inserted at the beggining
 * @param parent_id String. Id of the parent element in which the new element will be added
 * @param element_id String. Id of the new element that will be added
 * @param html String. Content to be set inside new div
 */
function add_element(parent_id, element_id, html)
{
	add_element_with_class(parent_id, element_id, html, null);
}

/**
 * Used when user selects a 'Custom' option from a dropdown
 * Adds input field with id 'custom_'+id of the dropdown
 * @param custom_value String. Value to set in the newly added field
 * @param dropdown_id String. Id of the dropdown 
 */
function custom_value_dropdown(custom_value,dropdown_id)
{
	// the id of the custom field that will be added
	var custom_id = "custom_"+dropdown_id;
	var custom_field = document.getElementById(custom_id);

	var selected = get_selected(dropdown_id);
	if (selected=="Custom") {
		var dropdown = document.getElementById(dropdown_id);
		var parent_td = dropdown.parentNode;
		if (custom_field==null) {
			parent_td.appendChild(document.createElement("br"));
			// custom_field wasn't added in the page => add it here
			var input = document.createElement("INPUT");
			input.setAttribute("type", "text");
			input.setAttribute("class", "margintop");
			input.setAttribute("id", ''+custom_id+'');
			input.setAttribute("name", ''+custom_id+'');
			if (custom_value!=null)
				input.setAttribute("value", ''+custom_value+'');
			parent_td.appendChild(input);
		} else {
			// custom_field is already in the page => display it
			custom_field.style.display = "";
		}

	} else if (custom_field!=null && custom_field.style.display!="none")
		custom_field.style.display = "none";
}
