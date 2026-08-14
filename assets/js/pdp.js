document.addEventListener('DOMContentLoaded', function () {
    var steppers = document.querySelectorAll('.pdp-qty-stepper');

    steppers.forEach(function (stepper) {
        var input = stepper.querySelector('input.qty');
        var minusBtn = stepper.querySelector('.pdp-qty-minus');
        var plusBtn = stepper.querySelector('.pdp-qty-plus');

        if (!input || !minusBtn || !plusBtn) return;

        function getStep() {
            return parseInt(input.getAttribute('step'), 10) || 1;
        }

        function getMin() {
            var min = input.getAttribute('min');
            return min !== null ? parseInt(min, 10) : 1;
        }

        function getMax() {
            var max = input.getAttribute('max');
            return max !== null && max !== '' ? parseInt(max, 10) : Infinity;
        }

        minusBtn.addEventListener('click', function () {
            var value = parseInt(input.value, 10) || getMin();
            var newValue = Math.max(getMin(), value - getStep());
            input.value = newValue;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        plusBtn.addEventListener('click', function () {
            var value = parseInt(input.value, 10) || getMin();
            var newValue = Math.min(getMax(), value + getStep());
            input.value = newValue;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
});