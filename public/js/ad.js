$('#add-image').click(function(){
    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();
    console.log(index);
    // Je récupère le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    // J'injecte ce code au sein de la div
    $('#ad_images').append(tmpl);
    $('#widgets-counter').val(index+1);


    // je gére le bouton supprimer
    handleDeleteButtons()
});

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function (){
        const target = this.dataset.target;
        $(target).remove();
    })
}
function updateCounter(){
    const  count = +$('div.form-group').length-1;
    $('#widgets-counter').val(count);
}

handleDeleteButtons();
updateCounter();
