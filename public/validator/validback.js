function validateForm() {
    var libelle = document.getElementById('event_type_libelle').value;
    console.log(libelle);
    if(libelle.length===0){
        $('#spanerror').addClass('invalid-feedback d-block');
        $('#event_type_libelle').addClass('is-invalid');
        return false;
    }
    else {
        $('#event_type_libelle').removeClass('is-invalid');
        $('#spanerror').removeClass('invalid-feedback d-block');
        return true;
    }
}
