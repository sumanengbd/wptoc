document.addEventListener('DOMContentLoaded', function() {
    var wptoc = document.querySelector('.wptoc');
    var togglerHeader = document.querySelector('.wptoc__header');
    var toggler = document.querySelector('.wptoc__toggler');
    var content = document.querySelector('.wptoc__content');

    if ( typeof(wptoc) !== 'undefined' && wptoc !== null  ) {
    	const dataMinus = wptoc.getAttribute('data-minus');
    	const showValueString = togglerHeader.getAttribute('data-show');
    	const showValueBoolean = showValueString === 'true';
    	var isOpen = showValueBoolean;

	    if (isOpen) {
	        content.style.maxHeight = content.scrollHeight + 'px';
	        content.style.opacity = '1';
	        content.style.visibility = 'visible';
	        togglerHeader.style.marginBottom = '0';
	    } else {
	        content.style.maxHeight = '0';
	        content.style.opacity = '0';
	        content.style.visibility = 'hidden';
	        togglerHeader.style.marginBottom = `-${dataMinus}px`;
	    }

	    togglerHeader.addEventListener('click', function() {
	        isOpen = !isOpen;

	        if (isOpen) {
	            content.style.maxHeight = content.scrollHeight + 'px';
	            content.style.opacity = '1';
	            content.style.visibility = 'visible';
	        	togglerHeader.style.marginBottom = '0';
	        } else {
	            content.style.maxHeight = '0';
	            content.style.opacity = '0';
	            content.style.visibility = 'hidden';
	        	togglerHeader.style.marginBottom = `-${dataMinus}px`;
	        }

	        wptoc.classList.toggle('wptoc__unfolded');
	        toggler.classList.toggle('wptoc__toggler--active');
	    });

		function smoothScroll(target) {
			const targetElement = document.querySelector(target);
			if (!targetElement) return;
			const targetPosition = targetElement.offsetTop;
			const startPosition = window.pageYOffset;
			const distance = targetPosition - startPosition;
			const duration = 500;
			let start = null;

			function step(timestamp) {
				if (!start) start = timestamp;
					const progress = timestamp - start;
					window.scrollTo(0, easeInOut(progress, startPosition, distance, duration));

				if (progress < duration) window.requestAnimationFrame(step);
			}

			function easeInOut(t, b, c, d) {
				t /= d / 2;
				if (t < 1) return c / 2 * t * t + b;
				t--;
				return -c / 2 * (t * (t - 2) - 1) + b;
			}

				window.requestAnimationFrame(step);
		}

		const links = document.querySelectorAll('.wptoc a[href^="#"]');
		links.forEach(link => {
			link.addEventListener('click', e => {
				e.preventDefault();
				smoothScroll(link.getAttribute('href'));
			});
		});

	}
});
