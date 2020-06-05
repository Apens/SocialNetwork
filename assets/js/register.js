$(document).ready(function() {
    //On click signup, cache le login et devoile l'enregistrement
    $("#signup").click(function() {
        $("#first").slideUp("slow", function() {
            $("#second").slideDown("slow");
        });
    });

    //On click signin, cache le l'enregistrement et devoile le login
    $("#signin").click(function() {
        $("#second").slideUp("slow", function() {
            $("#first").slideDown("slow")
        });
    });

});