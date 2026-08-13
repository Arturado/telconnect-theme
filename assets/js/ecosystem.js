document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.eco-tab');
    var title = document.querySelector('.eco-glass-title');
    var desc = document.querySelector('.eco-glass-desc');
    var photo = document.querySelector('.eco-main-photo');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            if ( title ) title.textContent = tab.getAttribute('data-label');
            if ( desc ) desc.textContent = tab.getAttribute('data-desc');
            if ( photo ) {
                photo.src = tab.getAttribute('data-image');
                photo.alt = tab.getAttribute('data-label');
            }
        });
    });
});