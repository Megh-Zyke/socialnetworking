<div class="modal-overlay" id="modalOverlay"></div>

<div class="addPostsPage">
    <div class="close">
        <i class="fas fa-times" onclick="closePage()"></i>
    </div>

    <form action="post.php" method="post" enctype="multipart/form-data">
        <div class="addPostsHeading">
            <h1> Add Post </h1>
        </div>

        <div id="postForm">
            <input type="hidden" value=<?php echo $current_user_id; ?> name="userID">
            <textarea name="postText" id="postText" placeholder="What's on your mind?"></textarea>

            <div class="image_preview_Div">
                <img id="imagePreview" alt="Image Preview" />
            </div>

            <div>
            <input type="file" name="mediaButton" id="mediaButton" accept="image/*" class="custom-file-button" />

            </div>
            <button class="submitPost" type="submit">Post</button>
        </div>
    </form>
</div>