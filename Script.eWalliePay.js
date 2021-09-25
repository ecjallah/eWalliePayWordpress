/*!
 * Purpose: this simple framework manages all eWallie Pay AJAX Requests
 * Version Release: 1.0
 * Created Date: September 24, 2021
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
 * Dependency: jQuery 
*/

function Alert(type, msg, boldContent = null) {
    this.alertContainer = $('.eWalliePay-form').find('.eAlert-container');
    console.log(alertContainer);
	this.alertType = type;
	this.strongContent = (boldContent == null) ? '' : boldContent;
	this.alertMessage = msg;
	this.dismissable = `<button type="button" class="btn text-${this.alertType} close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true" style="font-size: 1.5em;">&times;</span>
						</button>`;

	this.alertContainer.html(/*html*/`
		<div style="width: 100%">
			<div class="row alert-container">
				<div style="width: 100%; display: flex;" class="alert alert-${this.alertType} alert-dismissable fade show" role="alert">
					<div style="flex: 1; font-size: 1em !important;">
						<strong>${this.strongContent}</strong>
						${this.alertMessage}
					</div>
				</div>                        
			</div>
		</div>`
	).hide(0).slideDown(250);
}

function HideAlert(){
    $('.eWalliePay-form').find('.eAlert-container').slideUp(250, function(){
        $(this).html('');
    });
}

function ChangeButtonState(button, text="Processing")
{
    let changedButton = $(button);
    let changedButtonOldText = changedButton.html();
    changedButton.attr('data-oldtext', changedButtonOldText);
    changedButton.attr('disabled', 'disabled').html(`<span>${text}&nbsp;<i class="fad fa-md fa-spinner-third fa-spin"></i></span>`);
}

function RestoreButtonState(button)
{
    let changedButton = $(button);
    let oldText       = changedButton.attr('data-oldtext');

    changedButton.removeAttr('disabled').html(oldText);
}

function isJSON(data){
    if (typeof data !== "string") {
        return false;
    }

    try {
        data = JSON.parse(data);
    } catch (e) {
        return false;
    }

    if (typeof data === "object" && data !== null) {
        return true;
    }

    return false;
}

jQuery(function($){
    
    $('body').on('keyup', ' #ewallie-user-identity', function(){
        let confirmBtn = $('body').find('.eWalliePay-form #confirm-ewallie-id')[0];
        let apprInput  = $('body').find('.eWalliePay-form .code-container');
        let curlInput  = $('body').find('.eWalliePay-form #ewallie-curl');
        let value      = this.value.trim();
        
        if (this.dataset.current !== value) {
            apprInput.slideUp(250, function(){
                this.apprInput = '';
                this.removeAttribute('required');
            });

            curlInput.val('');
            curlInput.removeAttr('required');
        }
        
        if (value != '') {
            if (confirmBtn.style.display === 'none') {
                $(confirmBtn).show(250);
            }
        }
        else{
            $(confirmBtn).hide(250);
        }
        this.setAttribute('data-current', value);
    });

    $('body').on('click', '.eWalliePay-form #confirm-ewallie-id', function(){
        let idInput = $('body').find('.eWalliePay-form #ewallie-user-identity');
        let value   = idInput.val().trim();
        HideAlert();
        if (value != '') {
            $.ajax({
                type: "POST",
                url: "/wp-content/plugins/eWalliePay/api",
                data: JSON.stringify({
                    "create-order": {
                        'key_x'         : eWallieKeys.keyX,
                        'key_y'         : eWallieKeys.keyY,
                        'key_z'         : eWallieKeys.keyZ,
                        'user_identity' : value,
                        'amount'        : eWallieKeys.total,
                        'currency'      : eWallieKeys.currency
                    },
                }),
                contentType: 'application/json',
                beforeSend: ()=>{
                    ChangeButtonState(this);
                }
            }).done(response=>{
                RestoreButtonState(this);
                if (response.status == 200) {
                    let codeContainer = document.querySelector('.eWalliePay-form .code-container');
                    let apprInput     = document.querySelector('.eWalliePay-form #ewallie-approval-code');
                    let curlInput     = document.querySelector('.eWalliePay-form #ewallie-curl');
                    curlInput.value   = response.response_body;
                    $(codeContainer).slideDown(250, ()=>{
                        $(this).slideUp(250);
                        apprInput.setAttribute('required', 'required');
                    });
                }
                else{
                    Alert('danger', response.message);
                }
            });
        }
        else{
            Alert('danger', "No eWallie Username/User ID provided");
        }
    });
});