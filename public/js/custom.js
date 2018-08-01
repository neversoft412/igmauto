$('#postForm').on('submit', function (event) {
    event.preventDefault();
    var $postForm = $(this);

    $postForm.find('.error-label').remove();

    $.ajax({
        url: $postForm.attr('action'),
        method: 'POST',
        data: $postForm.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.status.toUpperCase() === 'WARNING') {
                alert(response.message);
                location.reload();
            } else if (response.status.toUpperCase() === 'ERROR') {
                if (response.hasOwnProperty('messages')) {
                    var errorsMessagesText = returnErrorsMessages(response.messages);
console.log(errorsMessagesText);
                    for (var errorElementName in errorsMessagesText) {
                        if (errorsMessagesText.hasOwnProperty(errorElementName)) {
                            var elementAttributeName = errorElementName;

                            if (elementAttributeName.indexOf('captcha') > -1) {
                                elementAttributeName = 'captcha[input]';
                            }

                            if (elementAttributeName.indexOf('tags') > -1) {
                                elementAttributeName = 'tags[]';
                            }

                            $('[name="' + elementAttributeName + '"]')
                                .parent()
                                .append(
                                    '<div class="error-label">'
                                    + errorsMessagesText[errorElementName]
                                    + '</div>'
                                );
                        }
                    }
                }
            } else if (response.status.toUpperCase() === 'SUCCESS') {
                alert(response.message);
                location.href = '/blog/' + response.data.id;
            }
        },
    });
});

$('.delete-post-button').on('click', function () {
    var $deletePostButton = $(this);
    if (window.confirm($deletePostButton.data('confirm-text'))) {
        var postId = $deletePostButton.data('post-id');
        var deleteUrl = $deletePostButton.data('delete-url');
        $.ajax({
            url: deleteUrl,
            method: 'POST',
            data: {
                id: postId,
            },
            dataType: 'json',
            success: function (response) {
                if (response.status.toUpperCase() === 'WARNING') {
                    alert(response.message);
                }

                location.reload();
            },
        });
    }
});

$('#tagForm').on('submit', function (event) {
    event.preventDefault();
    var $postForm = $(this);

    $postForm.find('.error-label').remove();

    $.ajax({
        url: $postForm.attr('action'),
        method: 'POST',
        data: $postForm.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.status.toUpperCase() === 'WARNING') {
                alert(response.message);
                location.reload();
            } else if (response.status.toUpperCase() === 'ERROR') {
                if (response.hasOwnProperty('messages')) {
                    var errorsMessagesText = returnErrorsMessages(response.messages);

                    for (var errorElementName in errorsMessagesText) {
                        if (errorsMessagesText.hasOwnProperty(errorElementName)) {
                            $('[name="' + errorElementName + '"]')
                                .parent()
                                .append(
                                    '<div class="error-label">'
                                    + errorsMessagesText[errorElementName]
                                    + '</div>'
                                );
                        }
                    }
                }
            } else if (response.status.toUpperCase() === 'SUCCESS') {
                alert(response.message);
                location.reload();
            }
        },
    });
});

$('.delete-tag-button').on('click', function () {
    var $deleteTagButton = $(this);
    if (window.confirm($deleteTagButton.data('confirm-text'))) {
        var tagId = $deleteTagButton.data('tag-id');
        var deleteUrl = $deleteTagButton.data('delete-url');
        $.ajax({
            url: deleteUrl,
            method: 'POST',
            data: {
                id: tagId,
            },
            dataType: 'json',
            success: function (response) {
                if (response.status.toUpperCase() === 'WARNING') {
                    alert(response.message);
                }
                location.reload();
            },
        });
    }
});

function returnErrorsMessages(errorsMessages) {
    var errorsMessagesText = [];

    for (var elementNameAttr in errorsMessages) {
        if (errorsMessages.hasOwnProperty(elementNameAttr)) {
            var elementName = elementNameAttr;

            for (var messageKey in errorsMessages[elementNameAttr]) {
                if (errorsMessages[elementNameAttr].hasOwnProperty(messageKey)) {
                    elementName += '[' + messageKey + ']';
                    var subErrorsMessages = errorsMessages[elementNameAttr][messageKey];

                    if (typeof subErrorsMessages === 'object') {
                        for (var subMessageKey in subErrorsMessages) {
                            if (subErrorsMessages.hasOwnProperty(subMessageKey)) {
                                if (typeof subErrorsMessages[subMessageKey] === 'object') {
                                    var subElementName = elementName + '[' + subMessageKey + ']';

                                    for (var error in subErrorsMessages[subMessageKey]) {
                                        if (subErrorsMessages[subMessageKey].hasOwnProperty(error)) {
                                            if (typeof errorsMessagesText[subElementName] === 'undefined') {
                                                errorsMessagesText[subElementName]
                                                    = subErrorsMessages[subMessageKey][error];
                                            }
                                            else {
                                                errorsMessagesText[subElementName]
                                                    += '<br />' + subErrorsMessages[subMessageKey][error];
                                            }
                                        }
                                    }
                                }
                                else {
                                    errorsMessagesText[subMessageKey] = subErrorsMessages[subMessageKey];
                                }
                            }
                        }
                    }
                    else {
                        errorsMessagesText[elementNameAttr] = subErrorsMessages;
                    }

                    elementName = elementNameAttr;
                }
            }
        }
    }

    return errorsMessagesText;
}
