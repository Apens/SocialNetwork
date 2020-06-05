<?php
    include 'includes/header.php';
    include 'includes/classes/User.php';
    include 'includes/classes/Post.php';

    if(isset($_POST['post'])){
        $post= new Post($con, $userLoggedIn);
        $post->submitPost($_POST['post_text'], 'none');
        header("Location:index.php");
    }

?>
        <div class="user_details column">
            <a href="<?= $userLoggedIn ?>"> <img src="<?php echo $user['profile_pic'] ?>"> </a>

            <div class="user_details_left_right">
                <a href="<?= $userLoggedIn ?>">
                <?php
                    echo $user['firstname'] . " " . $user['lastname'];
                ?>
                </a>
                <br>

                    <?php
                    echo "Post: ".$user['num_post']."<br>";
                    echo "Likes: ".$user['num_likes'];
                    ?>

            </div>
        </div> <!-- fin user_details -->
        <div class="main_column column">
            <form action="index.php" method="POST" class="post_form">
                <textarea name="post_text" id="post_text" placeholder="Un truc à partager ?"></textarea>
                <input type="submit" name="post" id="post_button" value="Post">
                <hr>
            </form>



            <div class="posts_area">

            </div>
            <img id="loading" src="assets/images/icons/loading.gif" alt="">


        </div> <!-- fin main_column -->

        <script>
            var userLoggedIn = '<?= $userLoggedIn; ?>';

            $(document).ready(function () {
                $('#loading').show();

                // Requête initiale ajax pour charger les premiers post
                $.ajax({
                   url: "includes/handlers/ajax_load_post.php", //on rensigne l'url qui traitera les données
                   type: "POST", // On renseigne la methode
                   data: "page=1&userLoggedIn=" + userLoggedIn,
                  cache: false,

                success: function (data) {
                    $('#loading').hide();
                    $('.posts_area').html(data) //on envoie les données collectées dans la div

                 }
                });

                $(window).scroll(function () {
                    var height = $('.posts_area').height(); // la div contenant les posts
                    var scroll_top = $(this).scrollTop();
                    var page = $('.posts_area').find('.nextPage').val();
                    var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                    if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight)&& noMorePosts == 'false') {
                        $('#loading').show();

                        var ajaxReq = $.ajax({
                            url: "includes/handlers/ajax_load_post.php", //on rensigne l'url qui traitera les données
                            type: "POST", // On renseigne la methode
                            data: "page="+ page +"&userLoggedIn=" + userLoggedIn, //REQUEST d'ajax
                            cache: false,

                            success: function (response) {
                                $('.posts_area').find('.nextPage').remove();
                                $('.posts_area').find('.noMorePosts').remove();

                                $('#loading').hide();
                                $('.posts_area').append(response) //on ajoute d'autres données collectées dans la div

                            }
                        });

                    } // Fin IF
                    return false;

                }); // Fin (window).scroll function

            });
        </script>


    </div> <!-- wrapper -->

</body>
</html>