
//////////////////////////////////////////////
//      TRAITEMENT CLIQUE SUR CELLULE       //
//////////////////////////////////////////////

var winDash = document.getElementById("modalDashboard");
var winDashText = document.getElementById("modalDashboardText");
var tableDashboard = document.getElementById("table");

/**
 * Parcours du tableau dashboard et interaction clique sur cellule
 */
tableDashboard.onclick = function () {
        
        for (var i = 1; i < tableDashboard.rows.length; i++) {
                for (var j = 0; j < tableDashboard.rows[i].cells.length; j++) {

                        tableDashboard.rows[i].cells[j].onclick = function () {

                                index = this.cellIndex;
                                row = this.parentElement.rowIndex;

                                if (tableDashboard.rows[row].cells[index].textContent == "X") {

                                        var competence = document.getElementById("Entete" + index);
                                        winDash.style.display = "block";
                                        winDashText.textContent = competence.textContent;

                                } else {

                                        winDash.style.display = "none";

                                }

                        };

                }
        }
}

/**
 * Fermeture manuelle de la fenêtre
 */
function dashboardModelClose() 
{

        var winDash = document.getElementById("modalDashboard");
        winDash.style.display = "none";

}
