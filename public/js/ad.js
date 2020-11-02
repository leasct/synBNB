$('#add-image').click(function () {

    //récupérer les numéro des futurs champs
    const index = +$('#widgets-counter').val();

    //récupération des prototype d'entré
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    // Injection du code dans la div
    $('#ad-images').append(tmpl)

    $('#widgets-counter').val(index + 1)

    // Gestion du boutton supprimer
    handleDeleteButtons();

});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updatecouter(){
    const count = +$('#ad_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updatecounter();
handleDeleteButtons();