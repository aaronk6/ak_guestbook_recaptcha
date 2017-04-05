(function(exports) {
	'use strict';

	var onSubmit = function(token) {
		var form, tokenField;

		form = document.querySelector(".tx-veguestbook-pi1 form");
		tokenField = document.createElement('input');

		tokenField.setAttribute('name', 'tx_veguestbook_pi1[token]');
		tokenField.setAttribute('type', 'hidden');
		tokenField.setAttribute('value', token);

		form.appendChild(tokenField);
		form.submit();
	};

	var onloadCallback = function() {
		exports.grecaptcha.render(document.querySelector('.tx-guestbook-submit'), {
			'sitekey': exports.RECAPTCHA_SITE_KEY,
			'callback': onSubmit
		});
	};

	exports.ak_guestbook_recaptcha_onload = onloadCallback;

}(window));
