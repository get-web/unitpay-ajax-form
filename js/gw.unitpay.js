document.addEventListener("DOMContentLoaded", function () {

    let emptyElement = document.createElement("div");

    let unitpayForm = document.querySelector('[data-unitpay="ajax-form"]');
    if (typeof unitpayForm == 'undefined') return console.log('form undefined');

    let fieldCount = unitpayForm.querySelector('[data-unitpay="count"]');
    let fieldPrice = unitpayForm.querySelector('[data-unitpay="price"]');
    let fieldCurrency = unitpayForm.querySelector('[data-unitpay="currency"]');

    let resultSubtotal = unitpayForm.querySelector('[data-unitpay-result="subtotal"]') ||
        emptyElement;
    let resultCurrency = unitpayForm.querySelector('[data-unitpay-result="currency"]') ||
        emptyElement;

    let errorsBox = unitpayForm.querySelector('[data-unitpay-errors]') ||
        emptyElement;

    if (!!fieldCount) {

        fieldCount.addEventListener('keypress', (event) => {
            if (event.which < 48 || event.which > 57) event.preventDefault();
        });

        fieldCount.addEventListener("input", function (event) {

            let subtotal = GWU_GetSubtotal(fieldCount.value, fieldPrice.value);
            if (isNaN(subtotal) || subtotal < 0) {
                subtotal = 0;
            } else if (subtotal > 50000) {
                subtotal = 50000;
                fieldCount.value = parseInt(subtotal / fieldPrice.value);
                return fieldCount.dispatchEvent(new CustomEvent("input"));
            }

            resultSubtotal.innerHTML = subtotal;
            resultCurrency.innerHTML = fieldCurrency.value || '';
        });

        fieldCount.dispatchEvent(new CustomEvent("input"));

    }

    unitpayForm.addEventListener('submit', function (event) {
        event.preventDefault();
        let __this = this;
        __this.classList.add('lock');
        errorsBox.innerHTML = "";
        GWU_SendForm(__this)
            .then((result) => {
                if (!!!result) {
                    errorsBox.innerHTML = GWU_ErrorsListMaker(['Unknown error']); // Your message handler
                    __this.classList.remove('lock');
                    return;
                }
                if (result.status == "error") {
                    errorsBox.innerHTML = GWU_ErrorsListMaker(result.msg || ['Unknown error']); // Your message handler
                    __this.classList.remove('lock');
                    return;
                }
                window.location.href = result.redirect;
                setTimeout(() => {
                    __this.classList.remove('lock');
                }, 1000);
            });

    });

});



async function GWU_SendForm(form) {
    const formData = new FormData(form)
    const response = await fetch(form.getAttribute('action'), {
        method: 'POST',
        body: formData
    });
    return await response.json();
}


function GWU_GetSubtotal(count, price) {
    return GWU_RoundingNum(count) * GWU_RoundingNum(price);
}

function GWU_RoundingNum(x, n) {
    return parseFloat(Number.parseFloat(x).toFixed(n || 2));
}

function GWU_ErrorsListMaker(arr) {
    let errorsList = '<ul class="errors-list">';
    arr.forEach(function (error) {
        errorsList += `<li>${error}</li>`
    });
    errorsList += '</ul>';
    return errorsList;
}