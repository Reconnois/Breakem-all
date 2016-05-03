
"use strict";
window.addEventListener('load', function load(){
	// Cette ligne permet la 'supression' de l'event de load pour liberer du cache (on devrait faire ça idéalement pour tous les events utilisés une seule fois) 
	window.removeEventListener('load', load, false);
	connection.init();
	navbar.init();
	deconnection.init();
	register.init();
});

// Cette fonction sera utilisée dans beaucoup d'objets utilisant de l'ajax
//  Voilà pouruqoi elle est définie en tant que fct générale
function tryParseData(rawData){
	try {
		var obj = jQuery.parseJSON(rawData);
		return obj;
	}
	catch(err) {
		console.log(rawData);
		alert("Problem during server processes \n Check console for details");
	}
	return false;
}

var navbar = {
    init: function(){        
		navbar.shrink();      
        navbar.openNavbarSide();
        navbar.search.toggle();
        navbar.search.close();
        navbar.form.subscribe();
        navbar.form.login();
        navbar.form.closeFormKey();
        navbar.form.closeFormClick();
    },
    preventShrink: false,
    shrink: function(force){
    	if(!this.preventShrink){   		
	        $(window).scroll(function(){
	            if($(window).scrollTop() > 50){
	                $("#navbar").removeClass('full');
	                $("#navbar").addClass('shrink');
	            }else{
	                $("#navbar").removeClass('shrink');
	                $("#navbar").addClass('full');
	            }
	        });
	        return;
	    }
	    $("#navbar").removeClass('full');
        $("#navbar").addClass('shrink');
    },
    openNavbarSide : function(){
        $('#navbar-toggle').on('click', function(){
            if($('.navbar-side-menu').hasClass('navbar-collapse')){
                $('.navbar-side-menu').removeClass('navbar-collapse');
            }else{
                $('.navbar-side-menu').addClass('navbar-collapse');
            }
        });
    },
    search : {
        toggle: function(){

            $(document).on('click', '.search-toggle', function(){
                $('.search-page').removeClass('hidden-fade');
                setTimeout(function() {
                    $(".search-page").removeClass('hidden');
                }, 0);
            });
        },
        close: function(){
            $(document).on('click', '.btn-close', function(e){

                $(e.currentTarget).parents('.search-page').addClass('hidden-fade');
                setTimeout(function() {
                    $(".search-page").addClass('hidden');
                }, 800);
            });
        }
    },
    form : {
        subscribe : function(){
            $('#navbar-login').on('click', function(){
            	$('.index-modal-login').addClass('form-bg-active');
                $('.index-modal').removeClass('hidden-fade');
                setTimeout(function() {
                    $(".index-modal").removeClass('hidden');
                }, 0);
                $('#login-form').removeClass('hidden');
                $('#subscribe-form').addClass('hidden');
                $('.inscription_rapide').addClass('fadeDown').removeClass('fadeOutUp');
                $('body').css('overflow', 'hidden');
            });
        },
        login : function(){
        	$('.index-modal-login').addClass('form-bg-active');
            $('#navbar-inscription').on('click', function(){
                $('.index-modal').removeClass('hidden-fade');
                setTimeout(function() {
                    $(".index-modal").removeClass('hidden');
                }, 0);
                $('#subscribe-form').removeClass('hidden');
                $('#login-form').addClass('hidden');
                $('.inscription_rapide').addClass('fadeDown').removeClass('fadeOutUp');
                $('body').css('overflow', 'hidden');
            });
        },
        closeForm : function(){
            $('.index-modal').addClass('hidden-fade').addClass('fade').addClass('hidden'); 
            $('body').css('overflow', 'visible');       
        },
        closeFormKey: function(){
    	 	$(document).keyup(function(e) {
                if (e.keyCode == 27) {

                	$('.inscription_rapide').addClass('fadeOutUp').removeClass('fadeDown');	
                    
                    setTimeout(function() {
                    	navbar.form.closeForm();			    	
                	}, 700);
                }
            });
        },
        closeFormClick: function(){

        	$('.index-modal-login').on('click', function(e){
			    if(!$(e.target).is('.inscription_rapide') && !$(e.target).is('.inscription_rapide form, input, button, label, p, a')) {			    			    			    			   			    		

			    	$('.inscription_rapide').addClass('fadeOutUp').removeClass('fadeDown');	

			    	setTimeout(function() {
                    	navbar.form.closeForm();			    	
                	}, 700);		    		
			    }
			});
        }
    }
};

var register = {
	init: function(){
		this.setFormToWatch();
		if(!(this.getFormToWatch() instanceof jQuery)){
			console.log("Missing form");
			return;
		}
		this.setPseudoToWatch();
		if(!(this.getPseudoToWatch() instanceof jQuery)){
			console.log("Missing pseudo");
			return;
		}
		this.setEmailToWatch();
		if(!(this.getEmailToWatch() instanceof jQuery)){
			console.log("Missing email");
			return;
		}
		this.setPassToWatch();
		if(!(this.getPassToWatch() instanceof jQuery)){
			console.log("Missing pass");
			return;
		}
		this.setPassCheckToWatch();
		if(!(this.getPassCheckToWatch() instanceof jQuery)){
			console.log("Missing passcheck");
			return;
		}
		this.setCguToWatch();
		if(!(this.getCguToWatch() instanceof jQuery)){
			console.log("Missing cgu");
			return;
		}
		this.setDayToWatch();
		if(!(this.getDayToWatch() instanceof jQuery)){
			console.log("Missing day");
			return;
		}
		this.setMonthToWatch();
		if(!(this.getMonthToWatch() instanceof jQuery)){
			console.log("Missing month");
			return;
		}
		this.setYearToWatch();
		if(!(this.getYearToWatch() instanceof jQuery)){
			console.log("Missing year");
			return;
		}
		this.sendEvent();
	},
	setFormToWatch: function(){
		this._form = jQuery("#register-form");
	},
	setPseudoToWatch: function(){
		this._pseudo = this._form.find('input[name="pseudo"]');
	},
	setEmailToWatch: function(){
		this._email = this._form.find('input[name="email"]');
	},
	setPassToWatch: function(){
		this._mdp = this._form.find('input[name="password"]');
	},
	setPassCheckToWatch: function(){
		this._mdpcheck = this._form.find('input[name="password_check"]');
	},
	setCguToWatch: function(){
		this._cgu = this._form.find('input[name="cgu"]');
	},
	setDayToWatch: function(){
		this._day = this._form.find('input[name="day"]');
	},
	setMonthToWatch: function(){
		this._month = this._form.find('input[name="month"]');
	},
	setYearToWatch: function(){
		this._year = this._form.find('input[name="year"]');
	},
	getFormToWatch: function(){return this._form;},
	getPseudoToWatch: function(){return this._pseudo;},
	getEmailToWatch: function(){return this._email;},
	getPassToWatch: function(){return this._mdp;},
	getPassCheckToWatch: function(){return this._mdpcheck;},
	getCguToWatch: function(){return this._cgu;},
	getDayToWatch: function(){return this._day;},
	getMonthToWatch: function(){return this._month;},
	getYearToWatch: function(){return this._year;},

	isEmailValid: function(){
		var jQEmail = this.getEmailToWatch();
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
		if(jQEmail.val().match(mailformat) || jQEmail.val().length == 0){
			return true;
		}
		this.highlightInput(jQEmail);
		console.log("email fail");
		return false;
	},
	isPseudoValid: function(){
		var jQPseudo = this.getPseudoToWatch();
		var unauthorizedChars = /[^a-zA-Z-0-9]/;
		if(jQPseudo.val().match(unauthorizedChars) || jQPseudo.val().length == 0){
			this.highlightInput(jQPseudo);
			console.log("peudo fail");
			return false;
		}
		return true;
	},
	isPasswordValid: function(jQPassword){
		var unauthorizedChars = /[^a-zA-Z-0-9]/;
		if(jQPassword.val().match(unauthorizedChars) || jQPassword.val().length == 0){
			this.highlightInput(jQPassword);
			console.log("pass fail");
			return false;
		}
		return true;
	},
	isBirthValid: function(){
		var d = this.getDayToWatch().val();
		var m = this.getMonthToWatch().val();
		var y = this.getYearToWatch().val();

		if (isNaN(Number(d)))
			return false;
		if (isNaN(Number(m)))
			return false;
		if (isNaN(Number(y)))
			return false;
		
		try {
			// create the date object with the values sent in (month is zero based)
			var dt = new Date(y,m-1,d,0,0,0,0);

			// get the month, day, and year from the object we just created 
			var mon = dt.getMonth() + 1;
			var day = dt.getDate();
			var yr  = dt.getYear() + 1900;

			// if they match then the date is valid
			if ( mon == m && yr == y && day == d )
				return true;
			console.log("birth fail");
			return false;
		}
		catch(e) {
			console.log("birth fail");
			return false;
		}
		
		return true;
	},
	isCguAccepted: function(){
		if(this.getCguToWatch()[0].checked){
			return true;
		}
		alert("Vous devez accepter les cgu !");
		return false;
	},
	doPasswordsMatch: function(){
		if(this.getPassToWatch().val() == this.getPassCheckToWatch().val())
			return true;
		console.log("passwords don't match");
		return false;
	},
	highlightInput: function(jQinput){
		jQinput.addClass('failed-input');
		jQinput.val('');
		jQinput.focus();
		this.removeFailAnimationEvent(jQinput);
	},
	treatParsedJson: function(obj){
		if(obj.success){
			window.location.assign('confirmation/warningMail');
		}
		else{
			if(obj.errors){
				if(obj.errors.pseudo){
					alert(obj.errors.pseudo);
				}
				else if(obj.errors.email){
					alert(obj.errors.email);
				}
				else{
					console.log(obj.errors);
					alert("Ton formulaire n'a pu être validé\nCheck la console pour pplus de détails");
				}			
			}
		}
	},


	/*### Send Form event ###*/
	sendEvent: function(){
		var _this = this;
		var _form = this.getFormToWatch();
		this._pseudo = _form.find("input[name='pseudo']");
		this._cgu = _form.find("input[name='cgu']");
		this._btn = _form.find("button");
		this._birth_day = _form.find("input[name='day']");
		this._birth_month = _form.find("input[name='month']");
		this._birth_year = _form.find("input[name='year']");

		_form.submit(function(event) {
			event.preventDefault();
			return false;
		});

		this._btn.click(function(event) {
			if (
				_this.isEmailValid() 
				&& _this.isPseudoValid()
				&& _this.isPasswordValid(_this.getPassToWatch())
				&& _this.isPasswordValid(_this.getPassCheckToWatch())
				&& _this.doPasswordsMatch()
				&& _this.isBirthValid()
				&& _this.isCguAccepted()
			) {
				jQuery.ajax({
				  url: 'index/register',
				  type: 'POST',
				  data: {
					  	pseudo  		: _this.getPseudoToWatch().val(),
					    email			: _this.getEmailToWatch().val(),
					    password		: _this.getPassToWatch().val(),
					    password_check	: _this.getPassCheckToWatch().val(),
					    day				: _this.getDayToWatch().val(),
					    month			: _this.getMonthToWatch().val(),
					    year			: _this.getYearToWatch().val()
				  },
				  complete: function(xhr, textStatus) {
				    // console.log("request complted \n");
				  },
				  success: function(data, textStatus, xhr) {
				    var obj = tryParseData(data);
				    if(obj != false){
				    	_this.treatParsedJson(obj);
				    }
				  },
				  error: function(xhr, textStatus, errorThrown) {
				    console.log("request error !! : \t " + errorThrown);
				  }
				});
				
				// return true;
			};
			event.preventDefault();
			return false;
		});

	},

	/*### Remove Animation on keyup event ###*/
	removeFailAnimationEvent: function(jQInput){
		// Le one() permet de ne declencher l'event (keyup ici) qu'une seule fois puis de le supprimer automatiquement
		jQInput.one('keyup', function() {
			jQInput.removeClass('failed-input');
		});
	}
}
var connection = {
	init: function(){
		this.setFormToWatch();
		if(this.getFormToWatch() instanceof jQuery){
			this.sendEvent();
		};		
	},
	setFormToWatch: function(){this._form = jQuery("#login-form");},
	getFormToWatch: function(){return this._form;},
	isEmailValid: function(jQEmail){
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
		if(jQEmail.val().match(mailformat) || jQEmail.val().length == 0){
			return true;
		}
		this.highlightInput(jQEmail);
		return false;
	},
	isPasswordValid: function(jQPassword){
		var unauthorizedChars = /[^a-zA-Z-0-9]/;
		if(jQPassword.val().match(unauthorizedChars) || jQPassword.val().length == 0){
			this.highlightInput(jQPassword);
			return false;
		}
		return true;
	},
	highlightInput: function(jQinput){
		jQinput.addClass('failed-input');
		jQinput.val('');
		jQinput.focus();
		this.removeFailAnimationEvent(jQinput);
	},
	tryParseData: function(rawData){
		try {
			var obj = jQuery.parseJSON(rawData);
			return obj;
		}
		catch(err) {
			console.log(rawData);
			alert("Problem during server processes \n Check console for details");
		}
		return false;
	},
	treatParsedJson: function(obj){
		if(obj.connected){
			location.reload();
		}
		else{
			this.highlightInput(this._email);
			this.highlightInput(this._password);
			if(obj.errors.inputs){
				// missing input !
				alert("you are missing an input");
			}
			else if(obj.errors.user){
				// email and pass don't match
				alert("password and email don't match");
			}
		}
	},


	/*### Send Form event ###*/
	sendEvent: function(){
		var _this = this;
		var _form = this.getFormToWatch();
		var _email = _form.find("input[name='email']");
		var _password = _form.find("input[name='password']");
		var _btn = _form.find('button');

		this._email = _email;
		this._password = _password;
		this._btn = _btn;

		_btn.click(function(event) {
			if (_this.isEmailValid(_email) && _this.isPasswordValid(_password)) {
				jQuery.ajax({
				  url: 'index/connection',
				  type: 'POST',
				  data: {
				  	email: _email.val(),
				  	password: _password.val()
				  },
				  complete: function(xhr, textStatus) {
				    // console.log("request complted \n");
				  },
				  success: function(data, textStatus, xhr) {
				    var obj = tryParseData(data);
				    if(obj != false){
				    	_this.treatParsedJson(obj);
				    }
				  },
				  error: function(xhr, textStatus, errorThrown) {
				    console.log("request error !! : \t " + errorThrown);
				  }
				});
				
				// return true;
			};
			event.preventDefault();
			return false;
		});

	},

	/*### Remove Animation on keyup event ###*/
	removeFailAnimationEvent: function(jQInput){
		// Le one() permet de ne declencher l'event (keyup ici) qu'une seule fois puis de le supprimer automatiquement
		jQInput.one('keyup', function() {
			jQInput.removeClass('failed-input');
		});
	}
}
var deconnection = {
	init: function(){
		this.setBtnToWatch();
		if(this.getBtnToWatch() instanceof jQuery){
			this.clickEvent();			
		};		
	},
	setBtnToWatch: function(){
		this._btn = jQuery("#nav-deconnection");
	},
	getBtnToWatch: function(){return this._btn;},

	clickEvent: function(){
		var _this = this;
		var _btn = this._btn;
		_btn.click(function(event) {
			jQuery.ajax({
				url: 'index/deconnection',
				type: 'POST',
				data: {},
				complete: function(xhr, textStatus) {
					location.reload();
				},
				success: function(data, textStatus, xhr) {

				},
				error: function(xhr, textStatus, errorThrown) {

				}
			});
		});
	}
}
