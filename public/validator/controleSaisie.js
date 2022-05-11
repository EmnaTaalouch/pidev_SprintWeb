function notif(text,color)
{
    var x=document.getElementById("toast");
    x.classList.add("show");
    x.innerHTML=text;
    x.style.backgroundColor=color;
    setTimeout(function(){
        x.classList.remove("show");
    },3000);
}
function validator() {
    var nom_event = document.getElementById("nom_event").value;
    var theme_event = document.getElementById("theme_event").value;
    var date_debut = document.getElementById("date_debut").value;
    var date_fin = document.getElementById("date_fin").value;
    var nbr_participants = document.getElementById("nbr_participants").value;
    var lieu = document.getElementById("lieu").value;
    var description = document.getElementById("description").value;
    var event_type = document.getElementById("event_type").value;
    var datedebut = new Date(document.getElementById("date_debut").value);
    var datefin = new Date(document.getElementById("date_fin").value);
    var currentdate = new Date();
     if(nom_event.length===0){
        notif("Veuillez verifier votre nom !!", "red");
        return false;
    }else if(theme_event.length===0){
        notif("Veuillez verifier votre theme !!", "red");
        return false;
    }else if(date_debut.length===0){
        notif("Veuillez verifier votre date debut !!", "red");
        return false;
    }else if(date_fin.length===0){
        notif("Veuillez verifier votre date fin !!", "red");
        return false;
    }else if(nbr_participants.length===0){
        notif("Veuillez verifier votre nombre de participants !!", "red");
        return false;
    }else if(lieu.length===0){
        notif("Veuillez verifier votre lieu !!", "red");
        return false;
    }else if(description.length===0){
        notif("Veuillez verifier votre description !!", "red");
        return false;
    }else if(event_type.length===0){
        notif("Veuillez verifier votre type event !!", "red");
        return false;
    }else if(currentdate > datedebut){
        notif("la date debut doit etre superieur a la date actuelle !!", "red");
        return false;
    }else if(datedebut > datefin){
        notif("la date fin doit etre superieur a la date debut !!", "red");
        return false;
    }
    else {
        notif("Formulaire est validée !!","green");
        return true;
    }
}
