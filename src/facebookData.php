<!DOCTYPE html>
<html lang="en">
<!-- Head -->
<head>
        <link rel="icon" href="data:;base64,iVBORw0KGgo=">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="http://code.jquery.com/jquery.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
        <meta charset=utf-8" /> 
    <title>Facebook Connexion</title>
    <style>
        #map{
            position: relative;
            overflow: hidden;
            height: 500px;
        }
        .zmdi{
            float: left;
            height: 50px;
            padding-left: 15px ;
            padding-right: 15px;
            line-height: 20px;
        }

    </style>

</head>

<!-- démarrage de session php -->
<?php
    session_start();
?>

<!-- Scripts en javascript -->
<script>
    // Read a page's GET URL variables and return them as an associative array.
    var getParameterByName = function (name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
</script>

<script>

    var getPlace = function (element) {
        if(element.place){
            if(element.place.location.country == "Tunisia"){
                places.push(element.place.location.city);

            }
        } else {
            return null;
        }

    }
</script>

<script>

    var calculateElements = function (data) {
        for (element in data){
            post = data[element];

            //console.log(post.id + ": " +post.message+" created at: "+ post.created_time);
            //calculate the number of each type of posts
            if(post.type == "photo"){
                imageNumber++;
            };
            if (post.type == "status"){
                statusNumber++;
            };
            if(post.type == "link"){
                LinkNumber++;
            };
            if(post.type == "video"){
                videoNumber++;
            };

            //get the places of posts
            getPlace(post);
        }
    };

</script>

<script>
    /*
     * Récupère les commentaires des posts et les place dans le tableau texteComm
     * texteComm[0] : commentaire
     * texteComm[1] : indice du post auquel il est relié
     * @param {type} args
     * @returns {tableau de String}
     */
    function getComments(args)
    {
        var data;
        var result = "";
        data = args.data || null;
        
        //Si pas de commentaire
        if (data == null || !data.length) 
        {
            return null;
        }
        
        //Pour chaque commentaire
        data.forEach(function(item)
        {   
            var comments = "    ";
            var dataResponse;
            //on vérifie que le commentaire ne soit pas null ou ""
            if((item["message"]) && (item["message"] !== ""))
            {
                //On l'ajoute à notre affichage
                comments += item["message"];
                //On le stocke dans un tableau
                texteComm[0].push(item["message"]);
                //On mets l'indice du post dans le tableau 
                texteComm[1].push(textePosts.length -1);
            }
            result += comments + "\n";
            //S'il y a des réponses à ce commentaire
            if(item["comments"] && item["comments"] != undefined)
            {
                dataResponse = item["comments"].data || null;
                //On vérifie que les réponses ne soient pas null
                if(dataResponse != null && dataResponse.length>0)
                {
                    var commentsResponse;
                    result += "Réponse(s) :\n";
                    //On traite chaque réponse
                    dataResponse.forEach(function(itemResponse)
                    {
                        commentsResponse = "        ";
                        //On vérifie que la réponse existe et ne soit pas ""
                        if((itemResponse["message"]) && (itemResponse["message"] !== ""))
                        {
                            //On l'ajoute à l'affichage
                            commentsResponse += itemResponse["message"];
                            //On l'ajoute au tableau 
                            // Aucune distinction n'est faite entre les commentaires et les réponses dans la 
                            //Sauvegarde des données
                            texteComm[0].push(itemResponse["message"]);
                            texteComm[1].push(textePosts.length - 1);
                        }
                        result += commentsResponse + "\n";
                    });
                }
            }
            
        });
        //Retourne le résultat d'affichage
        return result;
    }
</script>

<script>
    var ajouteInfluenceur = function(args)
    {
        var nomInfluenceur = args.name;
        if(influenceurs[0].indexOf(nomInfluenceur) === -1)
        {
            influenceurs[0].push(nomInfluenceur);
            influenceurs[1].push(1);
        }
        else
        {
            var indiceInfluenceur = influenceurs[0].indexOf(nomInfluenceur);
            influenceurs[1][indiceInfluenceur] ++;
        }
    };
</script>


<script>
    /*
     * Récupère les données et les place dans les tableaux corespondants
     * @param {type} texte
     * @returns {String}
     */

    function convertArrayOfObjectsToCSV(args) {
        var result, ctr, keys, columnDelimiter, lineDelimiter, data;
        data = args.data || null;
        if (data == null || !data.length) {
            return null;
        }

        columnDelimiter = args.columnDelimiter || ',';
        lineDelimiter = args.lineDelimiter || '\n';
        for(var i=0; i<data.length; i++){
            if(Object.keys(data[i]).length == 7){
                keys = Object.keys(data[i]);
                console.log('keys: '+ keys);
                break;
            }
        }
        result = '';
        result += keys.join(columnDelimiter);
        result += lineDelimiter;
        
        //Pour chaque poste
        data.forEach(function(item) {
            ctr = 0;
            
            //On traite le message, le type, le lieu, la date et les potentiels commentaires
            keys.forEach(function(key) {
                switch (key)
                {
                    case "message" : 
                        result += "Post :\n";
                        if(item["message"] && item["message"] != undefined)
                        {
                            result += "    " + item["message"] + "\n";
                            textePosts.push(item["message"]);
                        }
                        else
                        {
                            result += "    " + "Pas de message.\n";
                            textePosts.push(null);
                        }
                        break;
                    case "comments" : 
                        result += "Commenataire(s) :\n";
                        if(item["comments"] && item["comments"] != undefined)
                        { 
                            result += getComments(item["comments"]) + "\n";
                        }
                        else
                        {
                            result += "    " + "Pas de commentaire.\n\n";
                        }
                        break;
                    case "type" : 
                        result += "Type :\n";
                        if(item["type"] && item["type"] != undefined)
                        { 
                            result += "    " + item["type"] + "\n";
                            texteDonnees[0].push(item["type"]);
                        }
                        else
                        {
                            result += "    " + "Pas de type.\n\n";
                            texteDonnees[0].push(null);
                        }
                        break;
                    case "created_time" : 
                        result += "Date de création :\n";
                        if(item["created_time"] && item["created_time"] != undefined)
                        { 
                            result += "    " + item["created_time"] + "\n";
                            texteDonnees[1].push(item["created_time"]);
                        }
                        else
                        {
                            result += "    " + "Pas de date de création.\n\n";
                            texteDonnees[1].push(null);
                        }
                        break;
                    case "link" : 
                        result += "Lien :\n";
                        if(item["link"] && item["link"] != undefined)
                        { 
                            result += "    " + item["link"] + "\n";
                            texteDonnees[2].push(item["link"]);
                        }
                        else
                        {
                            result += "    " + "Pas de lien.\n\n";
                            texteDonnees[2].push(null);
                        }
                        break;
                    case "from" :
                        result += "Personne qui a fait le post:\n";
                        if(item["from"] && item["from"] != undefined)
                        { 
                            result += "    " + item["from"].name + "\n";
                            ajouteInfluenceur(item["from"]);
                        }
                        else
                        {
                            result += "    " + "Pas de personne qui a fait le post.\n\n";
                        }
                        break;
                        
                    default : 
                        break;
                }   
            });
            result += lineDelimiter;
        });
        
        //on renvoie le résultatque l'on va afficher
        return result;
    }

</script>


<script>
    /*
     * Ne récupère que les messages français pour en garder un fichier ".txt"
     * @param {type} texte
     * @returns {Array}
     */

    function getOnlyFrenchMessages(texte) {
        var result;


        result = '';

        //Pour chaque message  ( 1 case = un message)
        for(var k =0;k<texte.length;k++) 
        {
            var message = texte[k];
            if(typeof message !== undefined ){
                //if the message in french language add it to result
                if (message){
                    var splittedMessage = message.toLowerCase().split(" ");
                    //Regex utilisée pour vérifier qu'un mot contient des chiffres (qu'il s'agit de l'arabe écrit en francais).
                    //Pour que le mot soit considéré comme arabe, on a fait le choix qu'il faut qu'au moins une lettre précède le chiffre
                    var regexArabic = new RegExp(/[a-z]+[235679][a-z]*/, 'i');
                    //booléen qui vaut vrai si l'on a trouvé un caractère arabe.
                    var b1=0;
                    //booléen qui vaut vrai si l'on a trouvé un mot qui n'existe qu'en francais
                    var b2=0;
                    //permet de tester si l'on trouve un caractère de l'alphabet arabe
                    for(var i=0;i<splittedMessage.length && b1==0;i++)
                    {
                        for(var j=0;j<splittedMessage[i].length && b1==0;j++)
                        {
                            if(1569<=String(splittedMessage[i]).codePointAt(j) && String(splittedMessage[i]).codePointAt(j)<=1791)
                            {
                                b1=1;
                            }
                        }    
                    }
                    //permet de tester s'il y a un mot indiquant que la langue est le francais
                    for(var i=0;i<splittedMessage.length && b2==0;i++)
                    {
                        if( String(splittedMessage[i]).localeCompare('je')==0 || String(splittedMessage[i]).localeCompare('tu')==0 || String(splittedMessage[i]).localeCompare('elle')==0 || String(splittedMessage[i]).localeCompare('nous')==0 || String(splittedMessage[i]).localeCompare('vous')==0 || String(splittedMessage[i]).localeCompare('ils')==0 || String(splittedMessage[i]).localeCompare('elles')==0 || String(splittedMessage[i]).localeCompare('de')==0 || String(splittedMessage[i]).localeCompare('et')==0 || String(splittedMessage[i]).localeCompare('mais')==0 || String(splittedMessage[i]).localeCompare('donc')==0 || String(splittedMessage[i]).localeCompare('ou')==0 || String(splittedMessage[i]).localeCompare('un')==0 || String(splittedMessage[i]).localeCompare('une')==0 || String(splittedMessage[i]).localeCompare('des')==0 || String(splittedMessage[i]).localeCompare('du')==0 )
                        {
                            b2=1;
                        }
                    }
                    
                    //On vérifie qu'il n'y a pas de caractère arabe dans le message.
                    if(b1!=1)
                    {
                        //On vérifie qu'il n'y a pas de mots arabes écrits en français dans le message
                        if(!regexArabic.test(splittedMessage))
                        {
                            //On s'assure qu'il s'agisse bien du français en regardant s'il y a des mots-clés dans le message.
                            if(b2==1)
                            {
                                result += "\n"+ message +"\n"+ "***";
                            }
                        }
                        
                    }
                }
            }

        }
        return result;
    }

</script>

<script>
    /*
     * Permet de compter le nombre de message de chaque langue
     * @param {type} texte
     * @returns {Array}
     */

    function getOnlyMessagesNumber(texte) {

        var numberofmessage = 0;
        var numberofarabicmessage = 0;
        var numberoffrenchmessage = 0;
        var numberofarabicfrenchmessage =0;
        var result = new Array(4);

        for(var k =0;k<texte.length;k++) 
        {
            var message = texte[k];
            
            if(typeof message !== undefined ){
                //if the message in french language add it to result
                if (message !== null){
                    numberofmessage ++;
                    var splittedMessage = message.toLowerCase().split(" ");
                    
                    //Regex utilisée pour vérifier qu'un mot contient des chiffres (qu'il s'agit de l'arabe écrit en francais).
                    //Pour que le mot soit considéré comme arabe, on a fait le choix qu'il faut qu'au moins une lettre précède le chiffre
                    var regexArabic = new RegExp(/[a-z]+[235679][a-z]*/, 'i');
                    //booléen qui vaut vrai si l'on a trouvé un caractère arabe.
                    var b1=0;
                    //booléen qui vaut vrai si l'on a trouvé un mot qui n'existe qu'en francais
                    var b2=0;
                    
                    //permet de tester si l'on trouve un caractère de l'alphabet arabe
                    for(var i=0;i<splittedMessage.length && b1==0;i++)
                    {
                        for(var j=0;j<splittedMessage[i].length && b1==0;j++)
                        {
                            if(1569<=String(splittedMessage[i]).codePointAt(j) && String(splittedMessage[i]).codePointAt(j)<=1791)
                            {
                                b1=1;
                            }
                        }   
                    }
                    //permet de tester s'il y a un mot indiquant que la langue est le francais
                    for(var i=0;i<splittedMessage.length && b2==0;i++)
                    {
                        if( String(splittedMessage[i]).localeCompare('je')==0 || String(splittedMessage[i]).localeCompare('tu')==0 || String(splittedMessage[i]).localeCompare('elle')==0 || String(splittedMessage[i]).localeCompare('nous')==0 || String(splittedMessage[i]).localeCompare('vous')==0 || String(splittedMessage[i]).localeCompare('ils')==0 || String(splittedMessage[i]).localeCompare('elles')==0 || String(splittedMessage[i]).localeCompare('de')==0 || String(splittedMessage[i]).localeCompare('et')==0 || String(splittedMessage[i]).localeCompare('mais')==0 || String(splittedMessage[i]).localeCompare('donc')==0 || String(splittedMessage[i]).localeCompare('ou')==0 || String(splittedMessage[i]).localeCompare('un')==0 || String(splittedMessage[i]).localeCompare('une')==0 || String(splittedMessage[i]).localeCompare('des')==0 || String(splittedMessage[i]).localeCompare('du')==0 )
                        {
                            b2=1;
                        }
                    }
                    
                    //si l'on a trouvé au moins un caractère de l'alphabet arabe, c'est écrit en arabe.
                    if(b1==1)
                    {
                        numberofarabicmessage++;
                    }
                    //sinon si l'on trouve un mot avec un chiffre dedans c'est de l'arabe écrit en francais
                    else if(regexArabic.test(splittedMessage))
                    {
                        numberofarabicfrenchmessage ++;
                    }
                    //sinon s'il contient des mot qui sont propres au francais uniquement c'est en francais
                    else if(b2==1)
                    {
                        numberoffrenchmessage ++;
                    }
                    
                    //sinon s'il ne contient aucun de ces mots, c'est de l'arabe en francais
                    else
                    {
                        numberofarabicfrenchmessage ++;
                    }

                }
            }

        }
        
        //Retourne un tableau multidimensionnel avec les nombres de messages de chaque langue
        result[0] = numberofmessage;
        result[1] = numberofarabicmessage;
        result[2] = numberoffrenchmessage;
        result[3] = numberofarabicfrenchmessage;
        return result;
    }

</script>

<script>
    function downloadFile(args) {
        var data, filename, link;
        var messages = args.data;
        if (messages == null) return;

        filename = args.filename || 'export.txt';

        if (!messages.match(/^data:text\/plain/i)) {
            messages = 'data:text/plain;charset=utf-8,' + messages;
        }
        data = encodeURI(text);

        link = document.createElement('a');
        link.setAttribute('href', data);
        link.setAttribute('download', filename);
        link.click();
    }

</script>

<script>
    function downloadCSV(args) {
        var data, filename, link;
        var csv = args.data;
        if (csv == null) return;

        filename = args.filename || 'export.csv';

        if (!csv.match(/^data:text\/csv/i)) {
            csv = 'data:text/csv;charset=utf-8,' + csv;
        }
        data = encodeURI(csv);

        link = document.createElement('a');
        link.setAttribute('href', data);
        link.setAttribute('download', filename);
        link.click();
    }

</script>

<script>
    /* this function allow the determination the latitude and longitude of each place */
    var getLatandLongFromCity = function (listOfCities) {

        for (var city in listOfCities){
            $.getJSON("https://maps.googleapis.com/maps/api/geocode/json?address="+encodeURIComponent(city), function(val) {
                if(val.results.length) {
                    var location = val.results[0].geometry.location;
                    latandLongArray.push(location);
                }
            });
        };
    };
</script>

<script>

    var getMarkers = function () {


        if (latandLongArray.length === 0) {
            window.setTimeout(getMarkers, 1000);
        } else {

            var i =0;
            latandLongArray.forEach(function (element) {

                var contentString = '<div id="content">' +
                    '<div id="siteNotice">' +
                    '</div>' +
                    '<h3 id="firstHeading" class="firstHeading">'+ Object.keys(counts)[i]+'</h3>' +
                    '<div id="bodyContent">' +
                    '<p><b>'+counts[Object.keys(counts)[i]]+'</b>, publications' +
                    '</p>' +
                    '</div>' +
                    '</div>';

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });


                var marker = new google.maps.Marker({
                    position: element,
                    map: map,
                    title: Object.keys(counts)[i]
                });

                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });
                i++;

            });
        }


    }
</script>

<script>
    
    //fonction utilisée pour trier correctement des nombres comportant plusieurs chiffres
    function compareNombres(a, b) 
    {
        return a - b;
    }
    
    /*
     * On trie le tableau des influenceurs
     */
    var trierInfluenceurs = function(influenceurs)
    {
        //On copie les noms et leurs nombres respectifs de posts dans d'autres tableaux
        var noms = new Array(10);
        var nbrs = influenceurs[1].slice();
        //On trie le tableau de nombres
        nbrs.sort(compareNombres);
        nbrs.reverse();
        
        var ancienIndice=0;//Pour ne pas toujours avoir la même personnes si elles ont autant de posts
        var ancienneValeur = 0;
        //On réassocie chaque nom au nombre qui lui était assigné au départ
        for(var i=0;i<10;i++)
        {
            var indiceInfluenceur = influenceurs[1].indexOf(nbrs[i], ancienIndice);
            //console.log(ancienneValeur);
            //console.log(influenceurs[1][indiceInfluenceur]);
            if(ancienneValeur != influenceurs[1][indiceInfluenceur])
            {
                
                ancienIndice = 0;
                indiceInfluenceur = influenceurs[1].indexOf(nbrs[i], ancienIndice);
            }
            //console.log(indiceInfluenceur);
            noms[i] = influenceurs[0][indiceInfluenceur];
            ancienIndice = indiceInfluenceur +1;
            ancienneValeur = influenceurs[1][indiceInfluenceur];
        }
        //On remet les tableau [1][10] dans le tableau influenceurs
        influenceurs[0] = noms;
        influenceurs[1] = nbrs.slice(0,10);
        
    };
</script>

<script>

    var getPosts = function (response){
        //calculate the different typ of posts and call get place of the post
        calculateElements(response.data);

        //convert response to csv data
        csv_data += convertArrayOfObjectsToCSV(response);
        
       
        if(response.paging){
            nextPage = response.paging.next;
            if(nextPage){
                //Method 1
                $.get(nextPage, getPosts, "json");

            }

        } 
        else 
        {
            $('#imageNumber').text(imageNumber);
            $('#LinkNumber').text(LinkNumber);
            $('#videoNumber').text(videoNumber);
            $('#statusNumber').text(statusNumber);
            
            var numberPosts = new Array(4);
            numberPosts = getOnlyMessagesNumber(textePosts);

            if(numberPosts)
            {
                numberMessagesPosts = numberPosts[0];
                numberArabicMessagesPosts = numberPosts[1];
                numberFrenchMessagesPosts = numberPosts[2];
                numberFrenchOrArabicMessagesPosts = numberPosts[3];            
            }
            
            var numberComm = new Array(4);
            numberComm = getOnlyMessagesNumber(texteComm[0]);

            if(numberComm)
            {
                numberMessagesComm = numberComm[0];
                numberArabicMessagesComm = numberComm[1];
                numberFrenchMessagesComm = numberComm[2];
                numberFrenchOrArabicMessagesComm = numberComm[3];            
            }
            
            frenchMessage = getOnlyFrenchMessages(textePosts);
            // frenchMessage += getOnlyFrenchMessages(texteComm[0]);
            

            places.forEach(function(x) { counts[x] = (counts[x] || 0)+1; });


            getLatandLongFromCity(counts);
            getMarkers();
           
            
            csv_data += "\n" + "There is : " + numberMessagesPosts + " messages in all.";
            csv_data += "\n" + "There is : " + numberArabicMessagesPosts + " messages in Arabic.";
            csv_data += "\n" + "There is : " + numberFrenchMessagesPosts + " messages in French.";
            csv_data += "\n" + "There is : " + numberFrenchOrArabicMessagesPosts + " messages in Arabic written in French.";
            
            csv_data += "\n";
            
            csv_data += "\n" + "There is : " + numberMessagesComm + " comments in all.";
            csv_data += "\n" + "There is : " + numberArabicMessagesComm + " comments in Arabic.";
            csv_data += "\n" + "There is : " + numberFrenchMessagesComm + " comments in French.";
            csv_data += "\n" + "There is : " + numberFrenchOrArabicMessagesComm + " comments in Arabic written in French.\n";
            
            
            trierInfluenceurs(influenceurs);
            var numero =1;
            influenceurs[0].forEach(function(influenceur)
            {
                csv_data += "\nInfluenceur n° " + numero + " : " + influenceur + " avec " + influenceurs[1][numero-1] + " posts.";
                numero++;
            });
            
            console.log('data after converting \n', csv_data);
            //console.log(texteDonnees);
            //download a csv file with all data
            //downloadCSV({ filename: "facebook-data.csv", data: csv_data });

            //download a text file with all data
             downloadCSV({ filename: "facebook-data.txt", data: frenchMessage});

            //download the number of messagaes
            //downloadCSV({ filename: "messages-number.txt", data: frenchMessage});
            
            
            
            $('#saveData').prop('disabled', true);
            $('#saveData').removeAttr("disabled");
        }

    };
    //Si l'on clique sur le bouton "See posts location"
    $(document).ready(function(){

        $('#loadPosts').bind('click', function() {
            FB.api('/1008279892518211/feed','GET',{"fields":"message,link,type,created_time,from,place,comments{message,comments{message}}","limit":"250","since":"1 september 2017","until":"30 september 2017","access_token": access_token}, getPosts);
            
        });
    });
    
    //Si l'on clique sur le bouton "Enregistrer les données
    //Alors on utilise de l'ajax pour appeler la page ajax.php
    $(document).ready(function() 
    {
        
        $('#saveData').click(function()
        {
            if($('#saveData').attr('disabled') !== "disabled")
            {
                $.post(
                    'ajax.php', 
                    {
                        Posts : textePosts,
                        Comm : texteComm,
                        Donnee : texteDonnees
                    },
                    'texte'
                );
            }        
        }); 
    });

</script>

<!-- Body -->
<body>

    <script>
        // Note: This example requires that you consent to location sharing when
        // prompted by your browser. If you see the error "The Geolocation service
        // failed.", it means you probably did not give permission for the browser to
        // locate you.

        function  initMap() {
            var tunisia = {lat: 35, lng: 9.2469};
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 7,
                center: tunisia
            });
            //get latitude and longitude of all cities identified

        }

    </script>

    <!--<button id="loadPosts" class ="btn btn-primary">Load Posts</button>-->
    <!--&lt;!&ndash;<a href='#' onclick='downloadCSV({ filename: "stock-data.csv", csv_data: csv_data });' >Download CSV</a>&ndash;&gt;-->
    <!--<div id="map"></div>-->

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
                <a class="navbar-brand"><i class="zmdi zmdi-facebook zmdi-hc-3x" position="fixed" ></i> Data Extraction</a>
            <!--<a class="navbar-brand" href="#"><img src="" height="42" width="62"></a>-->
        </div>

        </div>

    </nav>

    
    <!-- Page Content -->
    <div class="container">
        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer">
            <form method="post" action="#">
                <h1>Welcome to our application</h1>
                <p id="commentaires">Loading posts</p>
                <p>    
                    <a type="submit" class="btn btn-primary btn-large" id="saveData" disabled="disabled">Enregistrer les donnees</a>
                </p>
            </form>
            <p>    
                <a href="#" class="btn btn-primary btn-large" id="loadPosts">See Posts Locations</a>
            </p>
            <div width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" id="map"></div>
        </header>

        <hr>



        <!-- Title -->
        <div class="row">
            <div class="col-lg-12">
                <h3>Type of posts</h3>
            </div>
        </div>


        <!-- Page Features -->
        <div class="row text-center">

            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <div class="caption">
                        <h3>Photos</h3>
                        <p>Number of posts.</p>
                        <p id="imageNumber"></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <div class="caption">
                        <h3>Videos</h3>
                        <p>Number of posts.</p>
                        <p><span id="videoNumber"></span> </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <div class="caption">
                        <h3>Links</h3>
                        <p>Number of posts.</p>
                        <p><span id="LinkNumber"></span> </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <div class="caption">
                        <h3>status</h3>
                        <p>Number of posts.</p>
                        <p ><span id= "statusNumber"></span></p>
                    </div>
                </div>
            </div>

        </div>


        <hr>

    </div>

        <script>
            var imageNumber=0;
            var LinkNumber=0;
            var videoNumber=0;
            var statusNumber=0;
            var csv_data="";

            var places =  new Array();
            var latandLongArray = new Array();
            var counts = {};
            var frenchMessage = "";
            var numberMessagesPosts = 0;
            var numberFrenchMessagesPosts = 0;
            var numberArabicMessagesPosts = 0;
            var numberFrenchOrArabicMessagesPosts = 0;
            var numberMessagesComm = 0;
            var numberFrenchMessagesComm = 0;
            var numberArabicMessagesComm = 0;
            var numberFrenchOrArabicMessagesComm = 0;
            
            var textePosts = new Array();
            var texteComm = new Array(2);
            texteComm[0] = new Array();
            texteComm[1] = new Array();
            var texteDonnees = new Array(4);
            texteDonnees[0] = new Array();
            texteDonnees[1] = new Array();
            texteDonnees[2] = new Array();
            texteDonnees[3] = new Array();
            
            var influenceurs = new Array(2);
            influenceurs[0] = new Array();
            influenceurs[1] = new Array();

            var access_token="EAACEdEose0cBAHg0hSi6sMKuoYe5jp1mXZChZCAELX3S3AQa4NjPSPUjQSbRsZCDEy51ZBHeZCZB3Q9QZCXR7HBGpVNSKr5MI5JP8r63c1dAqvGJaW4gtz3HVd77elK08O02scfdTXx9nEdlEbdsgdvKOT5DsRBZC1wSLWTX1b1k0CJFdSt3zjhFvXohKtq4i7TslgcRZAgG1NwZDZD";
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '287072255125704',
                    xfbml      : true,
                    version    : 'v2.10'
                });
                FB.AppEvents.logPageView();

                FB.getLoginStatus(function(response) {
                    if (response.status === 'connected') {
                        console.log('Logged in.');
                    }
                    else {
                        FB.login();
                    }
                });

            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

        </script>


    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqki2JCXkrRPjtG3WiWvCMscMKk30toOY&callback=initMap">
    </script>

</body>
</html>