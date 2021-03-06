"use strict";

window.addEventListener('load', function load(){
	window.removeEventListener('load', load, false);
	mestournois.init();
});

var mestournois = {
	_this: this,
	init: function(){
		//Setter
		mestournois.setMesTournoisOngletOrg();
		mestournois.setMesTournoisOngletPart();
		mestournois.setMesTournoisIhm();

		//Onglet
		mestournois.onClick(mestournois.getMesTournoisOngletOrg());
		mestournois.onClick(mestournois.getMesTournoisOngletPart());

		//Request
		mestournois.requestOrgDefault();
		mestournois.requestOrg();
		mestournois.requestPart();
	},
	//Setter
	setMesTournoisOngletOrg : function(){
		this._mesTournoisOngletOrg = jQuery('#mestournois-onglet-organisateur');
	},
	setMesTournoisOngletPart : function(){
		this._mesTournoisOngletPart = jQuery('#mestournois-onglet-participant');
	},
	setMesTournoisIhm : function(){
		this._mesTournoisIhm = jQuery('.mestournois-ihm');
	},
	//Getter
	getMesTournoisIhm : function(){
		return this._mesTournoisIhm;
	},
	getMesTournoisOngletOrg : function(){
		return this._mesTournoisOngletOrg;
	},
	getMesTournoisOngletPart : function(){
		return this._mesTournoisOngletPart;
	},
	requestOrgDefault : function(){
		jQuery.get("mestournois/mestournoisOrg", function(result){
			mestournois.getMesTournoisIhm().html(result);
			pagi();
		});
	},
	requestOrg : function(){
		mestournois.getMesTournoisOngletOrg().on("click", function(){
			jQuery.get("mestournois/mestournoisOrg", function(result){
				mestournois.getMesTournoisIhm().html(result);
				pagi();
			});
		});
	},
	requestPart : function(){
		mestournois.getMesTournoisOngletPart().on("click", function(){
			jQuery.get("mestournois/mestournoisPart", function(result){
				mestournois.getMesTournoisIhm().html(result);
				pagi();
			});
		});
	},
	onClick : function(btnClick){
		btnClick.click(function(){
			jQuery(".admin-onglet-li").removeClass('active');
			jQuery(this).addClass('active');
		});
	},
};

function pagi(){

	/**** PAGINATION ****/

    var show_per_page = 5;  
    var number_of_items = $('#liste-derniers-tournois').children().size();  
    var number_of_pages = Math.ceil(number_of_items/show_per_page);  

    //Indcateurs dans la pagination  
    $('#current_page').val(0);  
    $('#show_per_page').val(show_per_page);  
  
    var navigation_html = '<a class="previous_link" href="javascript:previous();">Précèdent</a>';  
    var current_link = 0;  
    while(number_of_pages > current_link){  
        navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">';
        	navigation_html += '<li class="border_menu" numberpage="'+current_link+'">'+ (current_link + 1) +'</li>';
        navigation_html += '</a>';  
        current_link++;  
    }  
    navigation_html += '<a class="next_link" href="javascript:next();">Suivant</a>';  
  

    $('#page_navigation').html(navigation_html);  
  
    //add active_page class to the first page link  
    $('#page_navigation .page_link:first').addClass('active_page');  
    $('#page_navigation .page_link:first li.border_menu').addClass('active_menu');  
  
    //hide all the elements inside content div  
    $('#liste-derniers-tournois').children().css('display', 'none');  
  
    //and show the first n (show_per_page) elements  
    $('#liste-derniers-tournois').children().slice(0, show_per_page).css('display', 'block'); 
}


function previous(){  
  
    new_page = parseInt($('#current_page').val()) - 1;  
    //if there is an item before the current active link run the function  
    if($('.active_page').prev('.page_link').length==true){  
        go_to_page(new_page);  
    }  
  
}  
  
function next(){  
    new_page = parseInt($('#current_page').val()) + 1;  
    //if there is an item after the current active link run the function  
    if($('.active_page').next('.page_link').length==true){  
        go_to_page(new_page);  
    }  
  
}  

function go_to_page(page_num){  
    //Nombre d'élèments par page  
    var show_per_page = parseInt($('#show_per_page').val());  
    var start_from = page_num * show_per_page;   
    var end_on = start_from + show_per_page;  

  	$("li.border_menu").removeClass('active_menu');
  	$("li.border_menu[numberpage='"+page_num+"']").addClass('active_menu');

    //hide all children elements of content div, get specific items and show them  
    $('#liste-derniers-tournois').children().css('display', 'none').slice(start_from, end_on).css('display', 'block');  
  
    /*get the page link that has longdesc attribute of the current page and add active_page class to it 
    and remove that class from previously active page link*/  
    $('.page_link[longdesc=' + page_num +']').addClass('active_page').siblings('.active_page').removeClass('active_page');  
  
    //update the current page input field  
    $('#current_page').val(page_num);  
}
