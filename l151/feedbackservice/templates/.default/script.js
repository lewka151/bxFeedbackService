BX.ready(function()
{
    let ajForm = BX('aj-form');
    BX.bind(ajForm, 'submit', BX.proxy(sendAjForm, this));
});


function sendAjForm(e)
{
    let ajFormData = {
        'name'     :  $('#aj-form-name').val(),
        'email'    :  $('#aj-form-email').val(),
        'comment'  :  $('#aj-form-comment').val(),
        'detected' :  $('#aj-form-detected').val(),
        'rating'   :  $('input.aj-form-rating:checked').val()   
    };

    //ajFormParams описан в template.php
    BX.ajax.runComponentAction(ajFormParams.componentName,
        'sendForm', {
            mode: 'class',
            signedParameters: ajFormParams.signedParameters,
            data: {post: ajFormData},
            type: 'POST',
            dataType: 'json',
            async: true,
            cache: false
        })
        .then(function(response)
        {
            BX.adjust(BX('aj-form'), {html: '<h2 class="text-center">' + response.data.message + '</h2>'});
        }
        , function (response) 
        {
            console.log(response);
        }
    );

    return BX.PreventDefault(e);
}