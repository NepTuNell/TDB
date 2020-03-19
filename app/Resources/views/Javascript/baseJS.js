
$(document).ready(function () {

    /**********************************************************
     *           Traitement collision fenêtre modale
     **********************************************************/
    var imgMod = document.getElementById("modalImage");
    var winMod = document.getElementById("modalWindow");

    /*
    * Test de la position du curseur pour fermeture de la fenêtre sur clic
    */
    document.getElementsByTagName('body')[0].onclick = function (event) {

        if ( imgMod !== null ) {
                
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

        /****************************************************************
         *                Traitement Popup page de login
         ****************************************************************/
        var popupHelp      = $('#popupHelp');
        var popupToDisplay = $('.popup');
            
        if ( popupHelp.length ) {
            var popupHelpPos      = popupHelp[0].getBoundingClientRect();
            var popupToDisplayPos = popupToDisplay[0].getBoundingClientRect();

            if ( event.clientY < popupHelpPos['bottom'] && event.clientY > popupHelpPos['top'] &&
                event.clientX > popupHelpPos['left']   && event.clientX < popupHelpPos['right']) {
                popupToDisplay.css('display', 'block');
            } else {
                if (event.clientY < popupToDisplayPos['bottom'] && event.clientY > popupToDisplayPos['top'] &&
                    event.clientX > popupToDisplayPos['left']   && event.clientX < popupToDisplayPos['right']) {
                    popupToDisplay.css('display', 'block');
                } else {
                    popupToDisplay.css('display', 'none');
                }
            }
        }

    }

});

 
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
//      VISUALISATION DES POSTS UTILISATEUR     //
//////////////////////////////////////////////////

function viewFullPost(user, situation) {

    checkConnected().then(view, disconnected);

    function view () {

        var httpRequest = new XMLHttpRequest();

        if (!httpRequest) {
                alert('Impossible de créer une instance de XMLHTTP');
                return false;
        }

        httpRequest.responseType = 'text';
        var url = Routing.generate('situation_details_show', { 'user': user, 'situation': situation });
        var page = document.getElementById("main_page");

        httpRequest.onreadystatechange = function () {

                if (httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status === 200 || httpRequest.status === 0)) {

                        document.getElementsByTagName("body")[0].style.cursor = "default";
                        var data = httpRequest.responseText;
                        page.innerHTML = data;

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

    function disconnected () {

        alert('Vous avez été déconnecté(e) !');
        document.location.reload(true);

    }

}

/////////////////////////////////////////////////
//      SUPPRESSION D'UN POST UTILISATEUR      //
/////////////////////////////////////////////////

function deletePost(user, situation, situationDetails) {

    checkConnected().then(deletePost, disconnected);
    
    function deletePost () {

        if ( deleteRefDetails() ) {

                var httpRequest = new XMLHttpRequest();

                if (!httpRequest) {
                        alert('Impossible de créer une instance de XMLHTTP');
                        return false;
                }

                httpRequest.responseType = 'text';
                var url = Routing.generate('situation_details_delete', { 'id': situationDetails });

                httpRequest.onreadystatechange = function () {

                        if (httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status === 200 || httpRequest.status === 0)) {

                                document.getElementsByTagName("body")[0].style.cursor = "default";
                                var tryDelete = JSON.parse(httpRequest.responseText);

                                if (tryDelete != "Ok") {

                                        alert("La suppression de l'élément a échoué !");
                                        console.log(tryDelete);
                                        return false;

                                }

                                alert("L'élément a bien été supprimé !");
                                viewFullPost(user, situation);
                                
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

    }

    function disconnected () {

        alert('Vous avez été déconnecté(e) !');
        document.location.reload(true);

    }

}

///////////////////////////////////////////////////////////////////////////////////////
//       VISUALISATION DES UTILISATEURS DANS LA NAVIGATION A GAUCHE DE L'ECRAN       //
///////////////////////////////////////////////////////////////////////////////////////

function changeViewList () {

    checkConnected().then(view, disconnected);

    function view () {

        var httpRequest = new XMLHttpRequest();

        if (!httpRequest) {

                alert('Impossible de créer une instance de XMLHTTP');
                return false;

        }

        httpRequest.responseType = 'text';
        var url = Routing.generate('list_choice');
        var mainNav = document.getElementById('userNavigation');

        httpRequest.onreadystatechange = function () {
            if (httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status === 200 || httpRequest.status === 0)) {

                document.getElementsByTagName("body")[0].style.cursor = "default";
                var data = httpRequest.responseText;
                mainNav.innerHTML = data;

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

    function disconnected () {

        alert('Vous avez été déconnecté(e) !');
        document.location.reload(true);

    }

}
 

function checkConnected ()
{

    return new Promise( (resolve, reject) => {

        var httpRequest = new XMLHttpRequest();

        if (!httpRequest) {

            alert('Impossible de créer une instance de XMLHTTP');
            return false;

        }
        
        httpRequest.responseType = "text";
        var url = Routing.generate('user_check_connected');

        httpRequest.onreadystatechange = function () {

            if ( httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status === 200 || httpRequest.status === 0) ) {

                if ( httpRequest.responseText === '1' ) {
                    resolve();
                } else {
                    reject();
                }

            } 

        }

        httpRequest.open('POST', url, true);
        httpRequest.send();

    });

}