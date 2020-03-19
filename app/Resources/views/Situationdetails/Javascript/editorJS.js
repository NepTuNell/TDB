
///////////////////////////////////////////////////////////////////////
//      AJOUT ET SUPPRESSION DYNAMIQUE DE BOUTON POUR DESCRIPTIF     //
///////////////////////////////////////////////////////////////////////

var $collectionHolder;
var $newLink = $('<button id="btnImg" type="button" class="btn btn-secondary button-size add_tag_link" style="margin-top: 1%; margin-bottom: 5%;"><i class="fas fa-plus"></i>image</button>');

$(document).ready(function () {

    $collectionHolder = $('tbody.pictures');
    $collectionHolder.append($newLink);
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $newLink.on('click', function (e) {

        addTagForm($collectionHolder, $newLink);

    });

});

function addTagForm($collectionHolder, $newLinkLi) {
    
    var buttonImg = document.getElementById("btnImg");
    var compteur = document.getElementsByTagName('tbody');

    if (compteur[0].rows.length > 9) {

        buttonImg.style.color = "red";
        return;

    } else {

        buttonImg.style.color = "white";

    }

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);
    console.log(newForm)
    $collectionHolder.data('index', index + 1);

    var $newFormLi = $("<tr><td class=\"col-6\">Nouvelle image</td><td class=\"col-4\">" + newForm + "</td></tr>");
    $newLinkLi.before($newFormLi);
    addTagFormDeleteLink($newFormLi);

}

function addTagFormDeleteLink($tagFormLi) {

    var buttonImg = document.getElementById("btnImg");
    var $removeFormButton = $("<td class=\"col-2\"><button style=\"margin-top: 1%;\" type=\"button\" class=\"btn btn-secondary\"><i class=\"fas fa-trash-alt\"></i></button></td>");
    $tagFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {

        $tagFormLi.remove();
        buttonImg.style.color = "white";

    });

}
