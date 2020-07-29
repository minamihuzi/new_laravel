var checkinApp = new Vue({
    el: '#app',
    data: {
        attendees: [],
        attendee_id: 0,
		attendee_photo_path: '',
		searchTerm: '',
        searchResultsCount: 0,
		searchResultsCountArrived: 0,
		rateArrived:0,
		hiddenTType:-1,
		freeTType:-1,
		normalTType:-1,
		suggestedTType:-1,
		couplePType:"",
		malePType:"",
		femalePType:"",
        showScannerModal: false,
		showCheckInType: false,
		workingAway: false,
        isInit: false,
        isScanning: false,
		sort_by: $('#sort_by')[0],
		sort_order: $('#sort_order')[0],
        videoElement: $('video#scannerVideo')[0],
        canvasElement: $('canvas#QrCanvas')[0],
        scannerDataUrl: '',
        QrTimeout: null,
        canvasContext: $('canvas#QrCanvas')[0].getContext('2d'),
        successBeep: new Audio('/mp3/beep.mp3'),
        scanResult: false,
		showCheckInItem: false,
		showWalkin: false,
		showConfirmBack: false,
		showConfirmPay: false,
		showConfirmPayCheckin: false,
		showConfirmClearCheckin: false,
		showConfirmSign:"block",
		showTicketType: false,
		showTakePhoto: false,
		showConfirmPassError:false,
		showConfirmClearPassError:false,
		ticketPerTypeElement:$('#ticket_per_type')[0],
		profilePerTypeElement:$('#profile_per_type')[0],
		firstNameElement: $('#first_name')[0],
		attendeeIdElement: $('#attendee_id')[0],
		lastNameElement: $('#last_name')[0],
		signConfirmModal:$('#showConfirmSignModal')[0],
		emailElement: $('#email')[0],
		businessNameElement:$('#business_name')[0],
		searchElement:$('#search_checkin')[0],
		valueTTypeElement:$('#valueTType')[0],
		checkInTTypeElement:$('#CheckInType')[0],
		type_is_hidden:$('#is_hidden')[0],
		type_is_free:$('#is_free')[0],
		type_is_normal:$('#is_normal')[0],
		type_is_couple:$('#is_couple')[0],
		type_is_male:$('#is_male')[0],
		type_is_female:$('#is_female')[0],
		type_is_suggested:$('#is_suggested_donation')[0],
		ticketPerTypeWalkElement:$('#ticket_per_type_walk')[0],
		profilePerTypeWalkElement:$('#profile_per_type_walk')[0],
		firstNameWalkElement: $('#first_name_walk')[0],
		lastNameWalkElement: $('#last_name_walk')[0],
		referenceElement: $('#reference')[0],
		coupleFirstWalkElement: $('#couple_first_walk')[0],
		coupleLastWalkElement: $('#couple_last_walk')[0],
		coupleFirstCheckinElement: $('#couple_first_checkin')[0],
		coupleLastCheckinElement: $('#couple_last_checkin')[0],
		attendeeIdWalkElement: $('#attendee_id_walk')[0],		
		eventIdWalkElement: $('#event_id_walk')[0],		
		emailWalkElement: $('#email_walk')[0],
		payWalkElement: $('#pay_walk')[0],
		payAmountElement: $('#pay_amount')[0],
		payCheckAmountElement: $('#amount')[0],
		businessNameWalkElement:$('#business_name_walk')[0],
		passcodeElement:$('#pass_code')[0],
		passcodePayElement:$('#pass_code_pay')[0],
		passcodeClearElement:$('#pass_code_clear_checkin')[0],
		passcodePayCheckinElement:$('#pass_code_pay_checkin')[0],
		dashboardElement:$('#dashboard-url')[0],
		ticketTypeKey:"",
		searchKey:"",
        scanResultObject: {}
    },

    created: function () {
        this.fetchAttendees()
    },

    ready: function () {
    },

    methods: {
        fetchAttendees: function () {
            
			
        },
        toggleCheckin: function (attendee) {
			
			this.firstNameElement.value = attendee.first_name;
			this.lastNameElement.value = attendee.last_name;
			this.emailElement.value = attendee.email;
			this.attendeeIdElement.value = attendee.id;
			this.ticketPerTypeElement.value = attendee.type;
			this.referenceElement.value = attendee.order_reference;
			this.payCheckAmountElement.value = attendee.amount;
			var business_name = attendee.business_name;
			if(attendee.type=="Couples"){
				this.showTicketType = true;
				if(attendee.second_name===undefined){
					this.coupleFirstCheckinElement.value = '';
				}else{
					this.coupleFirstCheckinElement.value = attendee.second_name;
				}
				if(attendee.second_last_name===undefined){
					this.coupleLastCheckinElement.value = '';
				}else{
					this.coupleLastCheckinElement.value = attendee.second_last_name;
				}
			}else{
				this.showTicketType = false;
			}	
			if(business_name===undefined){
				this.businessNameElement.value = '';
			}else{
				this.businessNameElement.value = business_name;
			}
			this.showCheckInItem = true;
						
        },
		showTakePhotoDialog: function (attendee) {
			this.attendee_id = attendee.id;
			this.attendee_photo_path = attendee.photo_path;
			this.showTakePhoto = true;						
        },
		setCheckInOut: function (attendee) {
			//showConfirmSign = true;
			if(attendee.has_arrived==2){				
				//return;
			}
            if(this.workingAway) {
                //return;
            }
            
			this.workingAway = true;
            var that = this;

            var checkinData = {
                checking: attendee.has_arrived ? 'out' : 'in',
                attendee_id: attendee.id,
            };
			var that = this;
            this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
                if (res.data.status == 'success' || res.data.status == 'error') {
                    if (res.data.status == 'error') {
                        alert(res.data.message);
                    }
                    attendee.has_arrived = res.data.checked == 'out' ? 2 : 1;
                    that.workingAway = false;	
					that.fetchAttendees();
                } else {
                    that.workingAway = false;
                }
            });			
        },
		setCheckin: function (clear_state) {

            if(this.workingAway) {
                return;
            }
            //this.workingAway = true;
            var that = this;
			var attendeeId = this.attendeeIdElement.value;
			var firstName = this.firstNameElement.value;
			var lastName = this.lastNameElement.value;
			var email = this.emailElement.value;
			var business_name = this.businessNameElement.value;
			var ticketType = this.ticketPerTypeElement.value;
			if(!this.checkInputStateCheckin()){
				return;
			}
			var secondName = "";
			var secondLastName = "";
			if(ticketType=="Couples"){
				secondName = this.coupleFirstCheckinElement.value;
				secondLastName = this.coupleLastCheckinElement.value;
			}
			var checkinData = {
				checking: 'out',
				attendee_id: attendeeId,
				attendee_first_name: firstName,
				attendee_last_name: lastName,
				attendee_second_name: secondName,
				attendee_second_last_name: secondLastName,
				attendee_email: email,
				attendee_business_name: business_name,				
				ticket_type:ticketType
			};
			if(clear_state==0){
				checkinData = {
					checking: 'in',
					attendee_id: attendeeId,
					attendee_first_name: firstName,
					attendee_last_name: lastName,
					attendee_email: email,
					attendee_business_name: business_name,
					ticket_type:ticketType
				};
			}
			this.$http.post(Attendize.checkInRouteSet, checkinData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					if (res.data.status == 'error') {
						alert(res.data.message);
					}
					var has_arrived = checkinData.checking == 'out' ? 1 : 0;
					that.workingAway = false;	
					that.fetchAttendees();
				} else {
					/* @todo handle error*/
					that.workingAway = false;
				}
				this.showCheckInItem = false;
			});
			
        },
		doConfirmClearCheckin: function (clear_state) {
			var that = this;
			var attendeeId = this.attendeeIdElement.value;
			var firstName = this.firstNameElement.value;
			var lastName = this.lastNameElement.value;
			var email = this.emailElement.value;
			var business_name = this.businessNameElement.value;
			var ticketType = this.ticketPerTypeElement.value;
			if(!this.checkInputStateCheckin()){
				return;
			}
			var pass_code = this.passcodeClearElement.value;	
			if(pass_code==null || pass_code==""){
				this.showConfirmClearPassError = true;
				this.passcodeClearElement.focus();
				return;
			}
			var secondName = "";
			var secondLastName = "";
			if(ticketType=="Couples"){
				secondName = this.coupleFirstCheckinElement.value;
				secondLastName = this.coupleLastCheckinElement.value;
			}
			var checkinData = {
				pass_require:'yes',
				passcode:pass_code,
				checking: 'out',
				attendee_id: attendeeId,
				attendee_first_name: firstName,
				attendee_last_name: lastName,
				attendee_second_name: secondName,
				attendee_second_last_name: secondLastName,
				attendee_email: email,
				attendee_business_name: business_name,				
				ticket_type:ticketType
			};
			if(clear_state==0){
				checkinData = {
					checking: 'in',
					attendee_id: attendeeId,
					attendee_first_name: firstName,
					attendee_last_name: lastName,
					attendee_email: email,
					attendee_business_name: business_name,
					ticket_type:ticketType
				};
			}
			this.$http.post(Attendize.checkInRouteSet, checkinData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					if (res.data.status == 'error') {
						this.showConfirmClearPassError = true;
						return;
					}
					var has_arrived = checkinData.checking == 'out' ? 1 : 0;
					that.workingAway = false;	
					that.fetchAttendees();
				} else {
					/* @todo handle error*/
					that.workingAway = false;
				}
				this.showCheckInItem = false;
				this.showConfirmClearCheckin = false;     
				this.showConfirmClearPassError = false;	
				this.passcodeClearElement.value = "";
			});
			
        },
		setWalkin: function (clear_state) {

            var that = this;
			var attendeeId = this.attendeeIdWalkElement.value;
			var firstName = this.firstNameWalkElement.value;
			var lastName = this.lastNameWalkElement.value;
			var email = this.emailWalkElement.value;
			var business_name = this.businessNameWalkElement.value;
			var ticketType = this.ticketPerTypeWalkElement.value;
			var profileType = this.profilePerTypeWalkElement.value;
			var payWalk = this.payWalkElement.value;
			var payAmount = this.payAmountElement.value;
			var eventId = this.eventIdWalkElement.value;
			if(!this.checkInputStateWalk()){
				return;
			}
			var secondName = "";
			var secondLastName = "";
			if(profileType=="Couples"){
				secondName = this.coupleFirstWalkElement.value;
				secondLastName = this.coupleLastWalkElement.value;
			}
			var checkinData = {
				attendee_id: 0,
				eventId: eventId,
				attendee_first_name: firstName,
				attendee_second_name: secondName,
				attendee_second_last_name: secondLastName,
				attendee_last_name: lastName,
				attendee_email: email,
				attendee_amount: payAmount,
				attendee_business_name: business_name,
				ticket_type:ticketType,
				pay_walk:payWalk,
				profile_type:profileType
			};
			this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					that.fetchAttendees();
				} else {
					console.log(res);
				}
				this.showWalkin = false;
			});
			
        },
		emailWalkin: function () {

            var that = this;
			var attendeeId = this.attendeeIdWalkElement.value;
			var firstName = this.firstNameWalkElement.value;
			var lastName = this.lastNameWalkElement.value;
			var email = this.emailWalkElement.value;
			var business_name = this.businessNameWalkElement.value;
			var ticketType = this.ticketPerTypeWalkElement.value;
			var profileType = this.profilePerTypeWalkElement.value;
			var payWalk = this.payWalkElement.value;
			var payAmount = this.payAmountElement.value;
			var eventId = this.eventIdWalkElement.value;
			if(!this.checkInputStateWalk()){
				return;
			}
			var secondName = "";
			var secondLastName = "";
			if(profileType=="Couples"){
				secondName = this.coupleFirstWalkElement.value;
				secondLastName = this.coupleLastWalkElement.value;
			}
			var checkinData = {
				attendee_id: -1,
				eventId: eventId,
				attendee_first_name: firstName,
				attendee_second_name: secondName,
				attendee_second_last_name: secondLastName,
				attendee_last_name: lastName,
				attendee_email: email,
				attendee_amount: payAmount,
				attendee_business_name: business_name,
				ticket_type:ticketType,
				pay_walk:payWalk,
				profile_type:profileType
			};
			this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					that.fetchAttendees();
				} else {
					console.log(res);
				}
				this.showWalkin = false;
			});
			
        },
		printWalkin: function () {

            var that = this;
			var attendeeId = this.attendeeIdWalkElement.value;
			var firstName = this.firstNameWalkElement.value;
			var lastName = this.lastNameWalkElement.value;
			var email = this.emailWalkElement.value;
			var business_name = this.businessNameWalkElement.value;
			var ticketType = this.ticketPerTypeWalkElement.value;
			var profileType = this.profilePerTypeWalkElement.value;
			var payWalk = this.payWalkElement.value;
			var payAmount = this.payAmountElement.value;
			if(!this.checkInputStateWalk()){
				return;
			}
			var secondName = "";
			var secondLastName = "";
			if(profileType=="Couples"){
				secondName = this.coupleFirstWalkElement.value;
				secondLastName = this.coupleLastWalkElement.value;
			}
			var eventId = this.eventIdWalkElement.value;
			var checkinData = {
				attendee_id: -1,
				eventId: eventId,
				attendee_first_name: firstName,
				attendee_last_name: lastName,
				attendee_second_name: secondName,
				attendee_second_last_name: secondLastName,				
				attendee_email: email,
				attendee_amount: payAmount,
				attendee_business_name: business_name,
				ticket_type:ticketType,
				pay_walk:payWalk,
				profile_type:profileType
			};
			this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					that.fetchAttendees();
				} else {
					console.log(res);
				}
				this.showWalkin = false;
			});
			
        },
		checkInputStateCheckin:function(){
			var firstName = this.firstNameElement.value;
			var lastName = this.lastNameElement.value;
			var email = this.emailElement.value;
			var business_name = this.businessNameElement.value;
			var ticketType = this.ticketPerTypeElement.value;
			var payCheckAmount = this.payCheckAmountElement.value;
			if(ticketType==null || ticketType==""){
				//alert("Please select ticket type.")
				this.ticketPerTypeElement.focus();
				return false;
			}
			if(firstName==null || firstName==""){
				//alert("Please input first name.")
				this.firstNameElement.focus();
				return false;
			}			
			if(lastName==null || lastName==""){
				//alert("Please input last name.")
				this.lastNameElement.focus();
				return false;
			}
			if(ticketType=="Couples"){
				var coupleFirstName = this.coupleFirstCheckinElement.value;
				var coupleLastName = this.coupleLastCheckinElement.value;
				if(coupleFirstName==null || coupleFirstName==""){
					//alert("Please input couple's first name.")
					this.coupleFirstCheckinElement.focus();
					return false;
				}
				if(coupleLastName==null || coupleLastName==""){
					//alert("Please input couple's last name.")
					this.coupleLastCheckinElement.focus();
					return false;
				}
			}
			if(business_name==null || business_name==""){
				//alert("Please input user name.")
				this.businessNameElement.focus();
				return false;
			}
			if(email==null || email==""){
				//alert("Please input email.")
				this.emailElement.focus();
				return false;
			}
			if (this.validateEmail(email)) {
			}else{
				//alert("Please input valide email.")
				this.emailElement.focus();
				return false;
			}	
			if(payCheckAmount==null || payCheckAmount=="" || !this.checkDecimal(this.payCheckAmountElement)){
				//alert("Please input valid pay amount.")
				this.payCheckAmountElement.focus();
				return false;
			}
			return true;
		},
		checkInputStateWalk:function(){
			var firstName = this.firstNameWalkElement.value;
			var lastName = this.lastNameWalkElement.value;
			var email = this.emailWalkElement.value;
			var business_name = this.businessNameWalkElement.value;
			var ticketType = this.ticketPerTypeWalkElement.value;
			var profileType = this.profilePerTypeWalkElement.value;
			var payWalk = this.payWalkElement.value;
			var payAmount = this.payAmountElement.value;
			if(ticketType==null || ticketType==""){
				//alert("Please select ticket type.")
				this.ticketPerTypeWalkElement.focus();
				return false;
			}
			if(profileType==null || profileType==""){
				//alert("Please select ticket type.")
				this.profilePerTypeWalkElement.focus();
				return false;
			}
			if(firstName==null || firstName==""){
				//alert("Please input first name.")
				this.firstNameWalkElement.focus();
				return false;
			}
			if(profileType=="Couples"){
				var coupleFirstName = this.coupleFirstWalkElement.value;
				var coupleLastName = this.coupleLastWalkElement.value;
				if(coupleFirstName==null || coupleFirstName==""){
					//alert("Please input couple's first name.")
					this.coupleFirstWalkElement.focus();
					return false;
				}
				if(coupleLastName==null || coupleLastName==""){
					//alert("Please input couple's last name.")
					this.coupleLastWalkElement.focus();
					return false;
				}
			}
			if(lastName==null || lastName==""){
				//alert("Please input last name.")
				this.lastNameWalkElement.focus();
				return false;
			}
			
			if(business_name==null || business_name==""){
				//alert("Please input user name.")
				this.businessNameWalkElement.focus();
				return false;
			}
			if(email==null || email==""){
				//alert("Please input email.")
				this.emailWalkElement.focus();
				return false;
			}
			if (this.validateEmail(email)) {
			}else{
				//alert("Please input valide email.")
				this.emailWalkElement.focus();
				return false;
			}
			if(payWalk==null || payWalk==""){
				//alert("Please select pay type.")
				this.payWalkElement.focus();
				return false;
			}
			if(payAmount==null || payAmount=="" || !this.checkDecimal(this.payAmountElement)){
				//alert("Please input valid pay amount.")
				this.payAmountElement.focus();
				return false;
			}
			return true;
		},
		searchCheckin: function () {
            var search_key = this.searchElement.value;	
			this.searchTerm = search_key;
			var sort_by = this.sort_by.value;
			var sort_order = this.sort_order.value;
			this.$http.post(Attendize.checkInSearchRoute, {q: this.searchTerm,sort_by: sort_by,sort_order: sort_order}).then(function (res) {
                this.attendees = res.data;
                this.searchResultsCount = (Object.keys(res.data).length);
				var arrivedCnt = 0;
				this.attendees.forEach(function (arrayItem) {					
					if(arrayItem.has_arrived==1){
						arrivedCnt = arrivedCnt+1;
					}					
				});
				this.searchResultsCountArrived = arrivedCnt;				
				this.rateArrived = Number((Number((this.searchResultsCountArrived/this.searchResultsCount).toFixed(2))*100).toFixed(0));				
            }, function () {
                console.log('Failed to fetch attendees')
            });			
        },
		onKeyPress(event) {
			this.searchCheckin();
		},		
        clearSearch: function () {
            this.searchTerm = '';
            this.fetchAttendees();
			
        },
		doConfirmBack: function () {
			var pass_code = this.passcodeElement.value;	
			if(pass_code==null || pass_code==""){
				this.showConfirmPassError = true;
				this.passcodeElement.focus();
				//alert("Please input a pass code.")
				return;
			}
			passData = {
				passcode:pass_code
			};			
			var that = this;
			this.$http.post(Attendize.dashboardCheckInRoute, passData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					if (res.data.status == 'error') {
						//alert(res.data.message);
						this.showConfirmPassError = true;
						return;
					}else{
						var url = that.dashboardElement.value;
						this.showConfirmPassError = false;
						window.open(url+"?stop", "_self");
					}					
				} else {
					
				}
			});
		},
		doConfirmPay: function () {			
			var pass_code = this.passcodePayElement.value;	
			if(pass_code==null || pass_code==""){
				this.showConfirmPassError = true;
				this.passcodePayElement.focus();
				return;
			}
			passData = {
				passcode:pass_code
			};
			this.showConfirmPassError = false;
			var that = this;
			var attendeeId = this.attendeeIdWalkElement.value;
			var firstName = this.firstNameWalkElement.value;
			var lastName = this.lastNameWalkElement.value;
			var email = this.emailWalkElement.value;
			var business_name = this.businessNameWalkElement.value;
			var ticketType = this.ticketPerTypeWalkElement.value;
			var profileType = this.profilePerTypeWalkElement.value;
			var payWalk = this.payWalkElement.value;
			var payAmount = this.payAmountElement.value;
			if(!this.checkInputStateWalk()){
				return;
			}
			var secondName = "";
			var secondLastName = "";
			if(ticketType=="Couples"){
				secondName = this.coupleFirstWalkElement.value;
				secondLastName = this.coupleLastWalkElement.value;
			}
			var eventId = this.eventIdWalkElement.value;
			var checkinData = {
				passcode:pass_code,
				attendee_id: -2,
				eventId: eventId,
				attendee_first_name: firstName,
				attendee_last_name: lastName,
				attendee_second_name: secondName,
				attendee_second_last_name: secondLastName,				
				attendee_email: email,
				attendee_amount: payAmount,
				attendee_business_name: business_name,
				ticket_type:ticketType,
				pay_walk:payWalk,
				profile_type:profileType
			};
			this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					if (res.data.status == 'error') {
						//alert(res.data.message);
						this.showConfirmPassError = true;
						return;
					}else{
						that.fetchAttendees();
						this.showConfirmPassError = false;
					}
				} else {
					console.log(res);
				}
				this.showConfirmPay = false;   
				this.passcodePayElement.value="";				
				this.showWalkin = false;
			});
		},
		doConfirmPayCheckin: function () {			
			var pass_code = this.passcodePayCheckinElement.value;	
			if(pass_code==null || pass_code==""){
				this.showConfirmPassError = true;
				this.passcodePayCheckinElement.focus();
				return;
			}
			passData = {
				passcode:pass_code
			};
			var that = this;
			var attendeeId = this.attendeeIdElement.value;
			var firstName = this.firstNameElement.value;
			var lastName = this.lastNameElement.value;
			var email = this.emailElement.value;
			var business_name = this.businessNameElement.value;
			var ticketType = this.ticketPerTypeElement.value;
			if(!this.checkInputStateCheckin()){
				return;
			}
			var secondName = "";
			var secondLastName = "";
			if(ticketType=="Couples"){
				secondName = this.coupleFirstCheckinElement.value;
				secondLastName = this.coupleLastCheckinElement.value;
			}
			var checkinData = {
				passcode:pass_code,
				checking: 'received',
				attendee_id: attendeeId,
				attendee_first_name: firstName,
				attendee_last_name: lastName,
				attendee_second_name: secondName,
				attendee_second_last_name: secondLastName,
				attendee_email: email,
				attendee_business_name: business_name,
				ticket_type:ticketType
			};
			this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
				if (res.data.status == 'success' || res.data.status == 'error') {
					if (res.data.status == 'error') {
						this.showConfirmPassError = true;
						return;
					}else{
						that.fetchAttendees();
						this.showConfirmPassError = false;
					}
					var has_arrived = 2;
					that.workingAway = false;	
					
				} else {
					/* @todo handle error*/
					that.workingAway = false;
				}
				this.showCheckInItem = false;
				this.showConfirmPayCheckin = false;   
				this.passcodePayCheckinElement.value="";
			});
		},
		setTType: function (ttype) {
			var valttype1 = "";
			var valttype2 = "";
			var valttype3 = "";
			var valttype4 = "";
			var valttype = "";
			if(this.type_is_hidden.checked){
				this.hiddenTType = 1;
				valttype1 = "hidden";
				valttype = "hidden";
			}else{
				this.hiddenTType = -1;
				valttype1 = "";
			}
	
			if(this.type_is_normal.checked){
				this.normalTType = 2;
				valttype2 = "normal";
				if(valttype==""){
					valttype = "normal";
				}else{
					valttype = valttype+",normal";
				}
			}else{
				this.normalTType = -1;
				valttype2 = "";
			}					
		
			if(this.type_is_free.checked){
				this.freeTType = 3;
				valttype3 = "free";
				if(valttype==""){
					valttype = "free";
				}else{
					valttype = valttype+",free";
				}
			}else{
				this.freeTType = -1;
				valttype3 = "";
			}
		
		
			if(this.type_is_suggested.checked){
				this.suggestedTType = 4;
				valttype4 = "suggested donation";
				if(valttype==""){
					valttype = "suggested donation";
				}else{
					valttype = valttype+",suggested donation";
				}
			}else{
				this.suggestedTType = -1;
				valttype4= "";
			}			
			
			
			if(this.type_is_couple.checked){
				this.couplePType = "Couples";
				if(valttype==""){
					valttype = "Couples";
				}else{
					valttype = valttype+",Couples";
				}
			}else{
				this.couplePType = "";
			}
			
			if(this.type_is_male.checked){
				this.malePType = "Single Male";
				if(valttype==""){
					valttype = "Single Male";
				}else{
					valttype = valttype+",Single Male";
				}
			}else{
				this.malePType = "";
			}
			
			if(this.type_is_female.checked){
				this.femalePType = "Single Female";
				if(valttype==""){
					valttype = "Single Female";
				}else{
					valttype = valttype+",Single Female";
				}
			}else{
				this.femalePType = "";
			}
			if(valttype=="")
				this.valueTTypeElement.innerHTML="Registration Type Filter";
			else
				this.valueTTypeElement.innerHTML=valttype;
            //this.fetchAttendees();
        },
		searchTType: function () {
            var  search_key = this.searchElement.value;
			var that = this;
			this.$http.post(Attendize.checkInSearchRoute, {q: search_key}).then(function (res) {
                this.attendees = res.data;
                var search_attendees = [];
				
				var arrivedCnt = 0;
				this.attendees.forEach(function (arrayItem) {					
					if((!(that.type_is_normal.checked || that.type_is_free.checked || that.type_is_suggested.checked) || arrayItem.is_hidden==that.normalTType || arrayItem.is_hidden==that.freeTType || arrayItem.is_hidden==that.suggestedTType) && (!(that.type_is_couple.checked || that.type_is_male.checked || that.type_is_female.checked) || arrayItem.type==that.couplePType || arrayItem.type==that.malePType || arrayItem.type==that.femalePType)){
						search_attendees.push(arrayItem);
						if(arrayItem.has_arrived==1){
							arrivedCnt = arrivedCnt+1;						
						}					
					}
				});
				this.searchResultsCount = (Object.keys(search_attendees).length);
				this.attendees = search_attendees;
				this.searchResultsCountArrived = arrivedCnt;				
				this.rateArrived = Number((Number((this.searchResultsCountArrived/this.searchResultsCount).toFixed(2))*100).toFixed(0));				
				this.showCheckInType = false;       
            }, function () {
                console.log('Failed to fetch attendees')
            });			
        },
		clearTType: function () {
            this.valueTTypeElement.innerHTML="Registration Type Filter";
			this.hiddenTType = -1;
			this.freeTType = -1;
			this.normalTType = -1;
			this.suggestedTType = -1;
			this.couplePType = "";
			this.malePType = "";
			this.femalePType = "";
			this.type_is_hidden.checked = false;
			this.type_is_free.checked = false;
			this.type_is_normal.checked = false;
			this.type_is_suggested.checked = false;
			this.type_is_couple.checked = false;
			this.type_is_male.checked = false;
			this.type_is_female.checked = false;
            this.fetchAttendees();
			this.showCheckInType = false;       
        },
        /* QR Scanner Methods */

        QrCheckin: function (attendeeReferenceCode) {

            this.isScanning = false;

            this.$http.post(Attendize.qrcodeCheckInRoute, {attendee_reference: attendeeReferenceCode}).then(function (res) {
                this.successBeep.play();
                this.scanResult = true;
                this.scanResultObject = res.data;

            }, function (response) {
                this.scanResultObject.message = lang("whoops2");
            });
        },

        showQrModal: function () {
            this.showScannerModal = true;
            this.initScanner();
        },
		showTypeModal: function () {
            this.showCheckInType = true;            
        },
		showWalkinModal: function () {
            this.showWalkin = true;            
        },
		showConfirmBackModal: function () {
            this.showConfirmBack = true;            
        },
		showConfirmPayModal: function (py_type) {
            if(py_type==1){				
				if(this.checkInputStateCheckin()){
					this.showConfirmPayCheckin = true;            
				}				
			}else{
				if(this.checkInputStateWalk()){
					this.showConfirmPay = true;            
				}
			}
        },
		showConfirmClearModal: function (py_type) {
			this.showConfirmClearCheckin = true;   
		},
		setCoupleName: function (event) {
            var ticketType = event.target.value;
			if(ticketType=="Couples"){
				this.showTicketType = true;
			}else{
				this.showTicketType = false;
			}		
        },
        initScanner: function () {

            var that = this;
            this.isScanning = true;
            this.scanResult = false;

            /*
             If the scanner is already initiated clear it and start over.
             */
            if (this.isInit) {
                clearTimeout(this.QrTimeout);
                this.QrTimeout = setTimeout(function () {
                    that.captureQrToCanvas();
                }, 500);
                return;
            }

            qrcode.callback = this.QrCheckin;

            // FIX SAFARI CAMERA
            if (navigator.mediaDevices === undefined) {
                navigator.mediaDevices = {};
            }

            if (navigator.mediaDevices.getUserMedia === undefined) {
                navigator.mediaDevices.getUserMedia = function(constraints) {
                    var getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

                    if (!getUserMedia) {
                        return Promise.reject(new Error('getUserMedia is not implemented in this browser'));
                    }

                    return new Promise(function(resolve, reject) {
                        getUserMedia.call(navigator, constraints, resolve, reject);
                    });
                }
            }

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                },
                audio: false
            }).then(function(stream) {

                that.stream = stream;

                if (that.videoElement.mozSrcObject !== undefined) { // works on firefox now
                    that.videoElement.mozSrcObject = stream;
                } else if(window.URL) { // and chrome, but must use https
                    that.videoElement.srcObject = stream;
                };

            }).catch(function(err) {
                console.log(err.name + ": " + err.message);
                alert(lang("checkin_init_error"));
            });

            this.isInit = true;
            this.QrTimeout = setTimeout(function () {
                that.captureQrToCanvas();
            }, 500);

        },		
        /**
         * Takes stills from the video stream and sends them to the canvas so
         * they can be analysed for QR codes.
         */
        captureQrToCanvas: function () {

            if (!this.isInit) {
                return;
            }

            this.canvasContext.clearRect(0, 0, 600, 300);

            try {
                this.canvasContext.drawImage(this.videoElement, 0, 0);
                try {
                    qrcode.decode();
                }
                catch (e) {
                    console.log(e);
                    this.QrTimeout = setTimeout(this.captureQrToCanvas, 500);
                }
            }
            catch (e) {
                console.log(e);
                this.QrTimeout = setTimeout(this.captureQrToCanvas, 500);
            }
        },
        closeScanner: function () {
            clearTimeout(this.QrTimeout);
            this.showScannerModal = false;
            track = this.stream.getTracks()[0];
            track.stop();
            this.isInit = false;
            this.fetchAttendees();
        },
		closeCheckInItem: function () {
            this.showCheckInItem = false;
            this.isInit = false;
        },
		closeWalkin: function () {
            this.showWalkin = false;
            this.isInit = false;
        },
		closeConfirmBackModal: function () {
            this.showConfirmBack = false;
			this.showConfirmPassError = false;			
            this.isInit = false;
        },
		closeConfirmPayModal: function () {
            this.showConfirmPay = false; 
			this.showConfirmPassError = false;			
            this.isInit = false;
        },
		closeConfirmSignModal: function () {
            this.showConfirmSign = "none"; 
			//this.signConfirmModal.addClass("hidden");
            this.isInit = false;
        },
		closeConfirmPayCheckinModal: function () {
            this.showConfirmPayCheckin = false;     
			this.showConfirmPassError = false;
            this.isInit = false;
        },
		closeConfirmClearCheckinModal: function () {
            this.showConfirmClearCheckin = false;     
			this.showConfirmClearPassError = false;
            this.isInit = false;
        },
		closeCheckInType: function () {
            this.showCheckInType = false;			
            this.isInit = false;
        },
		closeShowTakePhotoModal: function () {
            this.showTakePhoto = false;		
			this.fetchAttendees();			
            this.isInit = false;
        },
		validateEmail: function(email) {
			const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(String(email).toLowerCase());
		},
		checkDecimal: function(inputtxt) { 
			var decimal=  /^[-+]?[0-9]+\.[0-9]+$/; 
			if(inputtxt.value.match(decimal)) 
			{ 
				return true;
			}
			else
			{ 
				return false;
			}
		} 
	}
});
