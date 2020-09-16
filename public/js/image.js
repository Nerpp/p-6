
    // Gestion des boutons "Supprimer"
    let links = document.querySelectorAll("[data-delete]");
    for (link of links) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm("Voulez-vous supprimer cet image")) {
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    response => response.json()
                ).then(data => {
                    if (data.success) {
                        this.parentElement.remove();
                    } else {
                        alert(data.error)
                    }
                }).catch(e => alert(e))
            }
        })
    }



    /*
    // On boucle sur links
    for(link of links){
        // On écoute le clic
        link.addEventListener("click", function(e){
            console.log(link);
            // On empêche la navigation
            e.preventDefault();

            // On demande confirmation
            if(confirm("Voulez-vous supprimer cette image ?")){
                // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    // On récupère la réponse en JSON
                    response => response.json()
                ).then(data => {
                    console.log(data);
                    if(data.success)
                        this.parentElement.remove();
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
    */

