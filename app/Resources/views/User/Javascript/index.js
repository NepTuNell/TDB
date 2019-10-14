
/**
 * 
 * @param {*} user 
 * @param {*} situation 
 */
function viewFullPost(user, situation) {

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

/**
 * 
 * @param {*} user 
 * @param {*} situation 
 * @param {*} situationDetails 
 */
function deletePost(user, situation, situationDetails) {

        if (deleteRefDetails()) {

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

/**
 * 
 */
function changeViewList() {

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
