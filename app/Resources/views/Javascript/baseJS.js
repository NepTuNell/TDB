//////////////////////////////////////////////
//      TRAITEMENT DE LA FENETRE MODALE     //
//////////////////////////////////////////////
var imgMod = document.getElementById("modalImage");
var winMod = document.getElementById("modalWindow");

/*
* Test de la position du curseur pour fermeture de la fenêtre sur clic
*/
document.getElementsByTagName('body')[0].onclick = function (event) {

    if (window.event) {
        event = window.event;
    }

    var winModPosition = winMod.getBoundingClientRect();
    var imgModPosition = imgMod.getBoundingClientRect();
    
    if (event.clientY < imgModPosition['bottom'] && event.clientY > imgModPosition['top'] &&
        event.clientX > imgModPosition['left'] && event.clientX < imgModPosition['right']) {

        winMod.style.display = "flex";

    } else {

        if (event.clientY < winModPosition['bottom'] && event.clientY > winModPosition['top'] &&
            event.clientX > winModPosition['left'] && event.clientX < winModPosition['right']) {

            winMod.style.display = "flex";

        } else {

            winMod.style.display = "none";

        }

    }

}

////////////////////////////////////////
//      CONFIRMATION SUPPRESSION      //
//////////////////////////////////////// 

function deleteRefDetails() {

    if (!window.confirm("Voulez-vous réellement supprimer cet élément ?")) {

        return false;

    } else {

        return true;

    }

}

//////////////////////////////////////////////////
//        GESTION DU CHARGEMENT DES PAGES       //
//////////////////////////////////////////////////

window.addEventListener("load", function clean() {

    // Suppression des éléments dynamiques lors d'un chargement

    var userNav = document.getElementById('userNav');

    /*
     * Test si l'élément contenant les utilisateurs existe, si non fin de la fonction
     */
    if (null !== userNav) {

        showUser();

    }

    // Suppression des éléments dynamiques lors d'un chargement

    var divPictures = document.getElementById('siobundle_situationdetails_pictures');

    /*
     * Test si l'élément contenant les images lors de la création d'une tâche existe, 
     * si oui suppression de toutes les images au rechargement de la page 
     */
    if (divPictures !== null) {

        while (divPictures.firstChild) {

            divPictures.removeChild(divPictures.firstChild);

        }

    }

});


////////////////////////////////////////////////
//        MISE A JOUR AFFICHAGE USERS         
////////////////////////////////////////////////

function showUser() {

    /*
    * Déclaration des variables locales à la fonction
    */
    var userNav = document.getElementById('user_navigation');
    var url = Routing.generate('users_list');
    var httpRequest = new XMLHttpRequest();

    /*
    * Test si l'objet XMLHttpRequest a bien été instancié, si non fin de la fonction
    */
    if (!httpRequest) {

        alert('Impossible de créer une instance de XMLHTTP');
        return false;

    } else {

        httpRequest.responseType = 'text'; // Type de retour attendu

    }

    httpRequest.onreadystatechange = function () {

        if (httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status === 200 || httpRequest.status === 0)) {

            var data = JSON.parse(httpRequest.responseText);
            var users = data[0];

            userNav.innerHTML = "";
            userNav.innerHTML += "<div class=\"row justify-content-around\">\n\
                                            <h5 class=\"user_nav_lib\">Utilisateurs</h5>\n\
                                        </div>"

            for (var i = 0; i < users.length; i++) {

                document.getElementsByTagName("body")[0].style.cursor = "default";
                var url = Routing.generate('user_show', { 'id': users[i].id });
                var color = (users[i].roles[0] === "ROLE_ADMIN" ? "rgba(17, 135, 165, 0.4)" : "rgba(155, 155, 153, 0.1)");

                userNav.innerHTML += "<div class=\"row justify-content-around\">\n\
                                                    <div class =\"col-10 user_nav_li text-center\" style=\"background-color: "+ color + "\">\n\
                                                        <li>\n\
                                                            <a href=\""+ url + "\">\n\
                                                                "+ users[i].username + "\n\
                                                            </a>\n\
                                                        </li>\n\
                                                    </div>\n\
                                              </div>";

            }

        } else if (httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status !== 200 || httpRequest.status !== 0)) {

            alert("Erreur de chargement des données !");
            return false;

        } else {

                document.getElementsByTagName("body")[0].style.cursor = "wait";

        }

    }

    httpRequest.open('POST', url, true);
    httpRequest.send();

}

// Appel des fonctions à un temps donné
var printTime = setInterval(printTime, 1000);
