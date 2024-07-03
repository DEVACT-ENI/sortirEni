let map;
let marker;

function init(){
    //initialise la map aux coordonnées de Rennes avec map en id
    //setView ([coordonnées GPS, zoom]
    map = L.map('map').setView([48.11, -1.66], 13);

    //ajoute la carte d'open street map à la vue
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // //placement du marker au sur la position initiale
    // marker = L.marker([48.11, -1.66], {icon: issIcon}).addTo(map);


    //ajout d'un événement click géré par la fonction .on sur openstreetmap
    map.on("click", mapClickListen)

    //on écoute les événements sur l'input Ville
    //le champ "blur" est utilisé pour quand on a quitté le champ et on a sélectionné la ville
    //getCity sera la fonction qui permettra de détecter la ville et d'aller chercher les infos de positionnement
    document.querySelector("#lieu_rue").addEventListener("change", getCity);
    document.querySelector("#lieu_ville").addEventListener("change", getCity);
    document.getElementById("refresh").addEventListener("click", getCity);


    function mapClickListen(e){
        //On récupère les coordonnées du click
        let pos = e.latlng;

        //On ajoute un marqueur
        addMarker(pos);

        //on affiche les coordonnées dans le formulaire avec querySelector et l'id de l'input
        document.querySelector('#lieu_latitude').value = pos.lat;
        document.querySelector('#lieu_longitude').value = pos.lng;



    }

    function addMarker(pos){
        //On vérifie si un marqueur  (pour éviter dans rajouter plusieurs qui persistent)
        if (marker != undefined){
            //on efface le marqueur qui existe
            map.removeLayer(marker)
        }

        marker = L.marker(pos, {
            //on rend le marqueur déplaçable
            draggable : true
        })

        //On écoute le glisser-déposer sur le marqueur pour mettre à jour les coordonnnées GPS à la fin du déplacement
        marker.on("dragend", function(e){
            pos = e.target.getLatLng();
            document.querySelector('#lieu_latitude').value = pos.lat;
            document.querySelector('#lieu_longitude').value = pos.lng;
        })


        marker.addTo(map)
    }


}


function addMarker(pos){
    //On vérifie si un marqueur  (pour éviter dans rajouter plusieurs qui persistent)
    if (marker != undefined){
        //on efface le marqueur qui existe
        map.removeLayer(marker)
    }

    marker = L.marker(pos, {
        //on rend le marqueur déplaçable
        draggable : true
    })

    //On écoute le glisser-déposer sur le marqueur pour mettre à jour les coordonnnées GPS à la fin du déplacement
    marker.on("dragend", function(e){
        pos = e.target.getLatLng();
        document.querySelector('#lieu_latitude').value = pos.lat;
        document.querySelector('#lieu_longitude').value = pos.lng;
    })


    marker.addTo(map)
}


function getCity(){
    console.log("getcity");
    //On fabrique l'adresse
    let listeVilles = document.querySelector("#lieu_ville")

    let address = document.querySelector("#lieu_rue").value + ", "
        + listeVilles.options[document.querySelector("#lieu_ville").selectedIndex].text;
    // console.log(address)
    console.log(document.querySelector("#lieu_rue").value);
    console.log(address);


//On initialise une requête Ajax
const xmlhttp = new XMLHttpRequest

xmlhttp.onreadystatechange = () => {
    //Si la requête est terminée
    if (xmlhttp.readyState === 4) {
        //Si on a une réponse favorable (status 200)
        if (xmlhttp.status === 200) {
            //On récupère la réponse
            let response = JSON.parse(xmlhttp.response)

            console.log(response);

            let lat = response[0]['lat']
            let lon = response[0]['lon']
            document.querySelector('#lieu_latitude').value = lat;
            document.querySelector('#lieu_longitude').value = lon;

            let pos = [lat, lon];
            addMarker(pos);
            //permet de se déplacer sur la carte
            map.flyTo(pos, 13);

        }
    }
}

//On ouvre la requête
    xmlhttp.open("get", `https://nominatim.openstreetmap.org/search?q=${address}&format=json&addressdetails=1&limit=1&polygon_svg=1`)

    xmlhttp.send()

}

window.onload = init

