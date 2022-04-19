function validateForm() {
    var libelle = document.getElementById('event_type_libelle').value;
    console.log(libelle);
    if(libelle.length===0){
        $('#spanerror').addClass('invalid-feedback d-block');
        $('#labelerror').addClass('form-error-icon badge badge-danger text-uppercase');
        $('#texterror').addClass('form-error-message');
        $('#texterror').html('Le Champ libelle est obligatoire !');
        $('#labelerror').html('Error');
        $('#event_type_libelle').addClass('is-invalid');
        return false;
    }
    else {
        $('#event_type_libelle').removeClass('is-invalid');
        $('#spanerror').removeClass('invalid-feedback d-block');
        $('#labelerror').removeClass('form-error-icon badge badge-danger text-uppercase');
        $('#texterror').removeClass('form-error-message');
        $('#texterror').html('');
        $('#labelerror').html('');
        return true;
    }
}
