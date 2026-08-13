document.addEventListener('DOMContentLoaded', function () {
    var track = document.getElementById('ts-track');
    if (!track) return;

    var isDragging = false;
    var startX = 0;
    var scrollStart = 0;
    var moved = false;

    track.addEventListener('mousedown', function (e) {
        isDragging = true;
        moved = false;
        startX = e.pageX;
        scrollStart = track.scrollLeft;
        track.classList.add('is-dragging');
    });

    window.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        var delta = e.pageX - startX;
        if (Math.abs(delta) > 4) moved = true;
        track.scrollLeft = scrollStart - delta;
    });

    window.addEventListener('mouseup', function () {
        if (!isDragging) return;
        isDragging = false;
        track.classList.remove('is-dragging');
    });

    // Evita que un drag se interprete como click en un enlace/botón dentro de la card
    track.addEventListener('click', function (e) {
        if (moved) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);
});