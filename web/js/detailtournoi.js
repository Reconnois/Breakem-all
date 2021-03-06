var dom = {
	init: function(){
		dom.setDetailTournoiInfos();
		dom.setEquipesLibresSection();
		dom.setPremiersMatchsBtn();
		dom.setMatchsWinnerBtns();
		dom.setCreerProchainsMatchsBtn();
		dom.setKickParticipantsBtns();
		dom.setSjeton();
		dom.setTget();
		if(isElSoloJqueryInstance(dom.getDetailTournoiInfos()) && 
			isElSoloJqueryInstance(dom.getSjeton()) &&
			dom.getTget().length > 10
		)
		adaptMarginToNavHeight(dom.getDetailTournoiInfos());
		else {
			console.log("fail to find all required detailtournoi's DOM");
			return false;
		}
		dom.setBtnsTeam();
		createNextMatchs.init();
		selectMatchWinner.init();
		createFirstMatchs.init();
		tournamentRegister.init();
		tournamentUnregister.init();
		if(!!dom.getKickParticipantsBtns())
			kickPlayer.init();
	},
	setKickParticipantsBtns: function(){
		dom._kickBtns = $('.detailtournoi-kick-btn');
	},
	setCreerProchainsMatchsBtn: function(){
		dom._prochMatchsBtn = $('#detailtournoi-btn-create-next-matchs');
	},
	setMatchsWinnerBtns: function(){
		dom._mWinBtns = $('.detailtournoi-btn-match-select-winner');
	},
	setPremiersMatchsBtn: function(){
		dom._premMatchsBtn = $('#detailtournoi-btn-create-matchs');
	},
	setDetailTournoiInfos: function(){
		dom._detailTournoiInfos = $('section.detailtournoi-infos');
	},
	setEquipesLibresSection: function(){
		dom._eqLibSec = $('.detailtournoi-equipeslibres-section');
	},
	setBtnsTeam: function(){
		dom._btnsTeamJoin = $('.equipelibre-btn-inscription');
	},
	setSjeton: function(){
		dom._sJeton = $('#sJeton');
	},	
	setTget: function(){
		dom._tGet = $_GET('t');
	},
	getDetailTournoiInfos: function(){
		return dom._detailTournoiInfos;
	},
	getEquipesLibresSection: function(){
		return dom._eqLibSec;
	},
	getBtnsTeam: function(){
		return dom._btnsTeamJoin;
	},
	getPremiersMatchsBtn: function(){
		return (isElSoloJqueryInstance(dom._premMatchsBtn)) ? dom._premMatchsBtn : false;
	},
	getCreerProchainsMatchsBtn: function(){
		return (isElSoloJqueryInstance(dom._prochMatchsBtn)) ? dom._prochMatchsBtn : false;
	},
	getMatchsWinnerBtns: function(){
		return (dom._mWinBtns.length > 0) ? dom._mWinBtns : false;
	},
	getSjeton: function(){
		return dom._sJeton;
	},	
	getTget: function(){
		return dom._tGet;
	},
	getKickParticipantsBtns: function(){
		return (dom._kickBtns.length > 0) ? dom._kickBtns : false;
	},
};
var tournamentRegister = {
	_processing: false,
	init: function(){
		tournamentRegister.setRandBtn();
		// Event du cas où le tournoi est à l'inscription random
		if(isElSoloJqueryInstance(tournamentRegister.getRandBtn())){
			tournamentRegister.loadRandRegisterEvent();
			return;
		}

		tournamentRegister.setChoiceBtn();
		if(isElSoloJqueryInstance(tournamentRegister.getChoiceBtn())){
			dom.getBtnsTeam().each(function(index, el) {
				tournamentRegister.loadJoinTeamEvent($(el));
			});
			tournamentRegister.loadChooseTeamBtnEvent();
			return;
		}

	},
	setChoiceBtn: function(){
		tournamentRegister._choiceBtn = $('.detailtournoi-btn-inscription-choisie');
	},
	setRandBtn: function(){
		tournamentRegister._randBtn = $('.detailtournoi-btn-inscription');
	},
	getChoiceBtn: function(){return tournamentRegister._choiceBtn;},
	getRandBtn: function(){return tournamentRegister._randBtn;},
	loadRandRegisterCallback: function(obj){
		tournamentRegister._processing = false;
		if(obj != false){
			if(obj.errors){
				popupError.init(obj.errors);

				return;
			}
			popup.init('Vous avez été inscrit aléatoirement à une équipe');
			setTimeout(function(){
				location.reload();
			}, 1000);
		}
	},
	loadRandRegisterEvent: function(){
		tournamentRegister.getRandBtn().click(function(event){
			if(tournamentRegister._processing)
				return;
			tournamentRegister._processing = true;
			ajaxWithDataRequest(
				'tournoi/randRegister', 
				'POST', 
				{
					t: dom.getTget(),
					sJeton: dom.getSjeton().val()
				}, 
				tournamentRegister.loadRandRegisterCallback
			);
		});
	},
	loadChooseTeamBtnEvent: function(){
		scroll.init(tournamentRegister.getChoiceBtn(), dom.getEquipesLibresSection());
		tournamentRegister.getChoiceBtn().click(function(event){
			popup.init("Choisissez votre équipe");
			dom.getEquipesLibresSection().addClass('animation fade fadeRight');
			// setTimeout(function(){
			// 	dom.getEquipesLibresSection().removeClass('animation fade fadeRight');
			// }, 550);
			tournamentRegister.getChoiceBtn().off();
		});
	},
	loadJoinTeamEventCallback: function(obj){
		if(obj != false){
			if(obj.errors){
				popup.init(obj.errors);
				return;
			}
			if(obj.success){
				popup.init(obj.success);
				setTimeout(function(){
					location.reload();
				}, 1000);
				return;
			}			
		}
	},
	loadJoinTeamEvent: function(jQBtn){
		if(isElSoloJqueryInstance(jQBtn)){
			var _hInput = jQBtn.parent().find('.equipelibre-tt-id');
			var _teamId = parseInt(_hInput.val());
			_hInput.remove();
			jQBtn.click(function(e){
				ajaxWithDataRequest(
					'tournoi/teamRegister', 
					'POST', 
					{
						t: dom.getTget(),
						ttid: _teamId,
						sJeton: dom.getSjeton().val()
					},
					tournamentRegister.loadJoinTeamEventCallback
				);
			});
		}
	}
};
var tournamentUnregister = {
	_processing: false,
	init: function(){
		tournamentUnregister.setBtn();
		if(isElSoloJqueryInstance(tournamentUnregister.getBtn()))
			tournamentUnregister.loadEvent();
	},
	setBtn: function(){
		tournamentUnregister._btn = $('.detailtournoi-btn-desinscription');
	},
	getBtn: function(){
		return tournamentUnregister._btn;
	},
	loadEventCallback: function(obj){
		tournamentUnregister._processing = false;
		if(obj != false){
			if(obj.errors){
				popupError.init(obj.errors);
				return;
			}else{
				console.log(obj);
			}
			popup.init('Vous avez été déinscrit de ce tournoi');
			setTimeout(function(){
				location.reload();
			}, 1000);							
		}
	},
	loadEvent: function(){
		tournamentUnregister.getBtn().click(function(event){
			if(tournamentUnregister._processing)
				return;
			tournamentUnregister._processing = true;
			ajaxWithDataRequest(
				'tournoi/unregister', 
				'POST', 
				{
					t: dom.getTget(),
					sJeton: dom.getSjeton().val()
				},
				tournamentUnregister.loadEventCallback
			);
		});
	}
};
var createFirstMatchs = {
	_processing: false,
	init: function(){
		if(!dom.getPremiersMatchsBtn())
			return false;
		createFirstMatchs.launchFirstClickEvent();
	},
	generateValidationDom: function(){
		$('.createFirstMatchContainer').each(function() {
			$(this).remove();
		});
		var container = $('<div class="animation fadeDown full-width full-height display-flex-column fixed absolute-0-0 bg-purple createFirstMatchContainer"></div>');
		var msg = $('<h3 class="titre4  text-center">Une fois les premiers matchs créés, le tournoi sera verrouillé et plus personne ne pourra s\'inscrire. <br />Êtes-vous sûr de vouloir lancer la création des rencontres ?</h3>');
		var btnContainer = $('<div class="detailtournoi-creatematchs-btn-event-container full-width display-flex-row"></div>');
		var cancelBtn = $('<button id="detailtournoi-cancel-creationpremiersmatchs" class="relative btn btn-pink "><a>Annuler</a></button>');
		var validationBtn = $('<button id="detailtournoi-validation-creationpremiersmatchs" class="relative btn btn-pink "><a>Oui, lancer !</a></button>');

		container.append(msg);
		btnContainer.append(cancelBtn);
		btnContainer.append(validationBtn);
		container.append(btnContainer);
		$('body').append(container);

		createFirstMatchs.launchValidationEvent(container, validationBtn, true);
		createFirstMatchs.launchValidationEvent(container, cancelBtn, false);
	},
	launchFirstClickEvent: function(){
		dom.getPremiersMatchsBtn().click(function(e) {
			createFirstMatchs.generateValidationDom();
		});
	},
	launchValidationEvent: function(jQContainer, jQBtn, launch){
		jQBtn.click(function(e) {
			jQContainer.removeClass('fadeDown');
			jQContainer.addClass('fadeOutUp');
			setTimeout(function(){
				jQContainer.remove();
			}, 1000);
			// C'est donc la validation qui a été choisie
			if(launch){
				createFirstMatchs.sendCreationRequest();
			}
		});
	},
	sendCreationRequestCallback: function(obj){
		createFirstMatchs._processing = false;
		if(obj != false){
			if(obj.errors){
				popup.init(obj.errors);
				return;
			}
			if(obj.success){
				popup.init(obj.success);
				setTimeout(function(){
					location.reload();
				}, 1000);
				return;
			}
		}
	},
	sendCreationRequest: function(){
		if(createFirstMatchs._processing)
			return;
		createFirstMatchs._processing = true;
		ajaxWithDataRequest(
			'detailtournoi/createFirstMatchs', 
			'POST', 
			{
				t: dom.getTget(),
				sJeton: dom.getSjeton().val()
			},
			createFirstMatchs.sendCreationRequestCallback
		);
	}
};
var selectMatchWinner = {
	_processing: false,
	init: function(){
		if(dom.getMatchsWinnerBtns() != false)
			selectMatchWinner.associateEventToBtn();
	},
	associateEventToBtn: function(){
		dom.getMatchsWinnerBtns().each(function() {
			var mId = $(this).data('m');
			var ttId = $(this).data('tt');
			$(this).removeAttr('data-m');
			$(this).removeAttr('data-tt');
			$(this).click(function(){
				selectMatchWinner.btnClick($(this), mId, ttId);
			});
		});
	},
	btnClickCallback: function(obj){
		selectMatchWinner._processing = false;
		if(obj != false){
			if(obj.errors){
				popup.init(obj.errors);
				return;
			}
			if(obj.success){
				popup.init(obj.success);
				setTimeout(function(){
					location.reload();
				}, 1000);
				return;
			}
		}
	},
	btnClick: function(jQbtn, m, tt){
		if(selectMatchWinner._processing)
			return;
		selectMatchWinner._processing = true;
		ajaxWithDataRequest(
			'detailtournoi/selectWinner', 
			'POST', 
			{
				t: dom.getTget(),
				sJeton: dom.getSjeton().val(),
				mId: m,
				ttId: tt
			},
			selectMatchWinner.btnClickCallback
		);
	}
};
var createNextMatchs = {
	_processing: false,
	init: function(){
		if(!!dom.getCreerProchainsMatchsBtn())
			createNextMatchs.associateEventToBtn();
	},
	associateEventToBtn: function(){
		dom.getCreerProchainsMatchsBtn().click(function(e) {
			createNextMatchs.btnClicked();
		});
	},
	btnClickedCallback: function(obj){
		createNextMatchs._processing = false;
		if(obj != false){
			if(obj.errors){
				popup.init(obj.errors);
				return;
			}
			if(obj.success){
				popup.init(obj.success);
				setTimeout(function(){
					location.reload();
				}, 1000);
				return;
			}
		}
	},
	btnClicked: function(){
		if(createNextMatchs._processing)
			return;
		createNextMatchs._processing = true;
		ajaxWithDataRequest(
			'detailtournoi/createNextMatchs', 
			'POST', 
			{
				t: dom.getTget(),
				sJeton: dom.getSjeton().val()
			},
			createNextMatchs.btnClickedCallback
		);
	}
};
var kickPlayer = {
	_processing: false,
	init: function(){
		kickPlayer.associateEvents();
	},
	associateEvents: function(){
		dom.getKickParticipantsBtns().each(function() {
			var pseudo = $(this).data('pseudo');
			$(this).removeAttr('data-pseudo');
			$(this).click(function(event) {
				kickPlayer.sendEvent(pseudo);
			});
		});
	},
	sendEventCallback: function(obj){
		kickPlayer._processing = false;
		if(obj.success){
			popup.init(obj.success);
			setTimeout(function(){
				location.reload();
			}, 1000);
		}
		else if(obj.errors)
			popup.init(obj.errors);
	},
	sendEvent: function(_s){
		if(kickPlayer._processing)
			return;
		kickPlayer._processing = true;
		var toSendData = {
			t: dom.getTget(),
			sJeton: dom.getSjeton().val(),
			pseudo: _s
		};
		ajaxWithDataRequest("detailtournoi/kickUser", "POST", toSendData, kickPlayer.sendEventCallback);
	}
};
initAll.add(dom.init);