document.addEventListener('DOMContentLoaded', function (root){
    const form = document.querySelector('.form-get-files');
    const fieldsForCheck = form.querySelectorAll('.check');
    const fillableFields = form.querySelectorAll('.form-get-files__input');
    const modal = document.getElementsByClassName("modal-fade");
    const successNotification = document.getElementsByClassName("success");
    const labelEmail = document.querySelector('#label_email');
    const errorMesssage = document.getElementsByClassName('error-message');

    function insertAfter(newNode, existingNode) {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

    /**
     * Генерация элемента, отображающего ошибку
     * @param text - текст ошибки
     * @returns {HTMLDivElement} - разметка ошибки
     */
    const generateError = function (text) {
        const error = document.createElement('div');
        error.className = 'error-message';
        // error.style.color = 'red';
        error.innerHTML = text;
        return error;
    };

    /**
     * Удаление отображающихся ошибок, если они выведены
     */
    const removeValidation = function () {
        const errors = form.querySelectorAll('.error');
        for (let i = 0; i < errors.length; i++) {
            errors[i].remove()
        }
    };

    /**
     * Валидация полей
     */
    const checkFieldsPresence = function () {
        let errors = 0;
        for (let i = 0; i < fieldsForCheck.length; i++) {
            if (!fieldsForCheck[i].value) {
                const error = generateError('Пожалуйста, заполните поле');
                insertAfter(error, fieldsForCheck[i]);
                errors++;
            }
        }
        return errors;
    };

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        removeValidation();
        if (checkFieldsPresence() === 0) {
            // Настройка AJAX запроса
            const request = new XMLHttpRequest();
            request.open('POST', 'php/send.php', true);
            request.setRequestHeader('accept', 'application/json');

            // Это простой способ подготавливить данные для отправки (все браузеры и IE > 9)
            const formData = new FormData(form);
            // Отправляем данные
            request.send(formData);
            request.onreadystatechange = function () {
                // <4 =  ожидаем ответ от сервера
                if (request.readyState < 4) {}
                // 4 = Ответ от сервера полностью загружен
                else if (request.readyState === 4) {
                    // 200 - 299 = успешная отправка данных!
                    if (request.status === 200 && request.status < 300) {
                        for (let i = 0; i < fillableFields.length; i++) {
                            fillableFields[i].value = "";
                        }
                        // if (errorMesssage[0].style.display === "block") {
                        //     errorMesssage[0].style.display = "none";
                        // }
                        if (modal[0].style.display === "block") {
                            modal[0].style.display = "none";
                        }
                        if (successNotification[0].style.display === "" || successNotification[0].style.display === "none") {
                            successNotification[0].style.display = "inline-block";
                        }
                        setTimeout(function(){
                            successNotification[0].style.display = 'none';
                        }, 2000);
                    } else {
                        const errors = JSON.parse(request.responseText);
                        for (let i = 0; i < errors.length; i++) {
                            const error = generateError(errors[i]);
                            form[i].parentElement.insertBefore(error, labelEmail);
                        }
                    }
                }
            }
        }
    })
});