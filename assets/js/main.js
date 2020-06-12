$(document).ready(function () {
    // Post profille button
    $('#submit_profile_post').click(function () {

        $.ajax({
            type: "POST",
            url:  "includes/handlers/ajax_submit_profile_post.php",
            data: $('form.profile_post').serialize(),
            success: function (msg) {
                $("#post_form").modal('hide');
                location.reload();
            },
            error: function () {
                alert('Echec')
            }
        });
    });

    $("#profiletabs a").on('click', function (event) {
        event.preventDefault()
        $(this).tab('show')
    })


});

function getUser(value,user) {
    $.post("includes/handlers/ajax_friend_search.php", {query:value,userLoggedIn:user}, function (data) {
        $(".results").html(data);
    });
}
