(function ($) {
    $(".wptoc-select2").select2({
        width: '100%',
        allowClear: true,
        minimumResultsForSearch: Infinity,
    });

    $(document).ready(function(){
        $('.wptoc_color_picker').wpColorPicker();
    });

    // Form Submit
    const submitButton = document.getElementById('wptoc-form-submit-button');
    const form = document.getElementById('wptoc-form');

    if ( typeof(form) !== 'undefined' && form !== null ) {
        submitButton.addEventListener('click', () => {
            HTMLFormElement.prototype.submit.call(form);
        });
    }

    // Tab JavaScript code
    var tabNavLinks = document.querySelectorAll('.wptoc-tab__nav a');
    var currentTab = localStorage.getItem('currentTab');
    
    for (var i = 0; i < tabNavLinks.length; i++) {
        tabNavLinks[i].addEventListener('click', function (e) {
            e.preventDefault();
            var currentTabNavLink = this;
            var currentTabNavParent = currentTabNavLink.parentNode;
            currentTabNavParent.classList.add('active');
            var tabNavSiblings = currentTabNavParent.parentNode.children;
            for (var j = 0; j < tabNavSiblings.length; j++) {
                if (tabNavSiblings[j] !== currentTabNavParent) {
                    tabNavSiblings[j].classList.remove('active');
                }
            }

            var targetId = currentTabNavLink.getAttribute('href');
            localStorage.setItem('currentTab', targetId);
            var tabContents = document.querySelectorAll('.wptoc-tab__content');

            for (var k = 0; k < tabContents.length; k++) {
                if (tabContents[k].id !== targetId.substring(1)) {
                    tabContents[k].style.display = 'none';
                } else {
                    tabContents[k].style.display = 'block';
                }
            }
        });
    }

    if ( typeof(form) !== 'undefined' && form !== null ) {
        if ( currentTab ) {
            document.querySelector('.wptoc-tab__nav a[href="' + currentTab + '"]').click();
        } else {
            document.querySelector('.wptoc-tab__nav a:first-child').click();
        }
    }

}(jQuery));