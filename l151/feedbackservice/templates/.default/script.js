BX.ready(function()
{
    let ajForm = BX('aj-form');
    BX.bind(ajForm, 'submit', BX.proxy(sendAjForm, this));
});


function sendAjForm(e)
{
    var ajFormData = {
        'name'     :  $('#aj-form-name').val(),
        'email'    :  $('#aj-form-email').val(),
        'comment'  :  $('#aj-form-comment').val(),
        'hblockid' :  $('#aj-form-hblockid').val(),
        'detected' :  $('#aj-form-detected').val(),
        'rating'   :  $('input.aj-form-rating:checked').val()   
    };

    BX.ajax.runComponentAction('l151:feedbackservice',
        'sendForm', {
            mode: 'class',
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