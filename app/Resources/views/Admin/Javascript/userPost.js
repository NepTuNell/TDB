window.addEventListener("beforeonload", viewPosts());

function viewPosts() {

    var httpRequest = new XMLHttpRequest();

    if (!httpRequest) {

        alert('Impossible de créer une instance de XMLHTTP');
        return false;

    }

    httpRequest.responseType = 'text';
    var index = document.getElementById("selectFilter").options[document.getElementById('selectFilter').selectedIndex].value;
    var url = Routing.generate('admin_view_posts_filter', { 'param': index });
    var tablePosts = document.getElementById("table.posts");

    httpRequest.onreadystatechange = function () {

        if (httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status === 200 || httpRequest.status === 0)) {

            document.getElementsByTagName("body")[0].style.cursor = "default";
            var data = httpRequest.responseText;
            tablePosts.innerHTML = data;

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

function userPost(id) {

    var httpRequest = new XMLHttpRequest();

    if (!httpRequest) {

        alert('Impossible de créer une instance de XMLHTTP');
        return false;

    }

    var post = document.getElementById("post");
    var url = Routing.generate('admin_view_post_filter', { 'id': id });

    httpRequest.onreadystatechange = function () {

        if (httpRequest.readyState === XMLHttpRequest.DONE && (httpRequest.status === 200 || httpRequest.status === 0)) {

            document.getElementsByTagName("body")[0].style.cursor = "default";
            var data = httpRequest.responseText;
            post.innerHTML = data;

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
